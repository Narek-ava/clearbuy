<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\ProductController;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $model = $this;

        return [
            'id'            => (int) $this->id,
            'name'          => (string) $this->name,
            'rating'        => $this->public_rating,
            'review_url'    => !empty($this->review_url) ? (string) $this->review_url : null,
            'buyers_guide_url' => !empty($this->buyers_guide_url) ? (string) $this->buyers_guide_url : null,
            'tagline'       => !empty($this->tagline) ? (string) $this->tagline : null,
            'excerpt'       => !empty($this->excerpt) ? (string) $this->excerpt : null,
            'summary'       => !empty($this->summary_main) ? (string) $this->summary_main : null,
            'reason_to_buy' => strpos($this->reasons_to_buy, '|') ? (array) explode('|', $this->reasons_to_buy) : [],
            'image'         => !is_null($this->productFirstImage()) ? (string) $this->productFirstImage() : 'undefined',
            'prices'        => self::getPrices($request, $model),
            'edit_url'      => action([ProductController::class, 'form'], ['id' => $this->id, 'backUrl' => '/admin/products'])
        ];
    }



    private static function getPrices($request, $model) : array
    {
        $api_request = $request->all(); //clear request without ProductRequest's merging in prepareForValidation()

        //$numberOfRecords = isset($api_request['style']) ? ($api_request['style'] == 'large' ? 6 : 1) : 6;
        $agents = isset($api_request['retailers']) ? explode(',', $api_request['retailers']) : [];

        $product_cast = [
            'id as id',
            'agent_id as agent_id',
            'currency_id as currency_id',
            'current_msrp as price',
            'original_msrp as original_price',
            'url as url',
            'recommended as recommended'
        ];

        switch($request->price_type) {

            case 'product':
                $all_prices = $model->prices()
                                    ->whereAgents($agents)
                                    ->byRecomended('DESC')
                                    ->orderByPrice()
                                    ->get($product_cast);
                break;

            case 'deal':
                $all_prices = $model->deal()
                                    ->notExpiry()
                                    ->whereAgents($agents)
                                    ->byRecomended('DESC')
                                    ->orderByPrice()
                                    ->get();
                break;

            default:
                //both price types should be used and ordered
                //by "recommended", then "price", then "price_type" (deal first).

                $deal_prices = $model->deal()
                                        ->notExpiry()
                                        ->whereAgents($agents)
                                        ->get();

                $product_prices = $model->prices()
                                        ->whereAgents($agents)
                                        ->get($product_cast);

                $merged_prices = $deal_prices->merge($product_prices);

                if($merged_prices->isNotEmpty()) {

                    //sort chain

                    $all_prices = $merged_prices->sortBy([
                        ['recommended', 'desc'],
                        ['price', 'asc'],
                        ['is_hot', 'desc']
                    ]);

                } else { //if both prices not exists, then get price from general tab

                    return [[
                        'agent_name' => (string) $model->brand->name,
                        'current_price' => (float) $model->price_msrp,
                        'currency' => (string) $model->msrpCurrency->symbol,
                        'url' => (string) $model->product_url,
                        'coupon_code' => null,
                        'is_hot' => false,
                        'is_free' => false,
                        'retailer_custom_text' => $model->retailer_custom_text
                    ]];
                }
        }


        // TODO refactor this on next iteration
        $style = $api_request['style'] ?? '';
        $price_limit = $style == 'large' ? 6 : 1;

        $all_prices = $all_prices->slice(0, $price_limit);
        $data = [];

        foreach ($all_prices as $price) {

            $new_original_price = max($price->original_price, $price->price);
            $new_current_price = min($new_original_price, $price->price);

             $price_data = [
                'agent_name' => (string) $price->agent->name,
                'original_price' => (float) $new_original_price,
                'current_price' => (float) $new_current_price,
                'currency' => (string) $price->currency->symbol,
                'url' => (string) $price->url,
                'coupon_code' => $price->coupon_code,
                'is_hot' => (bool) ($price->is_hot ?? false),
                'is_free' => (bool) ($price->is_free ?? false),
                'retailer_custom_text' => $price->retailer_custom_text
            ];

            if ($price_data['is_free']) {
                unset($price_data['original_price']);
                unset($price_data['current_price']);
            }

            $data[] = $price_data;
        }

        return $data;
    }



}
