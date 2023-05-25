<?php

namespace App\Http\Livewire;

use Livewire\Component;

class FormCrudButtons extends Component
{
    public  $plural, $list_path, $is_copy;
    public  $item = '';

    public function render()
    {
        return view('livewire.form-crud-buttons');
    }
}
