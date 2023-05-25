<?php

namespace App\Http\Livewire\Importable;


use App\Http\Helpers\Scrapper\AmazonScrapper;
use Livewire\Component;
use App\Http\Livewire\Traits\CSVImport;
use App\Models\Attribute;
use App\Models\Country;
use App\Models\OS;
use App\Models\ProductPrice;
use App\Models\Website;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ZapierService;
use Illuminate\Support\Facades\Log;

class CategoriesImport extends Component {
    use CSVImport;
    public $category_id;

    public function mount() {
        $this->baseUrl = '/admin/categories';
    }

    public function updatedCategoryId($value) {
        $query = parse_url($this->exportUrl, PHP_URL_QUERY);
        if ($query) {
            $this->exportUrl = preg_replace('/id=\d+/', "id={$this->category_id}", $this->exportUrl);
        } else {
            $this->exportUrl .= "?id={$this->category_id}";
        }
    }
    public function import() {

        try {
            foreach ($this->rows as $row) {
                $re = '/^attribute:[\s]([a-zA-Z0-9\s]+)/';
                $attributes = [];
                $productData = [];
                foreach ($row as $key => $value) {
                    preg_match($re, $key, $matches);
                    if (!empty($matches) && $value !== "") {
                        $attributes[$matches[1]] = $value;
                    } else {
                        if ($value !== '') {
                            $productData[$key] = $value;
                        }
                    }
                }

                $attributes_name = array_keys($attributes);

                $attributesDB = Attribute::whereIn('name', $attributes_name)->get();
                $exist = Product::where('name', $row['name'])->orWhere('sku', $row['sku'])->first();
                if (!$exist) {

                    $product = Product::create($productData);

                    if (isset($row['updatable_to_os']) && $row['updatable_to_os'] !== '') {
                        $updatable_to_os_ids = explode(',', $row['updatable_to_os']);
                        $osList = OS::whereKey($updatable_to_os_ids)->get();
                        foreach ($osList as $os) {
                            $product->updatableToOS()->attach($os);
                        }
                    }

                    if (isset($row['countries']) && $row['countries'] !== "") {
                        $countries = explode(',', $row['countries']);
                        $countries = Country::whereIn('id', $countries)->get();
                        foreach ($countries as $country) {
                            $product->targetCountries()->attach($country);
                        }
                    }
                    if (isset($row['similar_products']) && $row['similar_products'] !== '') {
                        $similar_products_ids = explode(',', $row['similar_products']);
                        $similar = Product::whereIn('id', $similar_products_ids)->get();
                        foreach ($similar as $similar_product) {
                            $product->similarProducts()->attach($similar_product);
                            $similar_product->similarProducts()->attach($product);
                        }
                    }

                    if (isset($row['websites']) && $row['websites'] !== '') {
                        $websites = explode(',', $row['websites']);
                        $websites = Website::whereIn('id', $websites)->get();
                        foreach ($websites as $website) {
                            $product->websites()->attach($website);
                        }
                    }

                    if (isset($row['images']) && $row['images'] !== '') {
                        $images = explode('|', $row['images']);
                        foreach ($images as $order => $path) {
                            $product->images()->save(new ProductImage(['path' => $path, 'order' => $order]));
                        }
                    }

                    if ($row['asin']) {
                        try {
                            $amazonAgent = \App\Models\Agent::select('id')->where('name', 'amazon')->first();
                            if ($amazonAgent != null) {
                                $scrapper = new AmazonScrapper([$row['asin']]);

                                $details = $scrapper->getItems();

                                if (!empty($details[0])) {
                                    $detail = (object) $details[0];
                                    $data = [
                                        'product_id' => $product->id,
                                        'agent_id' => $amazonAgent->id,
                                        'current_msrp' => $detail->amount,
                                        'original_msrp' => null,
                                        'currency_id' => $detail->currency,
                                        'url' => $detail->url
                                    ];
                                    if ($detail->savings != null) {
                                        $data['original_msrp'] = $detail->amount + $detail->savings;
                                    }
                                    $product->prices()->save(ProductPrice::create($data));
                                }
                            }
                        } catch (\Exception $e) {
                            Log::info($e->getMessage());
                        }
                    }

                    // insert attributes
                    if ($attributesDB->count()) {
                        foreach ($attributesDB as $attribute) {
                            $value = $attributes[$attribute->name];
                            if ($attribute->type == 0) {
                                $value = (int) $value;
                                $data = ['value_numeric' => $value];
                                $product->attributes()->attach([$attribute->id => $data]);
                            } elseif ($attribute->type == 1) {
                                $data = ['value_text' => $value];
                                $product->attributes()->attach([$attribute->id => $data]);
                            } elseif ($attribute->type == 2) {
                                $data = ['value_boolean' => !!$value];
                                $product->attributes()->attach([$attribute->id => $data]);
                            } elseif ($attribute->type == 3) {
                                $data = ['value_date' => $value];
                                $product->attributes()->attach([$attribute->id => $data]);
                            } elseif ($attribute->type == 4) {
                                $option = $attribute->options()->where('id', $value)->first();
                                if (!$option) {
                                    $this->failedImports[] = $row['name'];
                                    Log::info("For product {$row['name']} selected option does not exist for attribute {$attribute->name}");
                                    continue;
                                }
                                $data = ['attribute_option_id' => $option->id];
                                $product->attributes()->attach([$attribute->id => $data]);
                            } elseif ($attribute->type == 5) {
                                $value = explode(',', $value);

                                $options = $attribute->options()->whereIn('id', $value)->get();
                                foreach ($options as $option) {
                                    $product->attributes()->attach([$attribute->id => ['attribute_option_id' => $option->id]]);
                                }
                            } else {
                                continue;
                            }
                        }
                    }

                    ZapierService::productCreated($product->id);
                }
            }
        } catch (\Exception $e) {
            Log::info("Error importing product {$row['name']}, Error: {$e->getMessage()}");
        }
    }
}
