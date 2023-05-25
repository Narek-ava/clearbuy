<?php

namespace App\Http\Controllers;

use App\Models\Country;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Country as Requests;
use App\Http\Helpers\CSVExport;

class CountryController extends baseItemController
{
    protected $baseUrl = '/admin/countries';

    public function list(Requests\ListRequest $request)
    {
        $items = Country::orderBy($request->sort, $request->order);

        if ($request->name) {
            $items->where('name', 'LIKE', "%$request->name%");
        }

        $listData = $this->getListData($request);
        $listData['items'] = $items->paginate($request->perPage);

        return view('country.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = Country::find($request->id);

        return view('country.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $country = Country::firstOrNew(['id' => $request->id]);
        $country->name = $request->name;
        $country->save();

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/country?id='.$country->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function export()
    {
        $exporter = new CSVExport(new Country());
        $exporter->export('countries');
        return redirect()->back();
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            Country::whereIn('id', $request->items)->delete();
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
