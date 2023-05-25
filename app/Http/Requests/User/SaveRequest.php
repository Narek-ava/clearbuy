<?php

namespace App\Http\Requests\User;

use Laravel\Fortify\Rules\Password;
use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'roles' => 'required|array',
            'roles.*' => 'integer',
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255']
        ];

        $rule = Rule::unique('users');
        if ($this->id) {
            $rule->ignore($this->id);
        }
        $rules['email'][] = $rule;

        if ($this->id === null || $this->password !== null) {
            $rules['password'] = ['required', 'string', new Password, 'confirmed'];
        }

        return $rules;
    }
}
