<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\AttributeGroup;
use App\Models\Measure;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Attribute as Requests;

class AttributeController extends BaseItemController
{
    protected $baseUrl = '/admin/attributes';

    public function list(Requests\ListRequest $request)
    {
        $items = Attribute::orderByColumn($request->sort, $request->order);

        /*
        *   Search conditions
        */
        if ($request->name) {
            $items->where('name', 'LIKE', "%$request->name%");
        }

        if ($request->type !== null) {
            $items->where('type', '=', $request->type);
        }

        if ($request->kind !== null) {
            $items->where('kind', '=', $request->kind);
        }

        if ($request->measures && count($request->measures) > 0) {
            $items->whereHas('measure', function($q) use ($request) {
                $q->whereIn('id', $request->measures);
            });
        }

        if ($request->option) {
            $items->whereHas('option', function($q) use ($request) {
                $q->where('name', 'LIKE', "%$request->option%");
            });
        }

        if ($request->group_name) {
            $items->whereHas('group', function($q) use ($request) {
                $q->where('name', 'LIKE', "%$request->group_name%");
            });
        }
        /*
        *   End search
        */

        //hide personal product attributes
        $items->whereHas('group', function($q) use ($request) {
            $q->whereNull('product_id');
        });

        $listData = $this->getListData($request);
        $listData['items'] = $items->paginate($request->perPage);
        $listData['measures'] = Measure::all();
        $listData['types'] = Attribute::types();
        $listData['kinds'] = Attribute::kindsList();

        return view('attribute.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = Attribute::find($request->id);
        $formData['measures'] = Measure::select(['id', 'name'])->get();
        $formData['types'] = Attribute::types();
        $formData['kinds'] = Attribute::kindsList();
        //$formData['groups'] = AttributeGroup::whereNull('product_id')->orderBy('sort_order')->get();
        $formData['groups'] = AttributeGroup::orderBy('sort_order')->get();

        return view('attribute.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $item = Attribute::firstOrNew(['id' => $request->id]);
        $item->name = $request->name;
        $item->sort_order = $request->sort_order;
        $item->kind = $request->kind;

        try {
            $group = AttributeGroup::findOrFail($request->group);
            $item->group()->associate($group);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return back()->withErrors([
                'group' => 'Selected attribute group does not exist'
            ])->withInput();
        }

        // if ($item->products()->count() > 0 && $item->type != $request->type) {
        //     /*
        //      * If attribute is used by at least one product
        //      * type change is not allowed
        //      */
        //      return back()->withErrors([
        //          'update_type' => "Can't change type. This attribute is already used by some products."
        //      ])->withInput();
        //
        // } else $item->type = $request->type;

        $item->type = $request->type;

        $measure = Measure::find($request->measure);
        $item->measure()->associate($measure);

        //$item->save();

        $this->saveSingleOrMultipleOptions($item, $request);

        $item->save();

        if ($request->id) { //existing item

            //apply inheritance to child attributes (may exists in repeatable groups)
            $child_attributes = $item->children()->get();

            if($child_attributes->isNotEmpty()) {

                foreach ($child_attributes as $child) {
                    $child->name = $request->name;
                    $child->sort_order = $request->sort_order;
                    $child->kind = $request->kind;
                    $child->type = $request->type;
                    $child->measure()->associate($measure);

                    $this->saveSingleOrMultipleOptions($child, $request);

                    $child->save();
                }
            }

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/attribute?id='.$item->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function saveSingleOrMultipleOptions($item, $request) {

        if ($item->type == 4 OR $item->type == 5) { //single or multiple
            /*
             * Getting ids of options, that exist for this attribute
             * but are not present in the request data. They are
             * candidates for deletion
             */

            $option_ids = $item->options->map(function($item) {
                return $item->id;
            });

            if ($option_ids->count() > 0) { //existing options

                //return items that are missing in request options for deleting
                $option_ids = $option_ids->diff(collect($request->options)->map(function($item) {
                    return $item['id'];
                }));

                try {
                    AttributeOption::whereIn('id', $option_ids)->delete();
                } catch (\Illuminate\Database\QueryException $ex) {
                    return back()->withErrors([
                        'delete' => "Unable to delete options. They are already used in some products."
                    ])->withInput();
                }
            }

            foreach ($request->options as $option) {
                $attributeOption = AttributeOption::firstOrNew(['id' => $option['id']]);
                $attributeOption->name = $option['name'];
                $item->options()->save($attributeOption);
            }
        }


        $item->save();

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/attribute?id='.$item->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }

    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            Attribute::whereIn('id', $request->items)->delete();
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
