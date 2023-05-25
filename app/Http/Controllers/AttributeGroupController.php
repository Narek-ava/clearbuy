<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Models\Category;
use App\Models\ProductAttributeValue;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Log;

use App\Http\Requests\AttributeGroup as Requests;

class AttributeGroupController extends baseItemController
{
    protected $baseUrl = '/admin/attribute_groups';

    public function list(Requests\ListRequest $request)
    {
        $items = AttributeGroup::whereNull('product_id')->orderBy($request->sort, $request->order);

        if ($request->name) {
            $items->where('name', 'LIKE', "%$request->name%");
        }

        $listData = $this->getListData($request);
        $listData['items'] = $items->paginate($request->perPage);

        return view('attribute_group.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = AttributeGroup::find($request->id);

        return view('attribute_group.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $item = AttributeGroup::firstOrNew(['id' => $request->id]);
        $item->name = $request->name;
        $item->sort_order = $request->sort_order;
        $item->repeatable = $request->repeatable ?? 0;

        $item->save();


        //get all attributes assigned to the group
        $attributes = $item->attributes;
        $attrTypes = Attribute::types()->flip();

        if ($attributes->isNotEmpty()) {

            foreach ($attributes as $attr) {

                if ($attr->products()->where('attribute_id', $attr->id)->exists()) //exists in attribute_to_product
                {
                    //get all products existing in attribute_to_product
                    $products = $attr->products()->where('attribute_id', $attr->id)->get();

                    foreach ($products as $product) {

                        //category might not be assigned to product
                        if (!is_null($product->category_id)) {

                            if (!is_null($request->repeatable)) //repeatable group
                            {
                                //create personal product attribute group/set
                                //clone attributes from original
                                //assign them to product category
                                //copy original values if they does not exists in clones
                                try {

                                    DB::beginTransaction();

                                    $new_group = AttributeGroup::firstOrCreate([
                                        'name' => $request->name,
                                        'product_id' => $product->id,
                                        'parent_id' => $request->id
                                    ]);

                                    $category = Category::findOrFail($product->category_id);

                                    $attribute_clone = Attribute::firstOrCreate(
                                        ['name' => $attr->name, 'attribute_group_id' => $new_group->id],
                                        [
                                            'type' => $attr->type,
                                            'kind' => $attr->kind,
                                            'measure_id' => $attr->measure_id,
                                            'sort_order' => $attr->sort_order,
                                            'parent_id' => $attr->id
                                        ]
                                    );

                                    $category->attributes()->syncWithoutDetaching([$attr->id, $attribute_clone->id]); //assign attributes to category

                                    if (is_null($attribute_clone->valueForProduct($product->id))) { //if value is not set earlier

                                        $original_value = $attr->valueForProduct($product->id);

                                        if (!is_null($original_value)) {

                                            if ($attribute_clone->type != 5) { //assign attribute to product except multiple attributes

                                                $product->attributes()->syncWithoutDetaching([
                                                    $attribute_clone->id => [
                                                        'attribute_option_id' => $attribute_clone->type == $attrTypes->get('single option') ? $original_value->id : null,
                                                        'value_numeric' => ($attribute_clone->type == $attrTypes->get('numeric') OR $attr->type == $attrTypes->get('decimal')) ? $original_value : null,
                                                        'value_text' => $attribute_clone->type == $attrTypes->get('string') ? $original_value : null,
                                                        'value_boolean' => $attribute_clone->type == $attrTypes->get('boolean') ? $original_value : null,
                                                        'value_date' => $attribute_clone->type == $attrTypes->get('datetime') ? $original_value : null
                                                    ]
                                                ]);

                                            } else { //assign multiple attribute to product

                                                //get all models from attribute_to_product
                                                $original_values = ProductAttributeValue::where('product_id', $product->id)
                                                                                        ->where('attribute_id', $attr->id)
                                                                                        ->get();


                                                if ($original_values->isNotEmpty()) {
                                                    foreach ($original_values as $item) {
                                                        $clone_value = $item->replicate()->fill([
                                                            'attribute_id' => $attribute_clone->id,
                                                            'attribute_option_id' => $item->attribute_option_id,
                                                        ]);

                                                        $clone_value->save();
                                                    }
                                                }

                                            }

                                        }
                                    }

                                    DB::commit();

                                } catch (Exception $e) {
                                    DB::rollBack();
                                }

                            } else { //Could have changed from repeatable to non-repeatable

                                $category = Category::find($product->category_id);
                                $category->attributes()->syncWithoutDetaching([$attr->id]);
                            }
                        }
                    }

                }

            }
        }

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/attribute_group?id='.$item->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function delete(Requests\DeleteRequest $request)
    {

        try {
            AttributeGroup::whereIn('id', $request->items)->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            if ($request->ajax()) {
                return $request->session()->flash('message', 'Unable to delete. Selected items are used in other objects.');
            }
            return back()->withErrors([
                'delete' => 'Unable to delete. Selected items are used in other objects.'
            ])->withInput();
        }
        if ($request->ajax()) {
            $request->session()->flash('status', 'success');
            return $request->session()->flash('message', 'deleted successfully');
        }
        return back()->with([
            'status' => 'success',
            'message' => 'deleted successfully'
        ]);
    }
}
