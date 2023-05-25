<?php

namespace App\Http\Requests\Agent;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'image' => 'sometimes|nullable|string',
            'website' => 'sometimes|string|nullable|url|max:255',
            'is_retailer' => 'required|boolean',
            'type_id' => 'required|integer|min:0|max:1',
            'surname' => 'sometimes|nullable|string|max:255',
            'countries' => 'required|array',
            'countries.*' => 'not_in:-- select --',
        ];

        return $rules;
    }

    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge([
            'is_retailer' => $this->is_retailer ? 1: 0
        ]);
    }
}
