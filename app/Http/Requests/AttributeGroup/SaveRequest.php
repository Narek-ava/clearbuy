<?php

namespace App\Http\Requests\AttributeGroup;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'sort_order' => 'sometimes|nullable|integer',
            'name' => 'required|string|max:255'
        ];

        // $rule = Rule::unique('attribute_group');
        // if ($this->id) {
        //     $rule->ignore($this->id);
        // }
        //$rules['name'][] = $rule;

        return $rules;
    }
}
