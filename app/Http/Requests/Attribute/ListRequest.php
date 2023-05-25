<?php

namespace App\Http\Requests\Attribute;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'sometimes|max:255',
            'group_name' => 'sometimes|max:255',
            'option' => 'sometimes|max:255',
            'page' => 'sometimes|integer',
            'perPage' => 'sometimes|integer',
            'measures' => 'sometimes|array',
            'measures.*' => 'integer'
        ];
        if ($this->type !== null) {
            $rules['type'] = 'sometimes|integer|min:0|max:5';
        }

        if ($this->kind !== null) {
            $rules['kind'] = 'sometimes|integer|min:1|max:2';
        }
        return $rules;
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'name', 'type', 'measure', 'sort_order']);
    }

    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        if ($this->type == "any") {
            $this->type = null;
        }

        if ($this->kind == "any") {
            $this->kind = null;
        }
    }
}
