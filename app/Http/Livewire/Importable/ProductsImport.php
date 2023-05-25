<?php

namespace App\Http\Livewire\Importable;

use Livewire\Component;

use App\Models\AttributeGroup;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\Country;
use App\Models\OS;
use App\Models\ProductImage;
use App\Models\ProductPrice;
use App\Models\Website;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Http\Helpers\Scrapper\AmazonScrapper;
use App\Http\Requests\Product\SaveRequest as Request;
use App\Http\Livewire\Traits\CSVImport;
use App\Services\ZapierService;
use App\Traits\NullableFields;
use Image;

class ProductsImport extends Component
{
    use CSVImport, NullableFields;

    public function mount() {
        $this->baseUrl = '/admin/products';
    }

    public function import()
    {
        //get Product SaveRequest for actually rules array
        $request = new Request;
        $request->setRules('draft'); //except required
        $this->setRules($request->rules());

        //get all array rules
        $array_rules = collect($this->rules)->filter(function($item, $key){
            return Str::contains($item, 'array');
        })->keys()->all();

        /*
        *   Validation
        */

        foreach ($this->rows as $key=>$row)
        {
            //convert empty cells to null and some specified cells to array
            $row_with_nulls = collect($row)->map(function($item, $key) use ($array_rules) {

                if(in_array($key, $array_rules)){

                    if(strpos($item, '|')) $item = explode('|', $item);
                    elseif(strpos($item, ',')) $item = explode(',', $item);
                    else $item = [$item];
                    return $item;
                }
                return $this->nullIfEmpty($item);

            })->toArray();

            $this->validateData($row_with_nulls, $key);
        }

        if(!empty($this->errorsData)){
            $message = join(PHP_EOL, $this->errorsData);
            Log::channel('import_slack')->info($message);
        }

        //get attributes array where attr => type
        $array_attributes = collect($this->rules)->filter(function($item, $key){
            return Str::contains($key, 'attribute:');

        })->mapWithKeys(function($item, $key){

            $clear_attribute = Str::between($key, 'attribute:', '(');
            $attribute_type  = Str::between($key, '(', ')');
            return [trim($clear_attribute) => trim($attribute_type)];
        });

        /*
        *   Data Storage
        */

        //create an attribute group if not exists
        $attribute_group = AttributeGroup::firstOrCreate([ 'name' => 'Default' ]);

        if($array_attributes->isNotEmpty())
        {
            $attributes = [];
            $types = Attribute::types();
            $i = 0;

            foreach($array_attributes as $attr => $type)
            {
                //create an attribute if not exists
                $attribute_object = Attribute::firstOrCreate(
                    ['name' => $attr],
                    ['type' => $types->search($type), 'kind' => 1, 'sort_order' => $i+=10, 'attribute_group_id' => $attribute_group->id]
                );

                array_push($attributes, $attribute_object);
            }
        }

        $storedData = 0;

        if(!empty($this->validsData))
        {
            ini_set('max_execution_time', '0'); //infinite time

            foreach ($this->validsData as $key => $row) {

                if(Product::where('name', $row['name'])->where('asin', $row['asin'])->doesntExist())
                {
                    //foreign key => parent table
                    $ar_foreign = [
                        'brand_id' => 'brand',
                        'category_id' => 'category',
                        'country_id' => 'country',
                        'currency_current' => 'currency',
                        'currency_msrp' => 'currency',
                        'released_with_os_id' => 'os'
                    ];

                    //check if foreign key exists
                    foreach ($ar_foreign as $key=>$table) {
                        if(isset($row[$key]) && !is_null($row[$key]))
                        {
                            if (DB::table($table)->where('id', $row[$key])->doesntExist()) {
                                $row[$key] = null;
                            }
                        }
                    }

                    //tags array to string
                    if(!empty($row['tags']))
                    {
                        $row['tags'] = join(',', $row['tags']);
                    }

                    if(!empty($row['date_publish']))
                    {
                        $date_publish = new \Carbon\Carbon($row['date_publish']);
                        $row['date_publish'] = $date_publish->format('Y-m-d');
                    }

                    //save product
                    $product = Product::create($row);
                    ZapierService::productCreated($product->id);
                    $storedData++;

                    $category = Category::findOrNew($row['category_id']);
                    if(empty($category->name))
                    {
                       $category->name = 'NameIt_'.$row['category_id'];
                       $category->save();
                    }

                    //manipulation of imported attributes
                    $product_attrs_values = collect($row)->filter(function($item, $key){
                        return Str::contains($key, 'attribute:');

                    })->mapWithKeys(function($item, $key){

                        $clear_attribute = Str::between($key, 'attribute:', '(');
                        return [trim($clear_attribute) => $item];

                    })->all();

                    $product->attributes()->detach();

                    foreach ($attributes as $attribute) {

                        //check if attributes are checked for a product category
                        if(!$category->attributes->contains($attribute->id)) {
                            $category->attributes()->attach($attribute);
                        }

                        if (array_key_exists($attribute->name, $product_attrs_values))
                        {
                            $value = $product_attrs_values[$attribute->name];

                            if(!empty($value))
                            {
                                //save attributes values in product
                                switch(intval($attribute->type))
                                {
                                    case 0: //numeric
                                        $product->attributes()->attach([$attribute->id => ['value_numeric' => $value]]);
                                        break;

                                    case 1: //string
                                        $product->attributes()->attach([$attribute->id => ['value_text' => $value]]);
                                        break;

                                    case 2: //boolean
                                        $product->attributes()->attach([$attribute->id => ['value_boolean' => !!$value]]);
                                        break;

                                    case 3: //datetime
                                        $product->attributes()->attach([$attribute->id => ['value_date' => $value]]);
                                        break;

                                    case 4: //single option
                                        $option = $attribute->options()->firstOrCreate(['name' => $value]);
                                        $product->attributes()->attach([$attribute->id => ['attribute_option_id' => $option->id]]);
                                        break;

                                    case 5: //multiple options
                                        foreach($value as $v)
                                        {
                                            if(!empty($v)) {
                                              $option = $attribute->options()->firstOrCreate(['name' => $v]);
                                              $product->attributes()->attach([$attribute->id => ['attribute_option_id' => $option->id]]);
                                            }
                                        }
                                        break;

                                    case 6: //decimal
                                        $product->attributes()->attach([$attribute->id => ['value_numeric' => $value]]);
                                        break;

                                    default:
                                        continue 2;

                                }
                            }
                        }

                    }

                    if (isset($row['updatable_to_os']) && !empty($row['updatable_to_os'])) {
                        $osList = OS::whereKey($row['updatable_to_os'])->get();
                        if($osList->isNotEmpty()) {
                            foreach ($osList as $os) {
                                $product->updatableToOS()->attach($os);
                            }
                        }
                    }

                    //target countries
                    if (isset($row['countries']) && !empty($row['countries'])) {
                        $countries = Country::whereIn('id', $row['countries'])->get();
                        if($countries->isNotEmpty()) {
                            foreach ($countries as $country) {
                                $product->targetCountries()->attach($country);
                            }
                        }
                    }

                    if (isset($row['similar_products']) && !empty($row['similar_products'])) {
                        $similar = Product::whereIn('id', $row['similar_products'])->get();
                        if($similar->isNotEmpty()) {
                            foreach ($similar as $similar_product) {
                                $product->similarProducts()->attach($similar_product);
                                $similar_product->similarProducts()->attach($product);
                            }
                        }
                    }

                    if (isset($row['websites']) && !empty($row['websites'])) {
                        foreach ($row['websites'] as $website) {
                            $website = Website::where('id', $website)->get();
                            $product->websites()->attach($website);
                        }
                    }

                    $image_path = is_null($row['asin']) ? 'products/' : 'products/'.$row['asin'];

                    if (isset($row['images']) && !empty($row['images'])) {

                        $product->images()->delete();

                        foreach ($row['images'] as $order => $url) {

                            if(filter_var(trim($url), FILTER_VALIDATE_URL)) {

                                $filename = basename($url);

                                if(Storage::disk('do_image_spaces')->missing($image_path.'/'.$filename))
                                {
                                    try{

                                        $contents = file_get_contents($url);

                                        if($contents !== false AND !empty($contents)) {

                                            $filePath = storage_path('app/images/');

                                            $img = Image::make($url); //make image for resize

                                            if($img->width() > 1920 || $img->height() > 1080)
                                            {
                                                $img->resize(1920, 1080, function ($const) {
                                                   $const->aspectRatio();
                                                })->save($filePath.'/'.$filename, 75); //save temporary file

                                                $contents = file_get_contents($filePath.'/'.$filename);
                                                $resize = true;
                                            }

                                            //create cloud directory if not exists
                                            if(Storage::disk('do_image_spaces')->missing($image_path))
                                            {
                                                Storage::disk('do_image_spaces')->makeDirectory($image_path);
                                            }

                                            //cloud upload
                                            try{
                                                Storage::disk('do_image_spaces')->put($image_path.'/'.$filename, $contents, 'public');
                                                if(isset($resize)) unlink($filePath.'/'.$filename); //remove temporary file
                                            }catch(Exception $e){
                                                //Log::channel('import_slack')->info($e->getMessage());
                                            }
                                        }

                                    }catch(\Exception $e){
                                        //Log::channel('import_slack')->info($e->getMessage());
                                    }
                                }

                                //save to DB
                                if(ProductImage::where('path', $image_path.'/'.$filename)->doesntExist())
                                {
                                    $parse_url = parse_url($url);

                                    $product->images()->save(new ProductImage([
                                        'path' => $image_path.'/'.$filename,
                                        'order' => $order,
                                        'source_url' => $url,
                                        'source_name' => $parse_url['host']
                                    ]));
                                }
                            }

                        }

                    }

                    if($row['asin'] !== null) { //amazon prices

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
                            //Log::channel('import')->info($e->getMessage());
                        }

                    }

                }

            }

        }

        return $storedData;
    }

}
