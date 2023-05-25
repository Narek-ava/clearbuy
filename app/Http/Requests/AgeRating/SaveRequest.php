<?php

namespace App\Http\Requests\AgeRating;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'age_from' => 'required|integer|min:0',
            'name' => ['required', 'string', 'max:255']
        ];

        $rule = Rule::unique('age_rating');
        if ($this->id) {
            $rule->ignore($this->id);
        }
        $rules['name'][] = $rule;

        return $rules;
    }
}
