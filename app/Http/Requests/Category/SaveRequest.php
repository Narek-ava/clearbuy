<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'parent' => 'sometimes|nullable|integer',
            'attribute_ids' => 'sometimes|array|distinct',
            'attribute_ids.*' => 'integer',
            'featured_attributes' => 'sometimes|array|distinct|max:6',
            'featured_attributes.*' => 'integer',
            'name' => ['required', 'string', 'max:255'],
        ];

        $rule = Rule::unique('category');
        if ($this->id) {
            $rule->ignore($this->id);
        }
        $rules['name'][] = $rule;

        return $rules;
    }

    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        if ($this->parent == $this->id) {
            $this->parent = null;
        }
    }
}
