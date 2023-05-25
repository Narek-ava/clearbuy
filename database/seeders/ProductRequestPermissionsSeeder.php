<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProductRequestPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actions = ['view', 'update', 'delete'];

        foreach ($actions as $action) {
            $permission = Permission::where('name', $action.' {product_id}/request_success')->first();

            if($permission) continue;

            Permission::create(['name' => $action.' {product_id}/request_success']);
        }

        $role = Role::firstOrCreate(['name' => 'Product editors', 'guard_name' => 'web']);
        $role->givePermissionTo([
            'view {product_id}/request_success',
            'update {product_id}/request_success'
        ]);
    }
}
