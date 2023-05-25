<?php

namespace App\Http\Requests\License;

use App\Http\Requests\BaseGetFormRequest;
use Illuminate\Validation\Rule;

class GetFormRequest extends BaseGetFormRequest
{
    protected function prepareForValidation()
    {
        if (!$this->backUrl) {
            $this->backUrl = '/admin/licenses';
        }
    }
}
