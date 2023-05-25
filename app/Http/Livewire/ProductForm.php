<?php

namespace App\Http\Livewire;

use App\Http\Controllers\ProductController;
use App\Http\Requests\Product\SaveRequest as Request;
use App\Models\Product;
use App\Services\SidebarLinksService;

use Illuminate\Support\Facades\Validator;
use App\Traits\NullableFields;
use Illuminate\Support\Str;

use Livewire\Component;

class ProductForm extends Component
{
    use NullableFields;

    public Product $item; //bind with model

    //transmitted data to form
    public $currencies;
    public $categories;
    public $brands;
    public $countries;
    public $is_copy;
    public $attributeKinds;
    public $contentTypes;
    public $websites;
    public $badges;
    public $backUrl;
    public $sidebarLinks;
    public $allRatings;

    public $is_first_render;   //to prevent emit in child components (see a view)
    public $prepeared_rules;    //need to save rules state between requests
    protected $rules;   //standard rules for validation
    private $request;  //for saving in ProductController

    protected $validationAttributes = [ //attributes error messages
        'item.price_msrp'   => 'Retail Launch Price',
        'item.date_publish' => 'Launch date',
        //should work but there is no time to find out, checkout later
        'item.deal_prices.*.agent_id' => 'Deal prices: Agent',
        'item.deal_prices.*.price' => 'Deal prices: Price',
        'item.deal_prices.*.currency_id' => 'Deal prices: Currency',
        'item.deal_prices.*.url' => 'Deal prices: URL',
        'item.deal_prices.*.expiry_date' => 'Deal prices: Expiry date',
    ];

    public function mount()
    {
        $this->prepareRules();
        $this->is_first_render = true;
    }

    private function prepareRules($type = null)
    {
        $request = new Request;
        $request->setRules($type);

        foreach($request->rules() as $key => $val) {
            $this->prepeared_rules['item.'.$key] = $val;
        }
        $this->rules = $this->prepeared_rules;
        $this->request = $request;
    }

    public function hydrate()
    {
        $this->sidebarLinks = SidebarLinksService::getLinks('/admin/products');
        $this->rules = $this->prepeared_rules; //quick use
        $this->is_first_render = false; //no need to re-render child lw components


        $this->categories->transform(function($item) {
            return (object) $item ?? null;
        });
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function submit($formData, ProductController $pc)
    {
        $this->prepareRules($type = $formData['draft'] ? 'draft' : null);

        foreach ($formData as $key => $val) {
            $formData[$key] = $this->nullIfEmpty($val); //unlike SaveRequest this form array not includes nulls
        }

        //hidden input name="id" raise a bug with wire:model (inputs loses focus on keyup)
        $formData['id'] = $formData['item_id'] ?? null;

        $ar_multiple_multidim = ['contents','deal_prices','product_prices'];

        foreach ($formData as $key => $val) { //second iteration is needed for transfer nullable values
            foreach($ar_multiple_multidim as $multidim)
            {
                if(Str::startsWith($key, $multidim)) { //like deal_prices[0][agent_id] in $formData
                    $index = Str::between($key, $multidim.'[', '][');
                    $multidim_key = Str::between($key, $index.'][', ']');

                    $formData[$multidim][$index][$multidim_key] = $val;
                }
            }

            //transform simple arrays like source_name[key] to source_name => [key=>val]
            $ar_simple = ['product_attributes', 'ratings', 'source_name', 'source_url'];

            foreach ($ar_simple as $value) {
                $val_with_first_bracket =  $value.'[';
                if(Str::startsWith($key, $val_with_first_bracket)) { //exсept multiple options
                    $ind = Str::between($key, $val_with_first_bracket, ']');
                    $formData[$value][$ind] = $val;
                }
            }
        }

        $clear_rules = []; //prepare the rules without item. prefix

        foreach ($this->rules as $k => $v) {
            $clear_rules[Str::after($k, 'item.')] = $v;
        }

        //$validatedData = $this->validate(); //lw validation issue with rules like array.*
        $validatedData = Validator::make($formData, $clear_rules)->validate();
        $this->request->replace($formData); //fill all fields from formData array

        $pc->save($this->request);
    }

    public function render()
    {
        return view('livewire.product-form');
    }
}
