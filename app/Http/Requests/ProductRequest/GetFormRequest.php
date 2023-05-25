<?php

namespace App\Http\Requests\ProductRequest;

use App\Http\Requests\BaseGetFormRequest;
use Illuminate\Validation\Rule;

class GetFormRequest extends BaseGetFormRequest
{
    protected function prepareForValidation()
    {
        if (!$this->backUrl) {
            $this->backUrl = '/admin/product_request';
        }
    }
}
