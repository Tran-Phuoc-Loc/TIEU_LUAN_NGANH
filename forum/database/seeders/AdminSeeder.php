<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo vai trò admin nếu chưa tồn tại
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Tạo người dùng admin
        $adminUser = User::firstOrCreate([
            'email' => 'ttp6889@gmail.com',
        ], [
            'username' => 'adminuser',
            'password' => bcrypt('adminpassword1'), // Mật khẩu đã mã hóa
            'status' => 'active',
            'role' => 'admin',
        ]);

        // Gán vai trò admin cho người dùng
        $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}
