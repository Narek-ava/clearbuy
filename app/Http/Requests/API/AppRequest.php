<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class AppRequest extends FormRequest
{
    public function rules() : array
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'numeric',
        ];
    }
}
