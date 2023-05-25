<?php

namespace App\Services;

class SidebarLinksService
{
    public static function getLinks($active = '')
    {
        return collect([
            [
                'name' => 'Products',
                'items' => collect([
                    ['path' => '/admin/attribute_groups', 'name' => 'Attribute groups'],
                    ['path' => '/admin/attributes', 'name' => 'Attributes'],
                    ['path' => '/admin/ratings', 'name' => 'Ratings'],
                    ['path' => '/admin/categories', 'name' => 'Categories'],
                    ['path' => '/admin/products', 'name' => 'Products'],
                    ['path' => '/admin/deals', 'name' => 'Deals'],
                ])
            ],
            [
                'name' => 'Films/TV',
                'items' => collect([
                    ['path' => '/admin/film_genres', 'name' => 'Genres'],
                    ['path' => '/admin/age_ratings', 'name' => 'Age ratings'],
                    ['path' => '/admin/films', 'name' => 'Films'],
                    ['path' => '/admin/film_reviews', 'name' => 'Reviews'],
                    ['path' => '/admin/people', 'name' => 'People']
                ])
            ],
            [
                'name' => 'OS',
                'items' => collect([
                    ['path' => '/admin/licenses', 'name' => 'Licenses'],
                    ['path' => '/admin/oss', 'name' => 'OS'],
                ])
            ],
            [
                'name' => 'Users',
                'items' => collect([
                    ['path' => '/admin/roles', 'name' => 'Roles'],
                    ['path' => '/admin/users', 'name' => 'Users'],
                    ['path' => '/admin/auth_domains', 'name' => 'Allowed auth domains'],
                ])
            ],
            [
                'name' => 'Other',
                'items' => collect([
                    ['path' => '/admin/agents', 'name' => 'Agents'],
                    ['path' => '/admin/app_stores', 'name' => 'App stores'],
                    ['path' => '/admin/brands', 'name' => 'Brands'],
                    ['path' => '/admin/countries', 'name' => 'Countries'],
                    ['path' => '/admin/currencies', 'name' => 'Currencies'],
                    ['path' => '/admin/measures', 'name' => 'Measure units'],
                    ['path' => '/admin/websites', 'name' => 'Websites'],
                    ['path' => '/admin/domains', 'name' => 'Allowed domains'],
                    ['path' => '/admin/badges', 'name' => 'Award badges'],
                    ['path' => '/admin/tags', 'name' => 'Tags']
                ])
            ],
            [
                'name' => 'Apps',
                'path' => '/admin/apps',
            ],
        ])->map(function($item) use ($active) {
            $item = (object)$item;

            if(isset($item->items))
            {
                $item->items = $item->items->map(function($item) use ($active) {
                    $item = (object)$item;
                    $item->active = $item->path == $active;
                    return $item;
                });
                $item->active = $item->items->some(function($item) {
                    return $item->active;
                });

            }else{
                $item->active = $item->path == $active;
                return $item;
            }
            return $item;
        });
    }
}
