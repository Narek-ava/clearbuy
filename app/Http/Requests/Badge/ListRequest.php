<?php

namespace App\Http\Requests\Badge;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255',
            'year' => 'sometimes|nullable|string|max:4',
            'image' => 'sometimes|nullable|string',
        ];
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'name', 'year']);
    }
}
