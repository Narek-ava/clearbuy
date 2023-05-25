<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductVariant;


class VariantMultiple extends Component
{
    public $product_id;
    public $parent_id;
    public $variant_id;
    public $items;
    public $itemnew;
    public $itemname;
    public $checked = 'checked';

    protected $listeners = [
        'set-itemname' => 'setItemName'
    ];

    // protected $rules = [
    //     'name' => 'required|string|max:256',
    // ];

    public function mount($product_id, $parent_id, $variant_id) {

        $this->fill([
            'product_id' => $product_id,
            'parent_id' => $parent_id,
            'variant_id' => $variant_id,
            'items' => collect(),
        ]);

        if(!is_null($this->product_id)) //existing product
        {
            if(is_null($this->parent_id)) $pid = $this->product_id; //parent edit
            else $pid = $this->parent_id; //child edit

            $children_vars = Product::where('parent_id', $pid)->with('variant')->get();

            if($children_vars->isNotEmpty())
            {
                foreach($children_vars as $child) { //set all children variants

                    $child->variant->product_id = $child->id;
                    $this->items->push($child->variant);
                }
            }

            if(!is_null($this->parent_id)) { //for child
                $this->setChecked($variant_id);

                //unset other variants (show all variants only for parent)
                // $this->items = $this->items->reject(function($value, $key) use ($variant_id) {
                //     return $value->id != $variant_id;
                // });
            }
        }
    }

    public function hydrate() {

        $this->items->transform(function($item) {
            if($this->variant_id == $item['id']){
                $item['checked'] = $this->checked;
            }
            return (object) $item ?? null;
        });
    }

    public function updateditemnew() {
        $this->itemnew = trim($this->itemnew);

        if(empty($this->itemnew)) {
            session()->flash('notice', 'Please enter a variant name');
            return;
        }

        if($this->items->contains('name', $this->itemnew)) {
            session()->flash('notice', 'A variant with the same name already exists');
            return;
        }

        //$this->validate();

        $new_variant = ProductVariant::create([
            'name' => $this->itemnew
        ]);

        $this->items->push($new_variant);

        $this->setChecked($new_variant->id);
        $this->reset(['itemnew']); //purge input
        $this->dispatchBrowserEvent('close-input');

        session()->flash('message', 'New variant: '.$new_variant->name.' has been added, please save changes');
    }

    public function rename($element_id) {
        $this->itemname = trim($this->itemname);

        if(empty($this->itemname)) {
            session()->flash('notice', 'Please enter a variant name');
            return;
        }

        if($this->items->whereNotIn('id', [$element_id])->contains('name', $this->itemname)) {
            session()->flash('notice', 'A variant with the same name already exists');
            return;
        }

        try{
            $new_variant = ProductVariant::findOrFail($element_id);

        }catch(\Exception $e){
            session()->flash('notice', 'error: can\'t find variant in database');
            return;
        }

        $new_variant->name = $this->itemname;
        $new_variant->save();
        session()->flash('message', 'Variant name updated');
    }

    public function setChecked($variant_id) {
        //set new item checked
        $this->items->transform(function($itm, $key) use ($variant_id) {
            if($variant_id == $itm->id){
                $itm->checked = $this->checked;
            }
            return $itm;
        });
    }

    /**
     * Set $itemname
     *
     * @param string $itemName
     * @return void
     */
    public function setItemName($itemName)
    {
        $this->itemname = $itemName;
    }

    public function render()
    {
        return view('livewire.variant-multiple');
    }
}
