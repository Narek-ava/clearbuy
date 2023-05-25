<?php

namespace App\Http\Requests\People;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'sometimes|max:255',
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
