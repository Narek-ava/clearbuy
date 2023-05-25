<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * @param array $with
     * @return Builder[]|Collection
     */
    public function get(array $with = [])
    {
        return Category::query()->with($with)->get();
    }
}
