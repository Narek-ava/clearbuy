<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ProductPendingDraftFilter extends Component
{
    public $selected;
    public string $default; //default value
    public array  $options; //select options
    public $class; //for styles

    public function mount(){

        $selected_def = session('product_listing_pending_draft') ?? null;

        $opt = [
            ['key'=>'pending', 'value'=>'View: Pending'],
            ['key'=>'draft', 'value'=>'View: Drafts'],
        ];

        $opts = json_decode(json_encode($opt), FALSE);

        $this->fill([
            'selected' => $selected_def,
            'default'  => 'View: All',
            'options'  => $opts,
        ]);
    }

    public function updatedSelected($newValue){

        //no need to rerender options
        $this->skipRender();
        //save to session
        session(['product_listing_pending_draft' => $newValue]);
    }


    public function render()
    {
        return view('livewire.product-pending-draft-filter');
    }
}
