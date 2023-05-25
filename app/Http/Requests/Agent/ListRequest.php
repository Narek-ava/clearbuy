<?php

namespace App\Http\Requests\Agent;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'sometimes|max:255',
            'website' => 'sometimes|max:255',
            'page' => 'sometimes|integer',
            'perPage' => 'sometimes|integer'
        ];

        if ($this->is_retailer !== null) {
            $rules['is_retailer'] = 'sometimes|boolean';
        }

        if ($this->type !== null) {
            $rules['type'] = 'sometimes|integer|min:0|max:1';
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
        
        if ($this->is_retailer == "any") {
            $this->is_retailer = null;
        }

        if ($this->type == "any") {
            $this->type = null;
        }
    }
}
