<?php

namespace App\Http\Controllers;

use App\Http\Requests\AllowedDomains as Requests;
use Illuminate\Support\Facades\DB;

class AllowedDomainsController extends BaseItemController
{
    protected $baseUrl = '/admin/domains';

    public function list(Requests\ListRequest $request)
    {
        $items = DB::table('allowed_domains')
                ->orderBy($request->sort, $request->order)
                ->get();

        if ($request->domain) {
            $items->where('domain', 'LIKE', "%$request->domain%");
        }

        $listData = $this->getListData($request);
        //$listData['items'] = $items->paginate($request->perPage);
        $listData['items'] = $items;

        return view('domains.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = DB::table('allowed_domains')->where('id', '=', $request->id)->first();

        return view('domains.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        DB::table('allowed_domains')->updateOrInsert(
            ['id' => $request->id],
            ['domain' => $request->domain]
        );

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            $last_id = DB::getPdo()->lastInsertId();

            return redirect('/admin/domain?id='.$last_id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            DB::table('allowed_domains')->whereIn('id', $request->items)->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->withErrors([
                'delete' => 'Unable to delete. Selected items are used in other objects.'
            ])->withInput();
        }

        return back()->with([
            'status' => 'success',
            'message' => 'deleted successfully'
        ]);
    }
}
