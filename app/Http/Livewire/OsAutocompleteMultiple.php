<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\OS;
use App\Models\Category;

class OsAutocompleteMultiple extends Component
{
    public $items;
    public $suggestions;
    public $search;
    public $name;
    public $categoryId;
    public $categoryHasItems;

    protected $listeners = ['categoryChanged'];

    public function categoryChanged($categoryId)
    {
        $category = Category::find($categoryId);
        if ($category) {
            $this->categoryId = $categoryId;
            $this->categoryHasItems = $category->os->count() > 0;
        } else {
            $this->categoryId = null;
            $this->categoryHasItems = false;
        }
        $this->autocomplete();
    }

    public function mount($name, $items = [])
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
                return OS::find($item);
            }
            return $item;
        })->filter(function($item) {
            return $item !== null;
        });

        $this->name = $name;
        $this->search = "";
        $this->suggestions = collect([]);
        $this->categoryId = null;
        $this->categoryHasItems = true;
    }

    public function render()
    {
        return view('livewire.os-autocomplete-multiple');
    }

    public function autocomplete()
    {
        if (!$this->categoryHasItems) {
            return;
        }
        if (strlen($this->search) == 0) {
            $this->suggestions = collect([]);
            return;
        }
        $query = OS::where(function($query) {
            $query->orWhere('name', 'LIKE', '%'.$this->search.'%');
        });
        if ($this->categoryId) {
            $categoryId = $this->categoryId;
            $query->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('id', $categoryId);
            });
        }
        $query->whereNotIn('id', $this->items->map(function($item) {
             return $item->id;
         }));
        $this->suggestions = $query->limit(10)->get();
    }

    public function hydrate()
    {
        $this->items = OS::whereKey($this->items->map(function($item) {
            return $item['id'];
        }))->get();
    }

    public function add($id)
    {
        $item = OS::find($id);
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
