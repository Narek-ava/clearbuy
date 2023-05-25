<?php

namespace App\Http\Requests\App;

use App\Http\Requests\BaseSaveRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends BaseSaveRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|integer|min:0|max:1',
            'brand' => 'required|integer|min:1',
            'os' => 'sometimes|nullable|array|distinct',
            'os.*' => 'integer|min:1',
            'countries' => 'sometimes|nullable|array|distinct',
            'countries.*' => 'integer|min:1',
            'change_log_url' => 'sometimes|nullable|string|url|max:255',
            'links' => 'sometimes|array',
            'links.*.free' => 'sometimes|boolean',
            'links.*.app_purchase' => 'sometimes|boolean',
            'links.*.price' => 'sometimes|integer|min:1',
            'links.*.currency_id' => 'required_with:links.*.price|integer|min:1',
            'links.*.store_id' => 'required|integer|min:1',
            'links.*.url' => 'required|string|url|max:255',
            'images' => 'sometimes|array|distinct',
            'logo' => 'sometimes|nullable|string',
            'video_url' => 'sometimes|nullable|string|url|max:255',
            'description' => 'sometimes|nullable|string|max:512',
        ];

        $rule = Rule::unique('app');
        if ($this->id) {
            $rule->ignore($this->id);
        }
        //$rules['name'][] = $rule;

        return $rules;
    }
}
