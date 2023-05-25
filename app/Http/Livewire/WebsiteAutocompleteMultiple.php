<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Website;

class WebsiteAutocompleteMultiple extends Component
{
    public $items;
    public $suggestions;
    public $search;
    public $name;
    public $excludeIds;

    public function mount($name, $items = [], array $excludeIds = [])
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
                return Website::find($item);
            }
            return $item;
        })->filter(function($item) {
            return $item !== null;
        });

        $this->name = $name;
        $this->excludeIds = $excludeIds;
        $this->search = "";
        $this->suggestions = collect([]);
    }

    public function render()
    {
        return view('livewire.website-autocomplete-multiple');
    }

    public function autocomplete()
    {
        if (strlen($this->search) == 0) {
            $this->suggestions = collect([]);
            return;
        }
        $query = Website::where(function($query) {
            $query->orWhere('name', 'LIKE', '%'.$this->search.'%')
                  ->orWhere('url', 'LIKE', '%'.$this->search.'%');
        });
        $query->whereNotIn('id', $this->items->map(function($item) {
             return $item->id;
         })->concat($this->excludeIds));
        $this->suggestions = $query->limit(10)->get();
    }

    public function hydrate()
    {
        $this->items = Website::whereIn('id', $this->items->map(function($item) {
            return $item['id'];
        }))->get();
    }

    public function add($id)
    {
        $item = Website::find($id);
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
