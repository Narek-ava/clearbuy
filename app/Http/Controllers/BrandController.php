<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandContact;
use App\Models\Country;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Brand as Requests;
use App\Http\Helpers\CSVExport;

class BrandController extends BaseItemController
{
    protected $baseUrl = '/admin/brands';

    public function list(Requests\ListRequest $request)
    {
        $items = Brand::orderByColumn($request->sort, $request->order);

        if ($request->name) {
            $items->where('name', 'LIKE', "%$request->name%");
        }

        if ($request->website) {
            $items->where('website', 'LIKE', "%$request->website%");
        }

        if ($request->countries && count($request->countries) > 0) {
            $items->whereHas('country', function($q) use ($request) {
                $q->whereIn('id', $request->countries);
            });
        }

        $listData = $this->getListData($request);
        $paginator = $items->paginate($request->perPage);

        //for item's logo show
        $updatedItems = $paginator->getCollection();
        $updatedItems->transform(function ($value) {

            if(!is_null($value->image)){
                $value->image = $value->getLogoUrl($value->image);
            }
            return $value;
        });
        $listData['items'] = $paginator->setCollection($updatedItems);
        $listData['countries'] = Country::all();

        return view('brand.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = Brand::find($request->id);
        $formData['countries'] = Country::all();

        return view('brand.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $brand = Brand::firstOrNew(['id' => $request->id]);
        $brand->name = $request->name;
        $brand->website = $request->website;
        $brand->bio = $request->bio;
        $brand->image = $request->images[0] ?? null;

        try {
            $country = Country::findOrFail($request->country_id);
            $brand->country()->associate($country);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return back()->withErrors([
                'country' => 'Selected country does not exost'
            ])->withInput();
        }

        $brand->save();

        $brand->contacts()->delete();

        if ($request->contacts) {
            foreach ($request->contacts as $contact) {
                $brand->contacts()->save(new BrandContact($contact));
            }
        }

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/brand?id='.$brand->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function export()
    {
        $exporter = new CSVExport(new Brand());
        $exporter->export('brands');
        return redirect()->back();
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            Brand::whereIn('id', $request->items)->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->withErrors([
                'delete' => 'Unable to delete. Selected items are used in Apps or Products objects.'
            ])->withInput();
        }
        return back()->with([
            'status' => 'success',
            'message' => 'deleted successfully'
        ]);
    }
}
