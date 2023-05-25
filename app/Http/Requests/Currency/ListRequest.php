<?php

namespace App\Http\Requests\Currency;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        return [
            'name' => 'sometimes|max:255',
            'symbol' => 'sometimes|max:3',
            'page' => 'sometimes|integer',
            'perPage' => 'sometimes|integer',
            'countries' => 'sometimes|array',
            'countries.*' => 'integer'
        ];
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'name', 'symbol', 'country']);
    }
}
