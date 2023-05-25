<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Film;

class FilmAutocomplete extends Component
{
    public $item;
    public $suggestions;
    public $search;
    public $name;

    public function mount($name, $item = null)
    {
        $this->dismiss();
        if ($item) {
            $this->item = Film::find($item);
        } else {
            $this->item = null;
        }
        $this->name = $name;
    }

    public function render()
    {
        return view('livewire.film-autocomplete');
    }

    public function autocomplete()
    {
        if (strlen($this->search) == 0) {
            $this->suggestions = collect([]);
            return;
        }
        $query = Film::where(function($query) {
            $query->orWhere('name', 'LIKE', '%'.$this->search.'%');
        });
        $this->suggestions = $query->limit(10)->get();
    }

    public function hydrate()
    {
        if ($this->item !== null) {
            $this->item = Film::find($this->item['id']);
        }
    }

    public function add($id)
    {
        $item = Film::find($id);
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
