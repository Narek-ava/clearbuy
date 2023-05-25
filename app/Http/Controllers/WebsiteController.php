<?php

namespace App\Http\Controllers;

use App\Models\Website;

use App\Http\Helpers\CSVExport;
use App\Http\Requests\Website as Requests;

class WebsiteController extends BaseItemController
{
    protected $baseUrl = '/admin/websites';

    public function list(Requests\ListRequest $request)
    {
        $items = Website::orderBy($request->sort, $request->order);

        if ($request->name) {
            $items->where('name', 'LIKE', "%$request->name%");
        }

        if ($request->url) {
            $items->where('url', 'LIKE', "%$request->url%");
        }

        if ($request->description) {
            $items->where('description', 'LIKE', "%$request->description%");
        }

        $listData = $this->getListData($request);
        $listData['items'] = $items->paginate($request->perPage);

        return view('website.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = Website::find($request->id);

        return view('website.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $item = Website::firstOrNew(['id' => $request->id]);
        $item->name = $request->name;
        $item->url = $request->url;
        $item->description = $request->description;
        $item->logo = $request->logo ?? null;

        $item->save();

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/website?id='.$item->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function export()
    {
        $exporter = new CSVExport(new Website);
        $exporter->export('websites');
        return redirect()->back();
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            Website::whereIn('id', $request->items)->delete();
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
