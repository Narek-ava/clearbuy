<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseListRequest;
use Illuminate\Validation\Rule;

class ListRequest extends BaseListRequest
{
    public function rules()
    {
        return [
            'name' => 'sometimes|max:255',
            'email' => 'sometimes|max:255',
            'page' => 'sometimes|integer',
            'perPage' => 'sometimes|integer',
            'roles' => 'sometimes|array',
            'roles.*' => 'integer'
        ];
    }

    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id', 'name', 'email']);
    }
}
