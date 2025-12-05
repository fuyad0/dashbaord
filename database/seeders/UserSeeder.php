<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'Admin',
                'email' => 'admin@admin.com',
                'email_verified_at' => now(),
                'password' => '12345678',
                'role' => 'Admin',
            ],
            [
                'first_name' => 'User',
                'email' => 'user@gmail.com',
                'email_verified_at' => now(),
                'password' => '12345678',
                'role' => 'User',
            ],
        ];

        foreach ($users as $user) {
            User::create([
                'first_name' => $user['first_name'],
                'email' => $user['email'],
                'email_verified_at' => $user['email_verified_at'],
                'password' => Hash::make($user['password']),
                'role' => $user['role'],
            ]);
        }
    }
}
