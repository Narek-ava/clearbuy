<?php

namespace App\Http\Requests\FilmGenre;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255']
        ];

        $rule = Rule::unique('film_genre');
        if ($this->id) {
            $rule->ignore($this->id);
        }
        $rules['name'][] = $rule;

        return $rules;
    }
}
