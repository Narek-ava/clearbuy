<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Country;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Currency as Requests;
use App\Http\Helpers\CSVExport;

class CurrencyController extends BaseItemController {

    protected $baseUrl = '/admin/currencies';

    public function list(Requests\ListRequest $request) {
        $items = Currency::orderByColumn($request->sort, $request->order);

        if ($request->name) {
            $items->where('name', 'LIKE', "%$request->name%");
        }

        if ($request->symbol) {
            $items->where('symbol', 'LIKE', "%$request->symbol%");
        }

        if ($request->countries && count($request->countries) > 0) {
            $items->whereHas('country', function ($q) use ($request) {
                $q->whereIn('id', $request->countries);
            });
        }

        $listData = $this->getListData($request);
        $listData['items'] = $items->paginate($request->perPage);
        $listData['countries'] = Country::all();

        return view('currency.list', $listData);
    }

    public function form(Requests\GetFormRequest $request) {
        $formData = $this->getFormData($request);
        $formData['item'] = Currency::find($request->id);
        $formData['countries'] = Country::all();

        return view('currency.form', $formData);
    }

    public function save(Requests\SaveRequest $request) {
        $currency = Currency::firstOrNew(['id' => $request->id]);
        $currency->name = $request->name;
        $currency->symbol = $request->symbol;

        $countries = implode(',', $request->country_ids);
        $currency->country_ids = $countries;

        $currency->save();

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/currency?id='.$currency->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function export() {
        $exporter = new CSVExport(new Currency());
        $exporter->export('Currencies');
        return redirect()->back();
    }

    public function delete(Requests\DeleteRequest $request) {
        try {
            Currency::whereIn('id', $request->items)->delete();
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
