<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthDomains as Requests;
use Illuminate\Support\Facades\DB;

class AllowedAuthDomainsController extends BaseItemController
{
    protected $baseUrl = '/admin/auth_domains';

    public function list(Requests\ListRequest $request)
    {
        $items = DB::table('auth_domains')
                ->orderBy($request->sort, $request->order)
                ->get();

        if ($request->domain) {
            $items->where('domain', 'LIKE', "%$request->domain%");
        }

        $listData = $this->getListData($request);
        //$listData['items'] = $items->paginate($request->perPage);
        $listData['items'] = $items;

        return view('auth_domains.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = DB::table('auth_domains')->where('id', '=', $request->id)->first();

        return view('auth_domains.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        DB::table('auth_domains')->updateOrInsert(
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

            return redirect('/admin/auth_domain?id='.$last_id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            DB::table('auth_domains')->whereIn('id', $request->items)->delete();
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
