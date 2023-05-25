<?php

namespace App\Http\Requests\AppStore;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'sometimes|max:255',
            'brand_id' => 'sometimes|integer|min:1',
            'url' => 'sometimes|string',
            'icon' => 'sometimes|nullable|string',
            'page' => 'sometimes|integer',
            'perPage' => 'sometimes|integer'
        ];

        return $rules;
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'name']);
    }

    protected function prepareForValidation()
    {
        parent::prepareForValidation();
    }
}
