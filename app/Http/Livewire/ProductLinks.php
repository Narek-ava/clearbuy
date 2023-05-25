<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ProductLinks extends Component
{
    public $links;
    public $primaryLink;

    public function mount($links = [], $primaryLink = null)
    {
        $this->links = $links;
        if ($this->links === null) {
            $this->links = [];
        }
        if (is_array($this->links)) {
            $this->links = collect($this->links);
        }
        $this->links = $this->links->map(function($item) {
            return (object)$item;
        });
        $this->primaryLink = $primaryLink;
        if ($this->primaryLink === null) {
            $this->primaryLink = $this->links->search(function($item) {
                return $item->is_primary;
            });
        }
    }

    public function hydrate()
    {
        $this->links = $this->links->map(function($item) {
            if ($item !== null) {
                return (object)$item;
            } else {
                return null;
            }
        });
    }

    public function render()
    {
        return view('livewire.product-links');
    }

    public function add()
    {
        $this->links->push((object)[
            'agent' => '',
            'price_old' => '',
            'price_new' => '',
            'currency' => '',
            'link' => '',
            'description' => '',
            'is_primary' => false
        ]);
    }

    public function remove($index)
    {
        $this->links[$index] = null;
    }

    public function setPrimary($index)
    {
        $this->primaryLink = $index;
    }
}
