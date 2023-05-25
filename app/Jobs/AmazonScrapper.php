<?php

namespace App\Jobs;

use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;
use App\Http\Helpers\Scrapper\AmazonOriginScrapper as ScrapperAmazonScrapper;
use App\Models\AmazonScrappedFrom;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Currency;
use App\Models\ProductPriceChange;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AmazonScrapper implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $amazonAgentId;
    protected $products;
    protected $currentProduct;
    public array $asins;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Collection $products)
    {
        $this->products = $products;

        $agent = \App\Models\Agent::select('id')->where('name', 'amazon')->first();
        $this->amazonAgentId = $agent->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            if ($this->amazonAgentId != null) {

                $asins = $this->products->map(function ($item) {
                    return $item->asin;
                })->toArray();

                $scrapper = new ScrapperAmazonScrapper($asins);
                $details = $scrapper->getItemsInfoReduce();

                echo "Scrapped Details ", PHP_EOL;

                if(!empty($details)) {

                    foreach ($details as $detail) {

                        $data = (object) $detail;

                        echo "Processing PRODUCT {$data->asin}", PHP_EOL;

                        $product = $this->products->where('asin', $data->asin)->first();

                        $old_price_current = $product->price_current ?? 0;
                        $old_currency_current = $product->currency_current ?? null;

                        $currency_id = is_null($data->currency) ? null : Currency::where(['name' => $data->currency])->value('id');

                            if(!is_null($data->price))
                            {
                                if(intval($old_price_current) != intval($data->price))
                                {
                                    echo "Current price was changed", PHP_EOL;

                                    $product->price_current = $data->price;
                                    $product->currency_current = is_null($currency_id) ? $old_currency_current : $currency_id;
                                }
                            }

                        $old_price_msrp = $product->price_msrp ?? 0;
                        $old_currency_msrp = $product->currency_msrp ?? null;

                        $currency_msrp_id = is_null($data->msrp_currency) ? null : Currency::where(['name' => $data->msrp_currency])->value('id');

                            if(!is_null($data->msrp))
                            {
                                if(intval($old_price_msrp) != intval($data->msrp))
                                {
                                    echo "Current msrp price was changed", PHP_EOL;

                                    $product->price_msrp = $data->msrp;
                                    $product->currency_msrp = is_null($currency_msrp_id) ? $old_currency_msrp : $currency_msrp_id;
                                }
                            }

                        //update prices in product main table

                        if($product->isDirty('price_current') || $product->isDirty('price_msrp'))
                        {
                            $product->updated_at = \date('Y-m-d H:i:s');
                            $product->save();

                            echo "Product saved", PHP_EOL;
                        }


                        //  Price changes table

                        if($data->price !== null && $old_price_current !== null && $old_price_current != $product->price_current)
                        {
                            $product->priceChanges()->save(ProductPriceChange::create([
                                'product_id' => $product->id,
                                'price_type' => 'current',
                                'price_old' => $old_price_current,
                                'currency_old_id' => $old_currency_current,
                                'price_new' => $product->price_current,
                                'currency_new_id' => $product->currency_current,
                                'reason' => 'Changed from Amazon by CRON'
                            ]));
                        }

                        if($data->msrp !== null && $old_price_msrp !== null && $old_price_msrp != $product->price_msrp)
                        {
                            $product->priceChanges()->save(ProductPriceChange::create([
                                'product_id' => $product->id,
                                'price_type' => 'msrp',
                                'price_old' => $old_price_msrp,
                                'currency_old_id' => $old_currency_msrp,
                                'price_new' => $product->price_msrp,
                                'currency_new_id' => $product->currency_msrp,
                                'reason' => 'Changed from Amazon by CRON'
                            ]));
                        }


                        //  Retailer links -> product prices

                        $newPrices = (object) [
                            'agent_id' => $this->amazonAgentId,
                            'product_id' => $product->id,
                            'current_msrp' => $data->price,
                            'original_msrp' => $data->msrp,
                            'currency_id' => $product->currency_current,
                            'url' => $data->url
                        ];

                        $oldPrices = $product->prices->where('agent_id', $this->amazonAgentId)->first();

                        if ($oldPrices == null) {

                            echo "Old product prices are null, saving new prices", PHP_EOL;
                            $product->prices()->save(ProductPrice::create((array) $newPrices));

                        }else{

                            if ($data->price != $oldPrices->current_msrp || $data->msrp != $oldPrices->original_msrp) {

                                echo "Update product prices", PHP_EOL;

                                $oldPrices->update([
                                    'agent_id' => $newPrices->agent_id,
                                    'product_id' => $product->id,
                                    'currency_id' => $newPrices->currency_id,
                                    'current_msrp' => $newPrices->current_msrp,
                                    'original_msrp' => $newPrices->original_msrp,
                                    'url' => $newPrices->url,
                                ]);
                            }

                        }

                    }
                }
            }
        } catch (ApiException $exception) {
            echo "Error calling PA-API 5.0!", PHP_EOL;
            echo "HTTP Status Code: ", $exception->getCode(), PHP_EOL;
            echo "Error Message: ", $exception->getMessage(), PHP_EOL;
            if ($exception->getResponseObject() instanceof ProductAdvertisingAPIClientException) {
                $errors = $exception->getResponseObject()->getErrors();
                foreach ($errors as $error) {
                    echo "Error Type: ", $error->getCode(), PHP_EOL;
                    echo "Error Message: ", $error->getMessage(), PHP_EOL;
                }
            } else {
                echo "Error response body: ", $exception->getResponseBody(), PHP_EOL;
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage() . " File:" . $e->getFile() . "Line: " . $e->getLine());
        }
    }
}
