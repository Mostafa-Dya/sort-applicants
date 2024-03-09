<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create a user with ADMIN role
        $user = User::create([
            'name' => 'Admin Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('P@$$W0RD'), // You can replace 'adminpassword' with the desired password
            'role' => 'Admin',
        ]);

        // Create permissions for the ADMIN user
        Permissions::create([
            'user_id' => $user->id,
            'create' => true,
            'read' => true,
            'update' => true,
            'delete' => true,
        ]);
    }
}
