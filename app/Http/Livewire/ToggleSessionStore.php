<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;

class ToggleSessionStore extends Component
{
    public $state, $inactive, $active;

    public function mount(Request $request)
    {
        $this->state = $request->session()->get('showState', false);
    }

    public function changeit(Request $request)
    {
        //save to session as active
        $request->session()->put('showState', $this->state);
        //dispatch state for uri change and reload
        $this->dispatchBrowserEvent('toggle-update');
    }

    public function render()
    {
        return view('components.common.toggle');
    }
}
