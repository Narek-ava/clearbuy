<?php

namespace App\Http\Requests\AllowedDomains;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'domain' => ['required', 'string', 'max:255']
        ];

        $rule = Rule::unique('allowed_domains');
        if ($this->id) {
            $rule->ignore($this->id);
        }
        $rules['domain'][] = $rule;

        return $rules;
    }
}
