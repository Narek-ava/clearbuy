<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BaseSaveRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (!$this->id || !preg_match('/^[0-9]+$/', $this->id)) {
            $this->merge([
                'id' => null
            ]);
        }
    }
}
