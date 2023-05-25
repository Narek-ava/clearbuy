<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;

class BeforeDeleteProduct extends Component
{
    public $namelist;
    protected $listeners = ['dispatchPopup'];


    public function mount() {

      $this->namelist = collect();
    }

    public function dispatchPopup($ids) {
        
        if(!empty($ids))
        {
            $items = Product::whereIn('id', $ids)->get();

            foreach($items as $item) {
                $this->namelist->push($item->name);
            }

            foreach($items as $item) {
                if($item->priceChanges()->exists()) {
                    $this->dispatchBrowserEvent('show-confirm-history-modal');
                    return;
                }
            }

            $this->dispatchBrowserEvent('show-delete-product-items-modal');
            //return;
        }
    }

    public function render()
    {
        return view('livewire.before-delete-product');
    }
}
