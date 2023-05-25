<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\AppImage;
use App\Models\AppLink;
use App\Models\OS;
use App\Models\Country;
use App\Models\Brand;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\App as Requests;
use App\Http\Helpers\CSVExport;
use App\Models\Currency;
use Illuminate\Support\Facades\Validator;

class AppController extends BaseItemController
{
    protected $baseUrl = '/admin/apps';

    public function list(Requests\ListRequest $request)
    {
        $items = App::orderBy($request->sort, $request->order)->with(['os', 'brand', 'countries', 'links']);

        if ($request->name) {
            $items->where('app.name', 'LIKE', '%'.$request->name.'%');
        }

        if ($request->type !== null) {
            $items->where('type_id', '=', $request->type);
        }

        if ($request->price_from) {
            $items->where('price', '>=', $request->price_from);
        }

        if ($request->price_to) {
            $items->where('price', '<=', $request->price_to);
        }

        if ($request->brand) {
            $items->whereHas('brand', function($q) use ($request) {
                $q->where('brand.name', 'LIKE', '%'.$request->brand.'%');
            });
        }

        $listData = $this->getListData($request);
        $paginator = $items->paginate($request->perPage);

        //for item's logo show
        $updatedItems = $paginator->getCollection();
        $updatedItems->transform(function ($value) {

            if(!is_null($value->logo)){
                $value->logo = $value->getLogoUrl($value->logo);
            }
            return $value;
        });
        $listData['items'] = $paginator->setCollection($updatedItems);

        $listData['types'] = App::types();

        return view('app.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = App::find($request->id);
        $formData['types'] = App::types();
        $formData['brands'] = Brand::all();
        $formData['currencies'] = Currency::all();

        return view('app.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $item = App::firstOrNew(['id' => $request->id]);

        // general
        $item->name = $request->name;
        $item->change_log_url = $request->change_log_url;
        $item->type_id = $request->type;
        $item->logo = $request->logo;
        $item->video_url = $request->video_url;
        $item->description = $request->description;
        $item->save();

        // images
        $item->images()->delete();

        if ($request->images) {
            foreach ($request->images as $order => $path) {
                $item->images()->save(new AppImage(['path' => $path, 'order' => $order]));
            }
        }

        // relations
        try {
            $brand = Brand::findOrFail($request->brand);
            $item->brand()->associate($brand);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return back()->withErrors([
                'brand' => 'Selected brand does not exist'
            ])->withInput();
        }

        $item->os()->detach();
        if ($request->os) {
            $osList = OS::whereKey($request->os)->get();
            foreach ($osList as $os) {
                $item->os()->attach($os);
            }
        }

        $item->countries()->detach();
        if ($request->countries) {
            $countries = Country::whereKey($request->countries)->get();
            foreach ($countries as $country) {
                $item->countries()->attach($country);
            }
        }

        // links
        $item->links()->delete();
        if (is_array($request->links)) {
            foreach ($request->links as $linkArr) {
                $link = (object)$linkArr;
                $item->links()->save(new AppLink([
                    'store_id' => $link->store_id,
                    'free' => $link->free ?? false,
                    'app_purchase' => $link->app_purchase ?? false,
                    'price' => $link->price ?? 0,
                    'currency_id' => $link->currency_id ?? null,
                    'url' => $link->url,
                ]));
            }
        }

        $item->save();

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/app?id='.$item->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }


    public function export()
    {
        $exporter = new CSVExport(new App);
        $exporter->merge(['images','os', 'countries'])->export('apps');
        return redirect()->back();
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            App::whereKey($request->items)->delete();
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


    // public function getApp(Request $request) {
    //
    //     $request->merge([
    //         'app_ids' => explode(',', $request->apps_ids),
    //         'stores' => explode(',', $request->stores),
    //     ]);
    //
    //     $validator = Validator::make($request->all(), [
    //         'app_ids' => 'required|array',
    //         'app_ids.*' => 'numeric',
    //         'stores' => 'sometimes|nullable|array',
    //         'stores.*' => 'numeric'
    //     ]);
    //
    //
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'error' => true,
    //             'server' => $validator->getMessageBag()->toArray()
    //         ], 400); // 400 being the HTTP code for an invalid request.
    //     }
    //
    //
    //
    //     $app_ids = $request->app_ids;
    //
    //    $stores = $request->stores[0] !== '' ? $request->stores :  [];
    //
    //
    //     try {
    //         $allData = [];
    //
    //         $apps = App::whereIn('id', $app_ids)->get();
    //
    //         foreach ($apps as $app) {
    //
    //             $images = $app->getImages();
    //
    //             $data = [
    //                 'name' => $app->name,
    //                 'description' => $app->description,
    //                 'images' => $images,
    //                 'video_url' => $app->video_url
    //             ];
    //
    //             foreach ($app->links as $link) {
    //
    //                 if(empty($stores)){
    //
    //                     $s = [
    //                         'store_name' => $link->store->name ,
    //                         'store_url' => $link->url,
    //                         'icon' => $link->store->icon ,
    //                         'free' => (bool)$link->free,
    //                         'app_purchase' => (bool)$link->app_purchase,
    //                     ];
    //
    //                     if(!$link->free && !$link->app_purchase) {
    //                         $s['price'] = $link->price;
    //                         $s['currency'] = $link->currency->symbol;
    //                     }
    //                     $data['links'][] = $s;
    //
    //                 }else if(in_array($link->store_id, $stores)){
    //                     $s = [
    //                         'store_name' => $link->store->name ,
    //                         'store_url' => $link->url,
    //                         'icon' => $link->store->icon ,
    //                         'free' => (bool) $link->free,
    //                         'app_purchase' => (bool) $link->app_purchase,
    //                     ];
    //
    //                     if(!$link->free && !$link->app_purchase) {
    //                         $s['price'] = $link->price;
    //                         $s['currency'] = $link->currency->symbol;
    //                     }
    //
    //                     $data['links'][] = $s;
    //                 }
    //             }
    //
    //             $allData[] = $data;
    //         }
    //
    //         return response()->json($allData);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => true,
    //             'message' => $e->getMessage(),
    //             'server' => "File: {$e->getFile()}, Line: {$e->getLine()}",
    //         ]);
    //     }
    //
    // }


}
