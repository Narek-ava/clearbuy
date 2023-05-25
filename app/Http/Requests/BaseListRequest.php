<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BaseListRequest extends FormRequest
{
    protected function allowedSorts() : \Illuminate\Support\Collection
    {
        return collect(['id']);
    }

    protected function defaultSort() : string
    {
        return 'id';
    }

    public function allowedPerPages() : \Illuminate\Support\Collection
    {
        return collect([10, 20, 50, 100, 200]);
    }

    protected function defaultPerPage() : int
    {
        return 50;
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }

    protected function prepareSorts()
    {
        if (!$this->allowedSorts()->contains($this->sort)) {
            $this->sort = $this->defaultSort();
        }
    }

    protected function prepareSortOrder()
    {
        if(!isset($this->order)) $this->order = 'DESC'; //order by default
    }

    protected function preparePerPage()
    {
        if (!$this->allowedPerPages()->contains($this->perPage) || $this->perPage === null) {
            $this->merge([
                'perPage' => $this->defaultPerPage()
            ]);
        }
    }

    protected function prepareForValidation()
    {
        $this->prepareSorts();
        $this->prepareSortOrder();
        $this->preparePerPage();
    }
}
