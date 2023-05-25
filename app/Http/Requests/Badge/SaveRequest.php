<?php

namespace App\Http\Requests\Badge;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'year' => 'sometimes|nullable|string|max:4',
            'image' => 'sometimes|nullable|string',
        ];

        $rule = Rule::unique('badge');
        if ($this->id) {
            $rule->ignore($this->id);
        }
        $rules['name'] = $rule;

        //$rules = [];

        return $rules;
    }
}
