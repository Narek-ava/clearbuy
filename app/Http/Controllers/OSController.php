<?php

namespace App\Http\Controllers;

use App\Models\OS;
use App\Models\Category;
use App\Models\Brand;
use App\Models\LicenseType;
use App\Models\OSRelease;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\OS as Requests;

class OSController extends BaseItemController
{
    protected $baseUrl = '/admin/oss';

    public function list(Requests\ListRequest $request)
    {
        $items = OS::orderByColumn($request->sort, $request->order)->whereDoesntHave('parent')->with(['categories', 'brand', 'licenseType', 'children']);

        $listData = $this->getListData($request);
        $listData['items'] = $items->get();

        return view('os.tree', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);

        $formData['item'] = OS::find($request->id);
        $formData['brands'] = Brand::all();
        $formData['licenses'] = LicenseType::all();
        $formData['categories'] = Category::listWithFullPath();

        if ($formData['item']) {
            $formData['parents'] = OS::where('id', '<>', $formData['item']->id)->get();
        } else {
            $formData['parents'] = OS::all();
        }

        return view('os.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $item = OS::firstOrNew(['id' => $request->id]);

        //general
        $item->name = $request->name;
        $item->description = $request->description;
        $item->change_log_url = $request->change_log_url;
        $item->is_kernel = !!$request->is_kernel;
        $item->image = $request->image;

        $item->save();

        //relations
        try {
            $license = LicenseType::findOrFail($request->license);
            $item->licenseType()->associate($license);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return back()->withErrors([
                'category' => 'Selected license does not exist'
            ])->withInput();
        }

        try {
            $brand = Brand::findOrFail($request->brand);
            $item->brand()->associate($brand);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return back()->withErrors([
                'brand' => 'Selected brand does not exist'
            ])->withInput();
        }

        $item->categories()->detach();
        if ($request->categories) {
            $categories = Category::whereIn('id', $request->categories)->get();
            foreach ($categories as $category) {
                $item->categories()->attach($category);
            }
        }

        if ($item->releases->count() > 0) {
            $preservedReleases = [];
            if (isset($request->releases) && count($request->releases) > 0) {
                $preservedReleases = collect($request->releases)->map(function($item) {
                    if (isset($item['id'])) {
                        return $item['id'];
                    } else {
                        return null;
                    }
                })->filter(function($item) {
                    return $item !== null;
                })->toArray();
            }
            try {
                $item->releases()->whereNotIn('id', $preservedReleases)->delete();
            } catch (\Illuminate\Database\QueryException $ex) {
                return back()->withErrors([
                    'delete' => "Unable to delete releases. They are already used in other objects."
                ])->withInput();
            }
        }

        if (isset($request->releases) && count($request->releases) > 0) {
            foreach ($request->releases as $releaseArr) {
                if (isset($releaseArr['id'])) {
                    $release = OSRelease::firstOrNew(['id' => $releaseArr['id']]);
                } else {
                    $release = new OSRelease();
                }
                $release->version = $releaseArr['version'];
                $release->release_date = $releaseArr['release_date'];
                if (isset($releaseArr['added_features'])) {
                    $release->added_features = $releaseArr['added_features'];
                }
                $item->releases()->save($release);
            }
        }

        $item->parent()->dissociate();
        $item->parentOSRelease()->dissociate();
        $parent = OS::find($request->parent);
        if ($parent) {
            $item->parent()->associate($parent);
            $release = $parent->releases()->find($request->parent_os_release);
            if (!$release) {
                return back()->withErrors([
                    'parent_os_release' => 'Selected parebt OS release not found'
                ])->withInput();
            }
            $item->parentOSRelease()->associate($release);
        }

        $item->save();

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/os?id='.$item->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            OS::whereIn('id', $request->items)->delete();
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
