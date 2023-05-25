<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Attribute;
use App\Models\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use App\Http\Requests\Category as Requests;
use App\Http\Helpers\CSVExport;
use App\Services\SidebarLinksService;

use Illuminate\Support\Facades\Log;

class CategoryController extends BaseItemController
{
    protected $baseUrl = '/admin/categories';

    public function list(Requests\ListRequest $request)
    {
        $items = Category::doesntHave('parent')->orderBy($request->sort, $request->order);

        if ($request->name) {
            $items->where('name', 'LIKE', "%$request->name%");
        }

        $listData = $this->getListData($request);
        $listData['items'] = $items->paginate($request->perPage);

        return view('category.tree', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = Category::find($request->id);
        $formData['categories'] = Category::listWithFullPath($request->id ? $request->id : 0);
        $formData['attributes'] = Attribute::all();
        $formData['parent'] = $request->parent;

        return view('category.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $item = Category::firstOrNew(['id' => $request->id]);
        $item->name = $request->name;
        $item->save();
        //$item->refresh();

        $item->parent()->dissociate();
        $parent = Category::find($request->parent);
        if ($parent) {
            $item->parent()->associate($parent);
        }

        //get category attributes with product_id (bcs they absent in category edit)
        $personal_attributes = $item->attributes()->whereHas('group', function(Builder $query){
                $query->whereNotNull('product_id');
        })->pluck('id')->toArray();

        $all_category_attributes = array_map('intval', array_merge($request->attribute_ids ?? [], $personal_attributes ?? []));

        $flipped_attrs = array_flip($all_category_attributes); //reverse array

        foreach($flipped_attrs as $key => $attr_id) {

            $flipped_attrs[$key] = ['featured' => false]; //default

            if(!is_null($request->featured_attributes)) {
                if(in_array($key, $request->featured_attributes)) {
                    $flipped_attrs[$key] = ['featured' => true];
                }
            }
        }

        $item->attributes()->sync($flipped_attrs);

        $item->save();
        $item->refresh();

        $this->updateChildAttributes($item);

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/category?id='.$item->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    private function updateChildAttributes($item)
    {
        foreach ($item->children as $child) {
            foreach ($item->attributes as $attribute) {
                if (!$child->attributes->contains($attribute)) {
                    $child->attributes()->attach($attribute);
                }
            }
            $child->save();
            $child->refresh();
            $this->updateChildAttributes($child);
        }
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            Category::whereIn('id', $request->items)->delete();
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

    public function export(Request $request)
    {
        $category = Category::find($request->id);
        $types = Attribute::types();
        $data = $category->attributes->map(function($attribute) use($types){
            return "attribute: " . $attribute->name . "( {$types[$attribute->type]} )";
        })->toArray();

        $ProductExporter = new CSVExport(new Product);
        $ProductExporter->merge(['countires', 'similar_products', 'updatable_to_os', 'websites', 'images', ...$data])->export('category');

        return redirect()->back();
    }
}
