<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class DealsRequest extends FormRequest
{
    public function rules() : array
    {
        return [
            'page' => 'required|integer',
            'posts_per_page' => 'required|integer',
            'websites' => 'sometimes|nullable|array',
            'websites.*' => 'string',
            'categories' => 'sometimes|nullable|array',
            'categories.*' => 'numeric',
        ];
    }
}
