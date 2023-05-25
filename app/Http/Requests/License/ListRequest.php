<?php

namespace App\Http\Requests\License;

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

        if ($this->is_open_source !== null) {
            $rules['is_open_source'] = 'sometimes|boolean';
        }
        return $rules;
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'name']);
    }

    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        if ($this->is_open_source == "any") {
            $this->is_open_source = null;
        }
    }
}
