<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Currency;

class CurrencyAutocomplete extends Component
{
    public $item;
    public $suggestions;
    public $search;
    public $name;

    public function mount($name, $item = null)
    {
        $this->dismiss();
        if ($item) {
            $this->item = Currency::find($item);
        } else {
            $this->item = null;
        }
        $this->name = $name;
    }

    public function render()
    {
        return view('livewire.currency-autocomplete');
    }

    public function autocomplete()
    {
        if (strlen($this->search) == 0) {
            $this->suggestions = collect([]);
            return;
        }
        $this->suggestions = Currency::where('name', 'LIKE', '%'.$this->search.'%')->
            orWhere('symbol', 'LIKE', '%'.$this->search.'%')->limit(10)->get();
    }

    public function hydrate()
    {
        if ($this->item !== null) {
            $this->item = Currency::find($this->item['id']);
        }
    }

    public function add($id)
    {
        $item = Currency::find($id);
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
