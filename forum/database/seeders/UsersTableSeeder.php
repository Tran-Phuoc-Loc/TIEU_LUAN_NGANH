<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Thêm dữ liệu mẫu vào bảng users
        DB::table('users')->insert([
            [
                'username' => 'john_doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password123'),
                'profile_picture' => '',
            ]
        ]);
    }
}
