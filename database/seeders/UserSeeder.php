<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Define the roles you want to create
        $roles = ['superadmin', 'admin', 'user'];

        // Create roles if they don't exist
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Create the superadmin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@app.com'],
            [
                'name'     => 'Super Admin',
                'password' => bcrypt('Niaz@Superadmin') // Use a secure password in production!
            ]
        );

        // Assign the superadmin role to the user
        $superAdmin->assignRole('superadmin');
    }
}
