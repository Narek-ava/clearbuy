<?php

namespace App\Http\Requests\Currency;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:3',
            'country_ids' => 'required|array',
            'country_ids.*' => 'integer|exists:country,id',
        ];

        $rule = Rule::unique('currency');
        if ($this->id) {
            $rule->ignore($this->id);
        }
        //$rules['name'][] = $rule;

        return $rules;
    }
}
