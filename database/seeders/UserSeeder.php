<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user')->insert([
            [
                'username' => 'admin',
                'password' => Hash::make('password'), // Pastikan Anda meng-hash password
                'email' => 'admin@example.com',
                'role_id' => 1, // Sesuaikan dengan id role Admin
                'status' => 'active',
                'profile_image' => null
            ],
            [
                'username' => 'user',
                'password' => Hash::make('password'), // Pastikan Anda meng-hash password
                'email' => 'user@example.com',
                'role_id' => 2, // Sesuaikan dengan id role User
                'status' => 'active',
                'profile_image' => null
            ],
        ]);
    }
}
