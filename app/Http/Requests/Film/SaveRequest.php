<?php

namespace App\Http\Requests\Film;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|integer|min:0|max:1',
            'runtime' => 'sometimes|nullable|string',
            'bingetime' => 'sometimes|nullable|string',
            'seasons' => 'sometimes|nullable|numeric|max:100',
            'still_running' => 'sometimes|nullable|integer|max:1',
            'end_of_release' => 'sometimes|nullable|date',
            'age_rating' => 'required|integer|min:1',
            'release_date' => 'required|date',
            'trailer_link' => 'sometimes|nullable|string|url|max:255',
            'description' => 'sometimes|nullable|string|max:1000',
            'genres' => 'sometimes|nullable|array|distinct',
            'genres.*' => 'integer|min:1',
            'websites' => 'sometimes|nullable|array|distinct',
            'websites.*' => 'integer|min:1',
            'actors' => 'sometimes|nullable|array|distinct',
            'actors.*' => 'integer|min:1',
            'recomendations' => 'sometimes|nullable|array|distinct',
            'recomendations.*' => 'integer|min:1',
            'director' => 'sometimes|nullable|integer|min:1',
            'writer' => 'sometimes|nullable|integer|min:1',
            'producer' => 'sometimes|nullable|integer|min:1',
            'production_company' => 'sometimes|nullable|integer|min:1'
        ];

        return $rules;
    }
}
