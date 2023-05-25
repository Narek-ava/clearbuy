<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'permissions' => 'array|required',
            'permissions.*' => 'integer',
            'name' => ['required', 'string', 'max:255']
        ];

        $rule = Rule::unique('roles');
        if ($this->id) {
            $rule->ignore($this->id);
        }
        $rules['name'][] = $rule;

        return $rules;
    }

    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        if (!$this->permissions) {
            $this->permissions = [];
        }
    }
}
