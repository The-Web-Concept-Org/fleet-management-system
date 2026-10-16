<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'super_admin',
                'status' => 'active',
                'permissions' => null,
            ],
            
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
