<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\OS;

class ParentOsRelease extends Component
{
    protected $listeners = ['parentChanged'];
    public $name;
    public $items;
    public $selected;
    public $parent;

    public function render()
    {
        return view('livewire.parent-os-release');
    }

    public function mount($name, $selected = null, $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->selected = $selected;
        $this->parentChanged($parent);
    }

    public function parentChanged($parentId)
    {
        if ($this->parent != $parentId) {
            $this->parent = $parentId;
            $this->selected = null;
        }

        $parent = OS::find($parentId);
        if (!$parent) {
            $this->items = [];
            return;
        }

        $this->items = $parent->releases->map(function($item) {
            return (object)['key' => $item->id, 'value' => $item->version.' ('.$item->release_date.')'];
        })->toArray();
    }
}
