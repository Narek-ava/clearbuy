<?php

namespace App\Http\Requests\AppStore;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'required|max:255',
            'brand' => 'required|integer|min:1',
            'url' => 'required|string',
            'icon' => 'sometimes|nullable|string',
        ];

        return $rules;
    }

    protected function prepareForValidation()
    {
        parent::prepareForValidation();
    }
}
