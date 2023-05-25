<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    // public function authorize() 
    // {
    //     return true;
    // }

    protected function prepareForValidation()
    {
        $this->merge([
            'product_ids' => explode(',', $this->product_id),
            'retailer_ids' => explode(',', $this->retailers),
            'variants_ids' => explode(',', $this->variants)
        ]);
    }

    public function rules() : array
    {
        return [
            'product_ids' => 'required|array',
            'product_ids.*' => 'numeric',
            'retailer_ids' => 'sometimes|array',
            'retailer_ids.*' => 'numeric',
            'price_type' => 'sometimes|nullable|string',
            'style' => 'nullable|string',
        ];
    }
}
