<?php

namespace App\Http\Requests\OS;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'is_kernel' => 'sometimes|nullable|boolean',
            'description' => 'sometimes|string|nullable|max:1000',
            'license' => 'required|integer|min:1',
            'brand' => 'required|integer|min:1',
            'change_log_url' => 'required|string|url|max:255',
            'categories' => 'sometimes|nullable|array|distinct',
            'categories.*' => 'integer|min:1',
            'releases' => 'required|array|distinct|min:1',
            'releases.*.version' => 'required|string|max:255',
            'releases.*.release_date' => 'required|date',
            'releases.*.added_features' => 'sometimes|nullable|array',
            'releases.*.added_features.*' => 'sometimes|nullable|string|max:1000',
            'parent' => 'sometimes|nullable|integer',
        ];

        if (is_numeric($this->parent) && $this->parent > 0) {
            $rules['parent_os_release'] = 'required|integer|min:1';
        }

        $rule = Rule::unique('os');
        if ($this->id) {
            $rule->ignore($this->id);
        }
        //$rules['name'][] = $rule;

        return $rules;
    }
}
