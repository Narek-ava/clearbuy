<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Http\Helpers\Scrapper\AmazonOriginScrapper;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Country;
use App\Models\Brand;
use App\Models\Currency;
use App\Models\ProductPriceChange;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\Intl\Currencies as SymfonyCurrencies;

/*
*   Fetching product fields from Amazon by ASIN
*/

class ProductAmazonImport extends Component
{
    public string $asin;
    public string $notifyer;
    public string $backUrl;

    protected $rules = [
        'asin' => 'required|string',
    ];

    public function mount(){
        $this->asin = '';  //default value for initialization before validation
    }

    public function getItemInfo()
    {
        $this->validate();

        if(!empty($this->asin))
        {
            $this->asin = trim($this->asin); //remove spaces

            /*
            * fetch product data
            * for more details, refer https://webservices.amazon.com/paapi5/documentation/get-items.html
            */

            $scrapper = new AmazonOriginScrapper([$this->asin]); //amazon API instance
            $items = $scrapper->getItemsInfoReduce();

            $result_data = []; //result array for fields

            if(!empty($items))
            {
                $item = (object) $items[0];

                //Сheck if the country exists, otherwise create new
                $country_db = Country::firstOrCreate(['name' => $item->country_name]);

                //Сheck if the brand exists, otherwise create new
                $brand_db = $item->brand ? Brand::firstOrCreate(['name' => $item->brand]) : null;
                $brand_id = is_null($brand_db) ? null : $brand_db->id;

                if(!is_null($item->currency)) {
                    //Сheck if the currency exists, otherwise create new
                    $symbol = SymfonyCurrencies::getSymbol($item->currency);

                    $currency_db = is_null($item->currency) ? null :
                        Currency::firstOrCreate(
                                ['name' => $item->currency],
                                ['symbol' => $symbol, 'country_ids' => $country_db->id]
                        );
                    $currency_id = is_null($currency_db) ? null : $currency_db->id;

                }else $currency_id = null;

                if(!is_null($item->msrp_currency)) {
                    //Сheck if the msrp_currency exists, otherwise create new
                    $symbol = SymfonyCurrencies::getSymbol($item->msrp_currency);

                    $currency_msrp_db = is_null($item->msrp_currency) ? null :
                        Currency::firstOrCreate(
                            ['name' => $item->msrp_currency],
                            ['symbol' => $symbol, 'country_ids' => $country_db->id]
                        );
                    $currency_msrp_id = is_null($currency_msrp_db) ? null : $currency_msrp_db->id;

                }else $currency_msrp_id = null;

                //convert units
                if(!empty($item->height_unit) && $item->height_unit == 'Inches') $height = ceil(($item->height*2.54)*10); //in millimeters
                if(!empty($item->length_unit) && $item->length_unit == 'Inches') $length = ceil(($item->length*2.54)*10); //in millimeters
                if(!empty($item->width_unit) && $item->width_unit == 'Inches') $width = ceil(($item->width*2.54)*10); //in millimeters
                if(!empty($item->weight_unit) && $item->weight_unit == 'Pounds') $weight = ceil($item->weight*453.59); //in gramms


                /*
                *   Create or update Product, but let's check if exists, for price changes table
                */

                $old_product = Product::where('asin', $this->asin)->first();

                $old_price_current = $old_product->price_current ?? null;
                $old_currency_current = $old_product->currency_current ?? null;

                $old_price_msrp = $old_product->price_msrp ?? null;
                $old_currency_msrp = $old_product->currency_msrp ?? null;

                /*
                *   fill result array
                */

                $result_data['name'] = $item->title;
                $result_data['model'] = $item->model;
                $result_data['size_height'] = $height ?? 0;
                $result_data['size_length'] = $length ?? 0;
                $result_data['size_width'] = $width ?? 0;
                $result_data['weight'] = $weight ?? 0;
                $result_data['summary_main'] = $item->feature_string;
                $result_data['date_publish'] = \date('Y-m-d');

                //new price may not be available, then try to leave the old one
                $result_data['price_current'] = is_null($item->price) ? $old_price_current : $item->price;
                $result_data['currency_current'] = is_null($currency_id) ? $old_currency_current : $currency_id;

                $result_data['price_msrp'] = is_null($item->msrp) ? $old_price_msrp : $item->msrp;
                $result_data['currency_msrp'] = is_null($currency_msrp_id) ? $old_currency_msrp : $currency_msrp_id;

                $result_data['brand_id'] = $brand_id;

                $product = Product::updateOrCreate(['asin' => $this->asin], $result_data);

                /*
                *   Price changes
                */


                if($result_data['price_current'] !== null && $old_price_current !== null && $old_price_current != $product->price_current)
                {
                    $product->priceChanges()->save(ProductPriceChange::create([
                        'product_id' => $product->id,
                        'price_type' => 'current',
                        'price_old' => $old_price_current,
                        'currency_old_id' => $old_currency_current,
                        'price_new' => $product->price_current,
                        'currency_new_id' => $product->currency_current,
                        'reason' => 'changed by asin import update'
                    ]));
                }

                if($result_data['price_msrp'] !== null && $old_price_msrp !== null && $old_price_msrp != $product->price_msrp)
                {
                    $product->priceChanges()->save(ProductPriceChange::create([
                        'product_id' => $product->id,
                        'price_type' => 'msrp',
                        'price_old' => $old_price_msrp,
                        'currency_old_id' => $old_currency_msrp,
                        'price_new' => $product->price_msrp,
                        'currency_new_id' => $product->currency_msrp,
                        'reason' => 'changed by asin import update'
                    ]));
                }


                /*
                *   upload photos in product directory (asin named)
                */

                //primary image url
                $order = 1; //gallery order

                if(!empty($item->primaryImg))
                {
                    //check directory for product
                    if(Storage::disk('do_image_spaces')->missing('products/'.$this->asin)) {
                        //create directory
                        Storage::disk('do_image_spaces')->makeDirectory('products/'.$this->asin);
                    }

                    if(Storage::disk('do_image_spaces')->missing('products/'.$this->asin.'/'.basename($item->primaryImg)))
                    {
                        try{
                            //save to cloud
                            Storage::disk('do_image_spaces')->put('products/'.$this->asin.'/'.basename($item->primaryImg), file_get_contents($item->primaryImg),'public');

                            $parse_url = parse_url($item->primaryImg);
                            //save to db
                            $product->images()->save(new ProductImage([
                                'path' => 'products/'.$this->asin.'/'.basename($item->primaryImg),
                                'order' => $order,
                                'source_url' => $item->primaryImg,
                                'source_name' => $parse_url['host']
                            ]));

                        }catch(Exception $e){
                            //$this->imageStatus = $e->getMessage();
                        }
                    }
                }

                //array of additional images
                if(!empty($item->variants))
                {
                    foreach($item->variants as $v)
                    {
                        $img = $v->getLarge()->getUrl();
                        $order++;

                        //if image not exists in cloud
                        if(Storage::disk('do_image_spaces')->missing('products/'.$this->asin.'/'.basename($img)))
                        {
                            try{
                                //save to cloud
                                Storage::disk('do_image_spaces')->put('products/'.$this->asin.'/'.basename($img), file_get_contents($img), 'public');

                                $parse_url = parse_url($img);
                                //save to db
                                $product->images()->save(new ProductImage([
                                    'path' => 'products/'.$this->asin.'/'.basename($img),
                                    'order' => $order,
                                    'source_url' => $img,
                                    'source_name' => $parse_url['host']
                                ]));

                            }catch(Exception $e){
                                //$this->notifyer = $e->getMessage();
                            }
                        }
                    }
                }

                session()->flash('status', 'success');
                session()->flash('message', 'Fields imported successfully!');
                //Redirect with success
                return redirect()->to('/admin/product?id='.$product->id.'&backUrl='.urlencode($this->backUrl));

            }else $this->notifyer = "Error: item is empty";

        }else $this->notifyer = "Error: Can't find product, check the ASIN";
    }

    public function render()
    {
        return view('livewire.product-amazon-import');
    }
}
