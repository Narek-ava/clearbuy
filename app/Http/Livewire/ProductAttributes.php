<?php

namespace App\Http\Livewire;

use Livewire;

use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductAttributes extends Livewire\Component
{
    protected $listeners = [
        'categoryChanged' => 'updateAttributeList',
        'deleteAttrSet'
    ];
    public $groups;
    public $kind_id;
    public $productId;
    public $old;
    public $categoryId;
    public $component_id;
    public $itemname;
    public $is_first_render;

    public function mount($productId, $old = null, $kind_id) {
        $this->groups = collect([]);
        $this->kind_id = $kind_id;
        $this->productId = $productId;
        $this->old = $old;
    }

    public function updateAttributeList($categoryId) {

        $this->categoryId = $categoryId;
        $this->errors = false;
        $category = Category::find($categoryId);
        $this->groups = collect([]); //this happens before mount (from listener)
        if(is_null($category)) return;

        //get all parents groups
        $groups = AttributeGroup::whereNull('parent_id')->orderBy('sort_order', 'ASC')->get();

        foreach($groups as $group) {

            $attributes = $category->attributes()->whereHas('group', function($query) use ($group) {
                $query->where('attribute_group_id', $group->id);
            })->where('kind', $this->kind_id)->orderBy('sort_order', 'ASC')->get();

            $children_groups = $group->productGroups($this->productId)->get();

            if($children_groups->isNotEmpty()) {

                foreach($children_groups as &$child_group) {

                    $child_attributes = $category->attributes()->whereHas('group', function($query) use ($child_group) {
                         $query->where('attribute_group_id', $child_group->id);
                    })->orderBy('sort_order', 'ASC')->get();

                    $child_group->attrs = $child_attributes;

                }
            }

            if ($attributes->count() > 0) {
                $this->groups->push((object)[
                    'id' => $group->id,
                    'name' => $group->name,
                    'repeatable' => $group->repeatable,
                    'parent_id' => $group->parent_id,
                    'children'  => $children_groups,
                    'attributes' => $attributes
                ]);

            }
        }

        //dd($this->groups);

        $this->component_id = $this->id; //current lw component_id
    }

    public function hydrate() {
        if(!is_null($this->component_id)) {
            $this->updateAttributeList($this->categoryId);
        }
    }

    public function addSubGroup($parent_id, $new_group_name) {

        $new_group_name = trim($new_group_name);

        if(empty($new_group_name)) {
            session()->flash('notice', 'Please enter a set name');
            return;
        }

        foreach($this->groups as $group) {
            if(intval($group->id) == intval($parent_id)) {
                if($group->children->contains('name', $new_group_name)) {
                    session()->flash('notice', 'Attribute set with the same name already exists');
                    return;
                }
            }
        }

        $category = Category::findOrFail($this->categoryId);

        $original_attributes = $category->attributes()->whereHas('group', function($query) use ($parent_id) {
                 $query->where('attribute_group_id', $parent_id);
        })->orderBy('sort_order', 'ASC')->get();


        try {

            DB::beginTransaction();

            $new_group = AttributeGroup::firstOrCreate([
                'name' => $new_group_name,
                'product_id' => $this->productId,
                'parent_id' => $parent_id
            ]);

            //clone attributes and attach them to category
            foreach($original_attributes as $item) {

                $clone = $item->replicate()->fill([
                    'attribute_group_id' => $new_group->id,
                    'parent_id' => $item->id
                ]);
                $clone->save();

                $category->attributes()->save($clone);
            }

            DB::commit();

            $this->updateAttributeList($this->categoryId);
            session()->flash('message', 'New set: '.$new_group_name.' has been added');

        } catch(Exception $e) {

            DB::rollBack();
            session()->flash('notice', $e->getMessage());
            return;
        }
    }

    public function rename($group_id) {

        $this->itemname = trim($this->itemname);

        if(empty($this->itemname)) {
            session()->flash('notice', 'Please enter a set name');
            return;
        }

        try{
            $new_group = AttributeGroup::findOrFail($group_id);
        }catch(\Exception $e){
            session()->flash('notice', 'error: can\'t find attribute set in DB');
            return;
        }

        //Find a parent and all its children
        $children_list = AttributeGroup::find($new_group->parent_id)->children()->get();

        if($children_list->isNotEmpty()) {
            if($children_list->contains('name', $this->itemname)) {
                session()->flash('notice', 'Attribute set with the same name already exists');
                return;
            }
        }

        $new_group->name = $this->itemname;
        $new_group->save();

        $this->updateAttributeList($this->categoryId);

        session()->flash('message', 'Set name updated');
    }

    public function deleteAttrSet($item_id) {

        if(!empty($item_id)) {

            try {

                DB::beginTransaction();

                //get attributes ids for removing product attributes values first
                $attrs = Attribute::where('attribute_group_id', $item_id)->get();

                if($attrs->isNotEmpty()) {
                    foreach($attrs as $attr) {
                        $attr->products()->detach();
                    }
                }

                //remove attributes
                Attribute::where('attribute_group_id', $item_id)->delete();

                //remove group
                AttributeGroup::where('id', $item_id)->delete();

                DB::commit();

            } catch (\Illuminate\Database\QueryException $ex) {
                session()->flash('notice', $ex->getMessage());
                DB::rollBack();
                return;
            }
            $this->updateAttributeList($this->categoryId);
            session()->flash('message', 'Deleted successfully');
            return;
        }
        session()->flash('message', 'Nothing to delete!');
        return;
    }

    public function render()
    {
        return view('livewire.product-attributes');
    }
}
