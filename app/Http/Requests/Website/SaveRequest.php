<?php

namespace App\Http\Requests\Website;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'logo' => 'sometimes|string',
            'description' => 'sometimes|nullable|string|max:500',
            'url' => 'required|string|url|max:255',
            'name' => ['required', 'string', 'max:255']
        ];

        $rule = Rule::unique('agent');
        if ($this->id) {
            $rule->ignore($this->id);
        }
        $rules['name'][] = $rule;

        return $rules;
    }
}
