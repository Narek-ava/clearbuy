<?php

namespace App\Http\Requests\Deal;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        $rules = [
            'product' => 'sometimes|integer',
            'agent' => 'sometimes|integer',
            
            'created_at' => 'sometimes|nullable|date',
            'expiry_date' => 'sometimes|nullable|date',
            'price' => 'sometimes|nullable|numeric',

            'page' => 'sometimes|integer',
            'perPage' => 'sometimes|integer',
        ];
        return $rules;
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'product', 'agent', 'price', 'expiry_date', 'created_at']);
    }
}
