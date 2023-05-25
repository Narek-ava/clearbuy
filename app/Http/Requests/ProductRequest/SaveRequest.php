<?php

namespace App\Http\Requests\ProductRequest;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'required|unique:product,name|string|max:255',
            'brand' => 'nullable|integer|min:1',
            'reasons_to_buy' => 'nullable|string|max:500',
            'excerpt' => 'nullable|string|max:255',
            'summary_main' => 'nullable|string',
            'asin' => 'nullable|unique:product,asin|string|max:10',
            'price_msrp' => 'nullable|numeric|min:0',
            'currency_msrp' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'price_tracking' => 'sometimes|nullable|boolean',
            'product_image' => 'sometimes|nullable|mimes:jpeg,gif,png,webp',
            'agent_id' => 'nullable|integer|min:1',
            'original_msrp' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'currency_id' => 'nullable|integer|min:1',
            'url' => 'nullable|string|url|max:500',
            'urgency' => 'nullable|numeric|min:1|max:5',
            'submitter_email' => 'sometimes|email|nullable|max:64',
        ];

        return $rules;
    }
}
