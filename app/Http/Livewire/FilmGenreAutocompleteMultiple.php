<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\FilmGenre;

class FilmGenreAutocompleteMultiple extends Component
{
    public $items;
    public $suggestions;
    public $search;
    public $name;

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
                return FilmGenre::find($item);
            }
            return $item;
        })->filter(function($item) {
            return $item !== null;
        });

        $this->name = $name;
        $this->search = "";
        $this->suggestions = collect([]);
    }

    public function render()
    {
        return view('livewire.film-genre-autocomplete-multiple');
    }

    public function autocomplete()
    {
        if (strlen($this->search) == 0) {
            $this->suggestions = collect([]);
            return;
        }
        $this->suggestions = FilmGenre::where('name', 'LIKE', '%'.$this->search.'%')->
            whereNotIn('id', $this->items->map(function($item) {
                return $item->id;
            }))->limit(10)->get();
    }

    public function hydrate()
    {
        $this->items = FilmGenre::whereKey($this->items->map(function($item) {
            return $item['id'];
        }))->get();
    }

    public function add($id)
    {
        $item = FilmGenre::find($id);
        $this->addItem($item);
    }

    public function addRaw()
    {
        if (strlen($this->search) == 0) {
            return;
        }
        $item = FilmGenre::firstOrCreate(['name' => $this->search]);
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
