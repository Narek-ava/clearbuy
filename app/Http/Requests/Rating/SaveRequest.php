<?php

namespace App\Http\Requests\Rating;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255|unique:ratings,name,' . $this->id,
            'sort_order' => 'sometimes|nullable|integer',
        ];

        return $rules;
    }
}
