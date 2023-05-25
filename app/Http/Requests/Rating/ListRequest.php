<?php

namespace App\Http\Requests\Rating;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'sometimes|nullable|string|max:255',
            'sort_order' => 'sometimes|nullable|integer',
            'page' => 'sometimes|integer',
            'perPage' => 'sometimes|integer',
        ];

        return $rules;
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'name', 'sort_order']);
    }
}
