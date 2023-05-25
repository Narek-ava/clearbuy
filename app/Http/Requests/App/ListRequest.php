<?php

namespace App\Http\Requests\App;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'sometimes|nullable|string|max:255',
            'brand' => 'sometimes|nullable|string|max:255',
            'price_from' => 'sometimes|nullable|numeric',
            'price_to' => 'sometimes|nullable|numeric',
            'page' => 'sometimes|integer',
            'perPage' => 'sometimes|integer',
        ];

        if ($this->type !== null) {
            $rules['type'] = 'sometimes|integer|min:0|max:1';
        }

        return $rules;
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'name', 'price']);
    }

    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        if ($this->type == "any") {
            $this->type = null;
        }
    }
}
