<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Attribute;
use App\Models\AttributeOption;

class ProductOptionAutocomplete extends Component
{
    public $items;
    public $suggestions;
    public $search;
    public $name;
    public $attr;

    public function mount($name, $attr, $items = [])
    {
        $this->items = $items;
        if ($this->items === null) {
            $this->items = [];
        }
        if (is_array($this->items)) {
            $this->items = collect($this->items);
        }
        $this->items = $this->items->map(function($item) {
            if (is_numeric($item)) {
                return AttributeOption::find($item);
            }
            return $item;
        })->filter(function($item) {
            return $item !== null;
        });

        $this->name = $name;
        $this->attr = $attr;
        $this->search = "";
        $this->suggestions = collect([]);
    }

    public function render()
    {
        return view('livewire.product-option-autocomplete');
    }

    public function autocomplete()
    {
        if (strlen($this->search) == 0) {
            $this->suggestions = collect([]);
            return;
        }
        $this->suggestions = AttributeOption::where('name', 'LIKE', '%'.$this->search.'%')->
            where('attribute_id', $this->attr->id)->
            whereNotIn('id', $this->items->map(function($item) {
                return $item->id;
            }))->limit(10)->get();
    }

    public function hydrate()
    {
        $this->items = AttributeOption::whereIn('id', $this->items->map(function($item) {
            return $item['id'];
        }))->get();
        if ($this->attr !== null) {
            $this->attr = Attribute::find($this->attr['id']);
        }
    }

    public function add($id)
    {
        $item = AttributeOption::find($id);
        $this->addItem($item);
    }

    public function addRaw()
    {
        if (strlen($this->search) == 0) {
            return;
        }
        $item = AttributeOption::firstOrCreate(['name' => $this->search, 'attribute_id' => $this->attr->id]);
        $this->addItem($item);
    }

    private function addItem($item)
    {
        if ($item == null) {
            return;
        }

        if ($this->items->map(function($item) {
            return $item->id;
        })->contains($item->id)) {
            return;
        }

        $this->items->push($item);

        $this->suggestions = collect([]);
        $this->search = "";
    }

    public function remove($id)
    {
        $this->items->splice($this->items->search(function($item) use ($id) {
            return $item->id == $id;
        }), 1);
    }
}
