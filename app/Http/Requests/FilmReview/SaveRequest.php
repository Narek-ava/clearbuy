<?php

namespace App\Http\Requests\FilmReview;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'subtitle' => 'sometimes|nullable|string|max:255',
            'rating' => 'required|integer',
            'summary' => 'sometimes|nullable|string|max:2000',
            'positive' => 'sometimes|nullable|string|max:2000',
            'negative' => 'sometimes|nullable|string|max:2000',
            'film' => 'required|integer|min:1',
            'recomendations' => 'sometimes|nullable|array|distinct',
            'recomendations.*' => 'integer|min:1'
        ];

        return $rules;
    }
}
