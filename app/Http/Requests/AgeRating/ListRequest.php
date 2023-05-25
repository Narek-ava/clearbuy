<?php

namespace App\Http\Requests\AgeRating;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        return [
            'name' => 'sometimes|max:255',
            'age_from' => 'sometimes|nullable|integer',
            'page' => 'sometimes|integer',
            'perPage' => 'sometimes|integer'
        ];
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'name', 'age_from']);
    }
}
