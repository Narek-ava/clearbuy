<?php

namespace App\Http\Requests\License;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'is_open_source' => 'required|boolean',
            'name' => ['required', 'string', 'max:255']
        ];

        $rule = Rule::unique('license_type');
        if ($this->id) {
            $rule->ignore($this->id);
        }
        $rules['name'][] = $rule;

        return $rules;
    }

    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'is_open_source' => $this->is_open_source ? 1 : 0
        ]);
    }
}
