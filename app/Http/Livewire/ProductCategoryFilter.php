<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
//use Illuminate\Http\Request;

class ProductCategoryFilter extends Component
{
    public $selected;
    public string $default; //default value
    public array  $options; //select options
    public $class; //for styles

    public function mount(){

        $selected_def = session('product_listing_category') ?? null;

        $this->fill([
            'selected' => $selected_def,
            'default'  => 'Category: All',
            'options'  => [],
        ]);

        //fill the list with categories
        $this->options = Category::all()->map(function ($item) {
            return (object)['key'=>$item->id, 'value'=>'Category: '.$item->name];
        })->toArray();
    }

    public function updatedSelected($newValue){

        //no need to rerender options
        $this->skipRender();
        //save to session
        session(['product_listing_category' => $newValue]);
    }


    public function render()
    {
        return view('livewire.product-category-filter');
    }
}
