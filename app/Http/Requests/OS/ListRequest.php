<?php

namespace App\Http\Requests\OS;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        $rules = [            
            'page' => 'sometimes|integer',
            'perPage' => 'sometimes|integer',
        ];
        return $rules;
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'name', 'license', 'brand']);
    }
}
