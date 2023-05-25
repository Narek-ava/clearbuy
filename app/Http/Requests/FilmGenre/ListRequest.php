<?php

namespace App\Http\Requests\FilmGenre;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        return [
            'name' => 'sometimes|max:255',
            'page' => 'sometimes|integer',
            'perPage' => 'sometimes|integer'
        ];
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'name']);
    }
}
