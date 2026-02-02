<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminPhuongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin phường 1 - Phường Hòa Cường
        Admin::create([
            'ho_ten' => 'Admin Phường Hòa Cường',
            'ten_dang_nhap' => 'adminphuong1',
            'mat_khau' => Hash::make('123456'),
            'email' => 'adminphuong1@example.com',
            'so_dien_thoai' => '0901234561',
            'quyen' => 2, // Admin phường
            'don_vi_id' => 7,
        ]);

        // Admin phường 2 - Phường Thanh Khê
        Admin::create([
            'ho_ten' => 'Admin Phường Thanh Khê',
            'ten_dang_nhap' => 'adminphuong2',
            'mat_khau' => Hash::make('123456'),
            'email' => 'adminphuong2@example.com',
            'so_dien_thoai' => '0901234562',
            'quyen' => 2, // Admin phường
            'don_vi_id' => 8,
        ]);

        // Admin phường 3 - Phường An Khê
        Admin::create([
            'ho_ten' => 'Admin Phường An Khê',
            'ten_dang_nhap' => 'adminphuong3',
            'mat_khau' => Hash::make('123456'),
            'email' => 'adminphuong3@example.com',
            'so_dien_thoai' => '0901234563',
            'quyen' => 2, // Admin phường
            'don_vi_id' => 9,
        ]);

        // Admin phường 4 - Phường An Hải
        Admin::create([
            'ho_ten' => 'Admin Phường An Hải',
            'ten_dang_nhap' => 'adminphuong4',
            'mat_khau' => Hash::make('123456'),
            'email' => 'adminphuong4@example.com',
            'so_dien_thoai' => '0901234564',
            'quyen' => 2, // Admin phường
            'don_vi_id' => 10,
        ]);

        // Admin phường 5 - Phường Sơn Trà
        Admin::create([
            'ho_ten' => 'Admin Phường Sơn Trà',
            'ten_dang_nhap' => 'adminphuong5',
            'mat_khau' => Hash::make('123456'),
            'email' => 'adminphuong5@example.com',
            'so_dien_thoai' => '0901234565',
            'quyen' => 2, // Admin phường
            'don_vi_id' => 11,
        ]);

        // Admin phường 6 - Phường Ngũ Hành Sơn
        Admin::create([
            'ho_ten' => 'Admin Phường Ngũ Hành Sơn',
            'ten_dang_nhap' => 'adminphuong6',
            'mat_khau' => Hash::make('123456'),
            'email' => 'adminphuong6@example.com',
            'so_dien_thoai' => '0901234566',
            'quyen' => 2, // Admin phường
            'don_vi_id' => 12,
        ]);

        // Admin phường 7 - Phường Hòa Khánh
        Admin::create([
            'ho_ten' => 'Admin Phường Hòa Khánh',
            'ten_dang_nhap' => 'adminphuong7',
            'mat_khau' => Hash::make('123456'),
            'email' => 'adminphuong7@example.com',
            'so_dien_thoai' => '0901234567',
            'quyen' => 2, // Admin phường
            'don_vi_id' => 13,
        ]);

        // Admin phường 8 - Phường Liên Chiểu
        Admin::create([
            'ho_ten' => 'Admin Phường Liên Chiểu',
            'ten_dang_nhap' => 'adminphuong8',
            'mat_khau' => Hash::make('123456'),
            'email' => 'adminphuong8@example.com',
            'so_dien_thoai' => '0901234568',
            'quyen' => 2, // Admin phường
            'don_vi_id' => 14,
        ]);

        // Admin phường 9 - Phường Cẩm Lệ
        Admin::create([
            'ho_ten' => 'Admin Phường Cẩm Lệ',
            'ten_dang_nhap' => 'adminphuong9',
            'mat_khau' => Hash::make('123456'),
            'email' => 'adminphuong9@example.com',
            'so_dien_thoai' => '0901234569',
            'quyen' => 2, // Admin phường
            'don_vi_id' => 15,
        ]);

        // Admin phường 10 - Phường Hòa Xuân
        Admin::create([
            'ho_ten' => 'Admin Phường Hòa Xuân',
            'ten_dang_nhap' => 'adminphuong10',
            'mat_khau' => Hash::make('123456'),
            'email' => 'adminphuong10@example.com',
            'so_dien_thoai' => '0901234570',
            'quyen' => 2, // Admin phường
            'don_vi_id' => 16,
        ]);

        // Admin phường 11 - Phường Hòa Vang
        Admin::create([
            'ho_ten' => 'Admin Phường Hòa Vang',
            'ten_dang_nhap' => 'adminphuong11',
            'mat_khau' => Hash::make('123456'),
            'email' => 'adminphuong11@example.com',
            'so_dien_thoai' => '0901234571',
            'quyen' => 2, // Admin phường
            'don_vi_id' => 17,
        ]);

        // Admin phường 12 - Phường Hòa Tiến
        Admin::create([
            'ho_ten' => 'Admin Phường Hòa Tiến',
            'ten_dang_nhap' => 'adminphuong12',
            'mat_khau' => Hash::make('123456'),
            'email' => 'adminphuong12@example.com',
            'so_dien_thoai' => '0901234572',
            'quyen' => 2, // Admin phường
            'don_vi_id' => 18,
        ]);
    }
}
