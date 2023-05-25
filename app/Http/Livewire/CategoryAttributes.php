<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\AttributeGroup;

class CategoryAttributes extends Component
{
    public $inheritedAttributes;
    public $kinds;
    public $ownAttributes;
    public $featuredAttributes;
    public $name;

    protected $listeners = ['categoryChanged'];

    public function mount($name, $categoryId = null, array $ownAttributes = [], array $featuredAttributes = [])
    {
        $this->categoryChanged($categoryId);
        $this->ownAttributes = collect($ownAttributes);
        $this->featuredAttributes = collect($featuredAttributes);
    }

    public function render()
    {
        return view('livewire.category-attributes');
    }

    public function categoryChanged($categoryId)
    {
        $this->kinds = collect([]);
        $groups = AttributeGroup::orderBy('sort_order', 'ASC')->whereNull('product_id')->get();
        $kinds = Attribute::kindsList();
        foreach ($kinds as $kind_id => $kind_name) {
            $tmpGroups = collect([]);
            foreach ($groups as $group) {
                $attributes = Attribute::whereHas('group', function($query) use ($group) {
                    $query->where('attribute_group_id', $group->id);
                })->where('kind', $kind_id)->orderBy('sort_order', 'ASC')->get();
                if ($attributes->count() > 0) {
                    $tmpGroups->push((object)[
                        'name' => $group->name,
                        'attributes' => $attributes
                    ]);
                }
            }
            if ($tmpGroups->count() > 0) {
                $this->kinds->push((object)[
                    'name' => $kind_name,
                    'groups' => $tmpGroups
                ]);
            }
        }

        $category = Category::find($categoryId);
        if ($category) {
            $this->inheritedAttributes = $category->attributes;
        } else {
            $this->inheritedAttributes = collect([]);
        }
    }
}
