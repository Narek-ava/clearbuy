<?php

namespace App\Services;

use App\Models\Website;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class WebsiteService
{
    /**
     * @param array $with
     * @return Builder[]|Collection
     */
    public function get(array $with = [])
    {
        return Website::query()->with($with)->get();
    }
}
