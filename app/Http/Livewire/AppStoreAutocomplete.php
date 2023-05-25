<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\AppStore;

class AppStoreAutocomplete extends Component
{
    public $item;
    public $suggestions;
    public $search;
    public $name;

    public function mount($name, $item = null)
    {
        $this->dismiss();
        if ($item) {
            $this->item = AppStore::find($item);
        } else {
            $this->item = null;
        }
        $this->name = $name;
    }

    public function render()
    {
       
        return view('livewire.app-store-autocomplete');
    }

    public function autocomplete()
    {
        if (strlen($this->search) == 0) {
            $this->suggestions = collect([]);
            return;
        }
        $query = AppStore::where(function($query) {
            $query->orWhere('name', 'LIKE', '%'.$this->search.'%')
                  ->orWhere('url', 'LIKE', '%'.$this->search.'%');
        });
        $this->suggestions = $query->limit(10)->get();
    }

    public function hydrate()
    {
        if ($this->item !== null) {
            $this->item = AppStore::find($this->item['id']);
        }
    }

    public function add($id)
    {
        $item = AppStore::find($id);
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
