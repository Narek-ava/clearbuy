<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductDealPrice;

use App\Http\Requests\Deal as Requests;
use App\Events\ProductRetailerAdded;
use App\Services\ZapierService;

class DealController extends BaseItemController
{
    protected $baseUrl = '/admin/deals';

    public function list(Requests\ListRequest $request)
    {
        $items = ProductDealPrice::orderByColumn($request->sort, $request->order);

        if ($request->session()->exists('showState')) {
            if($request->session()->get('showState')) { //if active show checked
                $items->whereDate('expiry_date', '>=', \date('Y-m-d'));
            }
        }

        if ($request->search) {
            $items->where('product.name', 'LIKE', '%' . $request->search . '%')
                ->orWhere('agent.name', 'LIKE', '%' . $request->search . '%')
                ->orWhere('created_at', 'LIKE', '%' . $request->search . '%')
                ->orWhere('expiry_date', 'LIKE', '%' . $request->search . '%');
        }

        $listData = $this->getListData($request);

        $listData['items'] = $items->paginate($request->perPage);

        return view('deal.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);

        if ($request->copy_id) {
            $formData['item'] = ProductDealPrice::findOrFail($request->copy_id);
            $formData['is_copy'] = true;
        } else {
            $formData['item'] = ProductDealPrice::find($request->id);
        }

        $formData['products'] = Product::all();
        $formData['agents'] = Agent::all();
        $formData['currencies'] = Currency::all();

        return view('deal.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {

        $data = $request->except(['_token','backUrl','copy_id', 'search_terms']);
        $data['expiry_notification'] = false;
        $data['recommended'] = $request->recommended ?? false;
        $data['is_hot'] = (bool) $request->is_hot;
        $product = Product::find($request->product_id);
        $productIsPending = $product->is_pending;

        $deal = ProductDealPrice::updateOrCreate(
            ['id' => $request->id],
            $data
        );
        
        if($request->id) {
            ZapierService::dealUpdated($deal->id);
        } else {
            ZapierService::dealCreated($deal->id);
        }

        if($productIsPending) ZapierService::productMovesFromPendingToNormal($product->id);

        if($request->id) {
            ZapierService::dealUpdated($deal->id);
        } else {
            ZapierService::dealCreated($deal->id);
        }

        if($productIsPending) ZapierService::productMovesFromPendingToNormal($product->id);

        $submitter_id = Product::where('id', $request->product_id)->value('submitter_id');

        if(!is_null($submitter_id))
        {
            try{

                ProductRetailerAdded::dispatch($product); //send email to request submitter

            }catch(\Exception $e){

                return redirect($request->backUrl)->with([
                    'status' => 'success',
                    'message' => 'saved successfully but '.$e->getMessage()
                ]);
            }
        }

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/deal?id='.$deal->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            ProductDealPrice::whereIn('id', $request->items)->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            if($request->ajax()){
               return $request->session()->flash('message', 'Unable to delete. Selected items are used in other objects.');
            }
            return back()->withErrors([
                'delete' => 'Unable to delete. Selected items are used in other objects.'
            ])->withInput();
        }
        if($request->ajax()){
            $request->session()->flash('status', 'success');
           return $request->session()->flash('message', 'deleted successfully');
        }
        return back()->with([
            'status' => 'success',
            'message' => 'deleted successfully'
        ]);
    }
}
