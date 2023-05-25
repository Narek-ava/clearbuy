<?php

namespace App\Http\Requests\People;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:64',
            'surname' => 'required|string|max:128',
        ];

        return $rules;
    }

    protected function prepareForValidation()
    {
        parent::prepareForValidation();

    }
}
