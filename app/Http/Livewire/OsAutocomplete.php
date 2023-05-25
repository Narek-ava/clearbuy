<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\OS;
use App\Models\Category;

class OsAutocomplete extends Component
{
    public $item;
    public $suggestions;
    public $search;
    public $name;
    public $categoryId;
    public $categoryHasItems;
    public $anyCategory;

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

    public function mount($name, $item = null, ?bool $anyCategory = false)
    {
        $this->dismiss();
        if ($item) {
            $this->item = OS::find($item);
        } else {
            $this->item = null;
        }
        $this->name = $name;
        $this->categoryId = null;
        $this->anyCategory = $anyCategory;
        $this->categoryHasItems = $anyCategory;
    }

    public function render()
    {
        return view('livewire.os-autocomplete');
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
        $this->suggestions = $query->limit(10)->get();
    }

    public function hydrate()
    {
        if ($this->item !== null) {
            $this->item = OS::find($this->item['id']);
        }
    }

    public function add($id)
    {
        $item = OS::find($id);
        if ($item == null) {
            return;
        }

        $this->dismiss();
        $this->item = $item;
    }

    public function dismiss()
    {
        $this->item = null;
        $this->suggestions = collect([]);
        $this->search = "";
    }
}
