<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'sometimes|max:255',
            'text' => 'sometimes|max:2000',
            'date_publish_from' => 'sometimes|nullable|date',
            'date_publish_to' => 'sometimes|nullable|date',
            'created_at_from' => 'sometimes|nullable|date',
            'created_at_to' => 'sometimes|nullable|date',
            'price_from' => 'sometimes|nullable|numeric',
            'price_to' => 'sometimes|nullable|numeric',
            'categories' => 'sometimes|array',
            'categories.*' => 'integer',
            'countries' => 'sometimes|array',
            'countries.*' => 'integer',
            'brands' => 'sometimes|array',
            'brands.*' => 'integer',
            'page' => 'sometimes|integer',
            'perPage' => 'sometimes|integer',
        ];
        return $rules;
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'name', 'sku', 'model', 'model_family', 'created_at', 'date_publish', 'price_msrp', 'price_current', 'is_promote', 'category', 'brand', 'country']);
    }
}
