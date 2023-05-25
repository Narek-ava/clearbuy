<?php

namespace App\Http\Requests\Deal;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'product_id' => 'required|integer',
            'agent_id' => 'required|integer',
            'currency_id' => 'required|integer',
            'price' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'original_price' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'expiry_date' => 'required|date',
            'url' => 'required|string|url',
            'is_free' => 'sometimes|boolean',
            'recommended' => 'sometimes|boolean',
            'coupon_code' => 'sometimes|nullable|string|max:20',
            'retailer_custom_text' => 'sometimes|nullable|string|max:20',
            'created_at' => 'required|date',
            'expiry_notification' => 'sometimes|nullable'
        ];

        if ($this->links) {
            $rules['primary_link'] = 'required|integer';
        }

        $rule = Rule::unique('deal_prices');
        if ($this->id) {
            $rule->ignore($this->id);
        }
        $rules['name'][] = $rule;

        return $rules;
    }
}
