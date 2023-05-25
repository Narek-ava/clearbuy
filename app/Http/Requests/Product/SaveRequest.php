<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    protected array $rules;

    protected function prepareForValidation()
    {
        //validation without required fields for draft
        if ($this->draft) {
            $this->setRules('draft');
        } else {
            $this->setRules(null);
        }
    }

    public function rules() : array
    {
        return $this->rules;
    }

    public function setRules($type)
    {
        switch ($type) {
            case 'draft':
                $rules = [
                    'name' => 'sometimes|string|max:255',
                    'sku' => 'sometimes|nullable|string|max:255',
                    'asin' => 'sometimes|nullable|string|max:10',
                    'rating' => 'sometimes|nullable|numeric|min:0|max:10',
                    'model' => 'sometimes|nullable|string|max:255',
                    'model_family' => 'sometimes|nullable|string|max:255',
                    'price_msrp' => 'sometimes|nullable|numeric|min:0',
                    'price_current' => 'sometimes|nullable|numeric|min:0',
                    'currency_msrp' => 'sometimes|nullable',
                    'currency_current' => 'sometimes|nullable',
                    'size_length' => 'sometimes|nullable|numeric|min:0',
                    'size_width' => 'sometimes|nullable|numeric|min:0',
                    'size_height' => 'sometimes|nullable|numeric|min:0',
                    'weight' => 'sometimes|nullable|numeric|min:0',
                    'date_publish' => 'sometimes|nullable|date',
                    'is_promote' => 'sometimes|nullable|boolean',
                    'tagline' => 'sometimes|string|nullable|max:50',
                    'excerpt' => 'sometimes|string|nullable|max:255',
                    'summary_main' => 'sometimes|string|nullable',
                    'reasons_to_buy' => 'sometimes|string|nullable|max:500',
                    'pros' => 'sometimes|string|nullable|max:500',
                    'cons' => 'sometimes|string|nullable|max:500',
                    'full_overview' => 'sometimes|string|nullable',
                    'tags' => 'sometimes|nullable|array|distinct',
                    'tags.*' => 'sometimes|nullable|string|max:255',
                    'category_id' => 'sometimes|nullable',
                    'brand_id' => 'sometimes|nullable',
                    'released_with_os' => 'sometimes|nullable|integer',
                    'updatable_to_os' => 'sometimes|nullable|array|distinct',
                    'updatable_to_os.*' => 'sometimes|nullable|integer|min:1',
                    'similar' => 'sometimes|nullable|array|distinct',
                    'similar.*' => 'sometimes|nullable|integer|min:1',
                    'similar_products' => 'sometimes|nullable|array|distinct',
                    'similar_products.*' => 'sometimes|nullable|integer|min:1',
                    'websites' => 'sometimes|nullable|array|distinct',
                    'websites.*' => 'sometimes|nullable|integer|min:1',
                    'review_url' => 'sometimes|nullable|string|url',
                    'buyers_guide_url' => 'sometimes|nullable|string|url',
                    'product_attributes' => 'sometimes|nullable|array',
                    'product_url' => 'sometimes|nullable|url|max:255',
                    'badges' => 'sometimes|nullable|array|distinct',
                    'badges.*' => 'sometimes|nullable|integer|min:1',

                    'deal_prices' => 'sometimes|array',
                    'deal_prices.*.agent_id' => 'required|integer|min:1',
                    'deal_prices.*.price' => 'sometimes|nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                    'deal_prices.*.currency_id' => 'required|integer|min:1',
                    'deal_prices.*.url' => 'sometimes|nullable|url|max:500',
                    'deal_prices.*.coupon_code' => 'sometimes|nullable|string|max:20',
                    'deal_prices.*.retailer_custom_text' => 'sometimes|nullable|string|max:20',
                    'deal_prices.*.expiry_date' => 'sometimes|nullable|date',
                    'deal_prices.*.hot' => 'sometimes|nullable|boolean',

                    'product_prices' => 'sometimes|array',
                    'product_prices.*.agent_id' => 'required|integer|min:1',
                    'product_prices.*.current_msrp' => 'sometimes|nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                    'product_prices.*.original_msrp' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                    'product_prices.*.currency_id' => 'required|integer|min:1',
                    'product_prices.*.url' => 'sometimes|nullable|url|max:500',

                    'contents' => 'sometimes|array',
                    'contents.*.title' => 'sometimes|nullable|string|max:255',
                    'contents.*.description' => 'sometimes|nullable|string|max:500',
                    'contents.*.url' => 'sometimes|nullable|string|url|max:255',
                    'contents.*.type_id' => 'sometimes|nullable|integer|min:1|max:3',
                    'images' => 'sometimes|array|distinct',

                    'ratings' => 'sometimes|array',
                    'ratings.*' => 'sometimes|numeric|min:0|max:10',

                    'attribute: Flight Time( numeric )' => 'sometimes|nullable|numeric',
                    'attribute: Camera FPS( numeric )' => 'sometimes|nullable|numeric',
                    'attribute: Camera resolution( multiple options )' => 'sometimes|array',
                    'attribute: Speed( numeric )' => 'sometimes|nullable|numeric',
                    'attribute: Range( numeric )' => 'sometimes|nullable|numeric',
                    'attribute: Camera sensor size( multiple options )' => 'sometimes|array',
                    'attribute: Camera megapixels( numeric )' => 'sometimes|nullable|numeric',
                    'attribute: Battery size( numeric )' => 'sometimes|nullable|numeric',
                    'attribute: Battery life( numeric )' => 'sometimes|nullable|numeric',
                    'attribute: Battery tech( single option )' => 'sometimes|nullable|string',
                    'attribute: Battery type( single option )' => 'sometimes|nullable|string',
                    'attribute: Service ceiling( numeric )' => 'sometimes|nullable|numeric',
                    'attribute: Ascend speed( numeric )' => 'sometimes|nullable|numeric',
                    'attribute: Descend speed( numeric )' => 'sometimes|nullable|numeric',
                    'attribute: Camera optical zoom( single option )' => 'sometimes|nullable|string',
                    'attribute: Camera data rate( numeric )' => 'sometimes|nullable|numeric',
                    'attribute: External storage type( multiple options )' => 'sometimes|array',
                    'attribute: Internal storage capacity( numeric )' => 'sometimes|nullable|numeric',
                    'attribute: Remote control( string )' => 'sometimes|nullable|string',
                    'attribute: Flight modes( string )' => 'sometimes|nullable|string',
                    'attribute: Available packages( string )' => 'sometimes|nullable|string',
                    'attribute: Hover accuracy: Vertical( numeric )' => 'sometimes|nullable|numeric',
                    'attribute: Hover accuracy: Horizontal( numeric )' => 'sometimes|nullable|numeric',
                ];

                break;

            default:
                $rules = [
                    'name' => 'required|string|max:255',
                    'sku' => 'nullable|bail|string|max:255',
                    'asin' => 'nullable|string|max:10',
                    'rating' => 'nullable|bail|numeric|min:0|max:10',
                    'model' => 'nullable|bail|string|max:255',
                    'model_family' => 'sometimes|string|nullable|max:255',
                    'price_msrp' => 'required|numeric|min:0',
                    'price_current' => 'sometimes|nullable|numeric|min:0',
                    'currency_msrp' => 'required|integer|min:1',
                    'currency_current' => 'sometimes|nullable|integer|min:0',
                    'size_length' => 'sometimes|nullable|numeric|min:0',
                    'size_width' => 'sometimes|nullable|numeric|min:0',
                    'size_height' => 'sometimes|nullable|numeric|min:0',
                    'weight' => 'sometimes|nullable|numeric|min:0',
                    'date_publish' => 'required|date',
                    'is_promote' => 'sometimes|nullable|boolean',
                    'tagline' => 'sometimes|string|nullable|max:50',
                    'excerpt' => 'sometimes|string|nullable|max:100',
                    'summary_main' => 'sometimes|string|nullable',
                    'reasons_to_buy' => 'sometimes|string|nullable|max:500',
                    'pros' => 'sometimes|string|nullable|max:500',
                    'cons' => 'sometimes|string|nullable|max:500',
                    'full_overview' => 'sometimes|string|nullable',
                    'tags' => 'sometimes|nullable|array|distinct',
                    'tags.*' => 'string|max:255',
                    'category_id' => 'required|integer|min:1',
                    'brand_id' => 'required|integer|min:1',
                    'released_with_os' => 'sometimes|nullable|integer',
                    'updatable_to_os' => 'sometimes|nullable|array|distinct',
                    'updatable_to_os.*' => 'integer|min:1',
                    'similar' => 'sometimes|nullable|array|distinct',
                    'similar.*' => 'integer|min:1',
                    'websites' => 'sometimes|nullable|array|distinct',
                    'websites.*' => 'integer|min:1',
                    'review_url' => 'nullable|string|url',
                    'buyers_guide_url' => 'nullable|string|url',
                    'product_attributes' => 'sometimes|nullable|array',
                    'product_url' => 'sometimes|nullable|url|max:255',
                    'badges' => 'sometimes|nullable|array|distinct',
                    'badges.*' => 'sometimes|nullable|integer|min:1',

                    'deal_prices' => 'sometimes|array',
                    'deal_prices.*.agent_id' => 'required|integer|min:1',
                    'deal_prices.*.price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                    'deal_prices.*.currency_id' => 'required|integer|min:1',
                    'deal_prices.*.url' => 'required|url|max:500',

                    'deal_prices.*.coupon_code' => 'sometimes|nullable|string|max:20',
                    'deal_prices.*.retailer_custom_text' => 'sometimes|nullable|string|max:20',

                    'deal_prices.*.expiry_date' => 'required|date',
                    'deal_prices.*.hot' => 'sometimes|nullable|boolean',

                    'product_prices' => 'sometimes|array',
                    'product_prices.*.agent_id' => 'required|integer|min:1',
                    'product_prices.*.current_msrp' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                    'product_prices.*.original_msrp' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                    'product_prices.*.currency_id' => 'required|integer|min:1',
                    'product_prices.*.url' => 'required|url|max:500',

                    'contents' => 'sometimes|array',
                    'contents.*.title' => 'required|string|max:255',
                    'contents.*.description' => 'sometimes|nullable|string|max:500',
                    'contents.*.url' => 'required|string|url|max:255',
                    'contents.*.type_id' => 'required|integer|min:1|max:3',
                    'images' => 'sometimes|array|distinct',

                    'ratings' => 'sometimes|array',
                    'ratings.*' => 'sometimes|numeric|min:0|max:10',
                ];

                if ($this->links) {
                    $rules['primary_link'] = 'required|integer';
                }

                $rule = Rule::unique('product');
                if ($this->id) {
                    $rule->ignore($this->id);
                }
                // $rules['name'][] = $rule; //error: [] operator not supported for strings
                // $rules['sku'][] = $rule;
        }

        $this->rules = $rules;
    }
}

