<?php

namespace App\Http\Requests\FilmReview;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        $rules = [
            'title' => 'sometimes|nullable|string|max:255',
            'rating_from' => 'sometimes|nullable|integer',
            'rating_to' => 'sometimes|nullable|integer',
            'film' => 'sometimes|nullable|string|max:255',
            'page' => 'sometimes|integer',
            'perPage' => 'sometimes|integer',
        ];

        return $rules;
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'title', 'rating']);
    }
}
