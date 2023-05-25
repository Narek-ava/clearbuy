<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'superadmin']);

        $user = User::create([
            'name' => 'superadmin',
            'email' => 'admin@example.com',
            'password' => Hash::make('superadmin')
        ]);

        $user->assignRole('superadmin');
    }
}
