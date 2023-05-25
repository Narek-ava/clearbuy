<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;

class SimilarProductsAutocomplete extends Component
{
    public $items;
    public $suggestions;
    public $search;
    public $name;
    public $ownId;

    public function mount($name, $items = [], $ownId = 0)
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
                return Product::find($item);
            }
            return $item;
        })->filter(function($item) {
            return $item !== null;
        });

        $this->name = $name;
        $this->ownId = $ownId;
        $this->search = "";
        $this->suggestions = collect([]);
    }

    public function render()
    {
        return view('livewire.similar-products-autocomplete');
    }

    public function autocomplete()
    {
        if (strlen($this->search) == 0) {
            $this->suggestions = collect([]);
            return;
        }
        $this->suggestions = Product::where(function ($query) {
               $query->where('name', 'LIKE', '%'.$this->search.'%')
               ->orWhere('sku', 'LIKE', '%'.$this->search.'%');
           })
           ->whereNotIn('id', $this->items->map(function($item) {
                return $item->id;
            })->concat([$this->ownId]))->limit(10)->get();
    }

    public function hydrate()
    {
        $this->items = Product::whereIn('id', $this->items->map(function($item) {
            return $item['id'];
        }))->get();
    }

    public function add($id)
    {
        if ($id == $this->ownId) {
            return;
        }
        $item = Product::find($id);
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
