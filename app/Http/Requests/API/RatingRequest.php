<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
{
    public function rules() : array
    {
        return [
            'product_id' => 'required|integer',
            'rating_name' => 'required|string',
            'rating_value' => 'required|integer',
        ];
    }
}
