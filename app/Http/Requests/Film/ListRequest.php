<?php

namespace App\Http\Requests\Film;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'sometimes|nullable|string|max:255',
            'release_date_from' => 'sometimes|nullable|date',
            'release_date_to' => 'sometimes|nullable|date',
            'genre' => 'sometimes|nullable|string|max:255',
            'age_rating' => 'sometimes|nullable|integer',
            'director' => 'sometimes|nullable|string|max:255',
            'writer' => 'sometimes|nullable|string|max:255',
            'producer' => 'sometimes|nullable|string|max:255',
            'actor' => 'sometimes|nullable|string|max:255',
            'production_company' => 'sometimes|nullable|string|max:255',
            'page' => 'sometimes|integer',
            'perPage' => 'sometimes|integer',
        ];

        if ($this->type !== null) {
            $rules['type'] = 'sometimes|integer|min:0|max:1';
        }

        return $rules;
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'name', 'release_date']);
    }

    protected function prepareForValidation()
    {
        parent::prepareForValidation();

        if ($this->type == "any") {
            $this->type = null;
        }
    }
}
