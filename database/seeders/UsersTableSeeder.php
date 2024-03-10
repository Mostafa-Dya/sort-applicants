<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create a user with ADMIN role
        $user = User::create([
            'name' => 'Admin',
            'email' => 'Admin',
            'password' => bcrypt('P@$$W0RD'), // You can replace 'adminpassword' with the desired password
            'role' => 'Admin',
        ]);

    }
}
