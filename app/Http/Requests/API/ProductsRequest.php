<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ProductsRequest extends FormRequest
{
    // public function authorize()
    // {
    //     return true;
    // }

    public function rules() : array
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'numeric',
            'type' => 'sometimes|string',
        ];
    }
}
