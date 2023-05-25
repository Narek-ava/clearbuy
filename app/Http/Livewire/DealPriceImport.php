<?php

namespace App\Http\Livewire;


use App\Models\ProductDealPrice;
use Livewire\Component;
use App\Http\Livewire\Traits\CSVImport;

class DealPriceImport extends Component
{
    use CSVImport;



    public function mount()
    {
        $this->COLUMNS = [
            'product_id',
            "agent_id",
            "price",
            "currency_id",
            "url",
            "coupon_code",
            "expiry_date",
            "is_hot"
        ];
    }

    public function import()
    {

        foreach ($this->rows as $row) {
            $exist = ProductDealPrice::where([
                [ 'product_id', $row['product_id'] ],
                [ 'agent_id', $row['agent_id'] ],
                [ 'price', $row['price'] ],
            ])->count();
            if(!$exist){
                ProductDealPrice::create($row);
            }
        }

        return redirect()->to('/admin/products');
    }
}
