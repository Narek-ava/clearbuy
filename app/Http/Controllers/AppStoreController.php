<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppStore as Requests;
use App\Models\AppStore;
use App\Models\Brand;

class AppStoreController extends BaseItemController
{
    protected $baseUrl = '/admin/app_stores';

    public function list(Requests\ListRequest $request)
    {
        $items = AppStore::orderBy($request->sort, $request->order);

        //filtering from UI
        if ($request->name) {
            $items->where('name', 'LIKE', "%$request->name%");
        }

        if ($request->url) {
            $items->where('url', 'LIKE', "%$request->url%");
        }

        if ($request->brand_id !== null) {
            $items->where('brand_id', '=', $request->brand_id);
        }

        $listData = $this->getListData($request);
        $paginator = $items->paginate($request->perPage);

        //for item's logo show
        $updatedItems = $paginator->getCollection();

        $listData['items'] = $paginator->setCollection($updatedItems);

        return view('app_store.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = AppStore::find($request->id);
        $formData['brands'] = Brand::all();

        return view('app_store.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $item = AppStore::firstOrNew(['id' => $request->id]);
        $item->name = $request->name;
        $item->url = $request->url;
        $item->brand_id = $request->brand;
        $item->icon = $request->icon ?? null;
        $item->save();

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/app_store?id='.$item->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            AppStore::whereKey($request->items)->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->withErrors([
                'delete' => 'Unable to delete. Selected items are used in other objects.'
            ])->withInput();
        }
        return redirect($request->backUrl)->with([
            'status' => 'success',
            'message' => 'deleted successfully'
        ]);
    }
}
