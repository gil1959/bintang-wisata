<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // bikin role
        $adminRole  = Role::firstOrCreate(['name' => 'admin']);
        $staffRole  = Role::firstOrCreate(['name' => 'staff']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // bikin user admin pertama (kalau belum ada)
        $admin = User::firstOrCreate(
            ['email' => 'admin@bintangwisata.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'), // ganti nanti
            ]
        );

        if (! $admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }
    }
}
