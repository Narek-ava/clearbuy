<?php

namespace App\Http\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class ProductDealPrices extends Component
{
    public $deal_prices;

    public function mount($deal_prices = [])
    {
        $this->deal_prices = collect($deal_prices)->map(function ($item) {
            return (object) $item;
        });
    }

    public function hydrate()
    {
        $this->deal_prices->transform(function ($item, $key) {
            return (object) $item ?? null;
        });
    }

    public function add()
    {
        $this->deal_prices->push((object)[
            'agent_id' => '',
            'price' => '',
            'original_price' => '',
            'currency_id' => '',
            'url' => '',
            'coupon_code' => '',
            'retailer_custom_text' => '',
            'expiry_date' => null,
            'recommended'=>false,
            'is_hot' => false,
            'is_free' => false
        ]);
    }

    public function remove($index)
    {
        unset($this->deal_prices[$index]);
    }


    public function render()
    {
        return view('livewire.product-deal-prices');
    }
}
