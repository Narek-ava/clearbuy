<?php

namespace App\Http\Livewire;

use App\Http\Helpers\Scrapper\AmazonScrapper;
use Livewire\Component;

class ProductPrices extends Component
{
    public $product_prices;
    public $asin;
    public $showScrappeButton = false;
    public $amazonAgentId = null;

    public function mount($product_prices = [], $asin = null)
    {
        $this->product_prices = $product_prices;
        $this->asin = $asin;
        if ($this->product_prices === null) {
            $this->product_prices = [];
        }
        if (is_array($this->product_prices)) {
            $this->product_prices = collect($this->product_prices);
        }

        $this->product_prices = $this->product_prices->map(function ($item) {
            return (object)$item;
        });

        $amazonAgent = \App\Models\Agent::where('name', 'amazon')->first();

        if ($amazonAgent != null) {
            $this->amazonAgentId = $amazonAgent->id;
        }

        $this->showHideScrappeButton();
    }

    public function showHideScrappeButton()
    {
        if ($this->amazonAgentId != null && $this->asin != null) {

            $this->showScrappeButton = !$this->product_prices->contains('agent_id', $this->amazonAgentId);
        }
    }

    public function hydrate()
    {
        $this->product_prices = $this->product_prices->map(function ($item) {
            if ($item !== null) {
                return (object) $item;
            } else {
                return null;
            }
        });
    }

    public function add()
    {
        $this->product_prices->push((object)[
            'agent_id' => '',
            'current_msrp' => '',
            'original_msrp' => '',
            'currency_id' => '',
            'url' => '',
            'recommended'=>false
        ]);
    }

    public function scrap()
    {
        if ($this->asin != '') {

            try {

                if ($this->amazonAgentId != null) {

                    $scrapper = new AmazonScrapper([$this->asin]);

                    $details =  $scrapper->getItems();

                    if(!empty($details[0])){
                        $detail = (object) $details[0];
                        $data = [
                            'agent_id' => $this->amazonAgentId,
                            'current_msrp' => $detail->amount,
                            'original_msrp' => null,
                            'currency_id' => $detail->currency,
                            'recommended' => false,
                            'url' => $detail->url
                        ];
                        if($detail->savings != null){
                            $data['original_msrp'] = $detail->amount + $detail->savings;
                        }

                        $this->product_prices->push((object)$data);
                    }
                }

                $this->showHideScrappeButton();

            } catch (\Exception $e) {
                //session()->flash('error', $e->getMessage());
                session()->flash('error', $e->getMessage().", file:".$e->getFile().", Line:".$e->getLine());
            }
        }
    }


    public function remove($index)
    {
        $this->product_prices[$index] = null;
        $this->showHideScrappeButton();
    }
    public function render()
    {
        return view('livewire.product-prices');
    }
}
