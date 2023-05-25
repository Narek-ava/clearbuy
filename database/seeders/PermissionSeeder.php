<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $entities = [
            'roles',
            'users',
            'countries',
            'brands',
            'measures',
            'agents',
            'currencies',
            'attribute_groups',
            'attributes',
            'categories',
            'products',
            'websites',
            'licenses',
            'oss',
            'film_genres',
            'age_ratings',
            'films',
            'film_reviews',
            'apps'
        ];

        $actions = ['view', 'update', 'delete'];

        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                Permission::create(['name' => $action.' '.$entity]);
            }
        }

        if(!Permission::where(['name' => 'use admin panel'])->get()) {
            Permission::create(['name' => 'use admin panel']);
        }
    }
}
