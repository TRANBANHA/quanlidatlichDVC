<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Dữ liệu cũ
            PostSeeder::class,
            AboutSeeder::class,
            ContactSeeder::class,
            
            // Dữ liệu mới cho hệ thống đặt lịch
            DonViSeeder::class,              // Tạo phường trước
            FakePhuongDuongSoNhaSeeder::class, // Tạo phường, đường, số nhà (cho form đăng ký)
            AdminSeeder::class,              // Tạo admin, cán bộ
            FakeServiceDataSeeder::class,    // Tạo dịch vụ, lịch, form (theo quy tắc mới: 1-2 thứ/dịch vụ)
            UserSeeder::class,               // Tạo người dùng
            FakeScheduleStaffSeeder::class,  // Phân công cán bộ cho lịch dịch vụ
            FakeHoSoSeeder::class,           // Tạo hồ sơ đặt lịch fake
        ]);
    }
}
