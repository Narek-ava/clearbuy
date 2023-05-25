<?php

namespace App\Http\Requests\Website;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        // dd($this->perPage);
        $rules = [
            'name' => 'sometimes|max:255',
            'url' => 'sometimes|max:255',
            'description' => 'sometimes|max:500',
            'page' => 'sometimes|integer',
            'perPage' => 'sometimes|integer'
        ];
        return $rules;
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'name']);
    }
}
