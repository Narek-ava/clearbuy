<?php

namespace App\Http\Requests\Attribute;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'sort_order' => 'sometimes|nullable|integer',
            'group' => 'required|integer|min:1',
            'type' => 'required|integer|min:0|max:6',
            'kind' => 'required|integer|min:1|max:3',
            'name' => ['required', 'string', 'max:255'],
        ];

        // $rule = Rule::unique('attribute');
        // if ($this->id) {
        //     $rule->ignore($this->id);
        // }
        // //$rules['name'][] = $rule;
        //
        // if ($this->type == 4 || $this->type == 5) {
        //     $rules['options'] = 'required|array|distinct';
        //     $rules['options.*.name'] = 'string|max:255';
        //     $rules['options.*.id'] = 'integer';
        // }
        // if ($this->type == 0) {
        //     $rules['measure'] = 'required|integer';
        // }

        return $rules;
    }

    protected function prepareForValidation()
    {
        parent::prepareForValidation();

        $this->merge([
            'options' => collect($this->options)->map(function ($item, $index) {
                    if (is_string($index)) {
                        return ['id' => intval(str_replace('id_', '', $index)), 'name' => $item];
                    } else {
                        return ['id' => 0, 'name' => $item];
                    }
                })->filter(function($item) {
                    return strlen($item['name']) > 0;
                })->values()->unique('name')->toArray()
        ]);
    }
}
