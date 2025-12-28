<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Admin tổng
        Admin::create([
            'ho_ten' => 'Admin Tổng',
            'ten_dang_nhap' => 'admin',
            'mat_khau' => Hash::make('123456'),
            'email' => 'admin@example.com',
            'so_dien_thoai' => '0901234567',
            'quyen' => 1, // Admin tổng
            'don_vi_id' => null,
        ]);

        // Admin phường 1
        Admin::create([
            'ho_ten' => 'Nguyễn Văn A',
            'ten_dang_nhap' => 'adminphuong1',
            'mat_khau' => Hash::make('123456'),
            'email' => 'adminphuong1@example.com',
            'so_dien_thoai' => '0901234568',
            'quyen' => 2, // Admin phường
            'don_vi_id' => 1,
        ]);

        // Cán bộ phường 1
        Admin::create([
            'ho_ten' => 'Trần Thị B',
            'ten_dang_nhap' => 'canbo1',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo1@example.com',
            'so_dien_thoai' => '0901234569',
            'quyen' => 0, // Cán bộ
            'don_vi_id' => 1,
        ]);

        Admin::create([
            'ho_ten' => 'Lê Văn C',
            'ten_dang_nhap' => 'canbo2',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo2@example.com',
            'so_dien_thoai' => '0901234570',
            'quyen' => 0, // Cán bộ
            'don_vi_id' => 1,
        ]);

        // Admin phường 2
        Admin::create([
            'ho_ten' => 'Phạm Thị D',
            'ten_dang_nhap' => 'adminphuong2',
            'mat_khau' => Hash::make('123456'),
            'email' => 'adminphuong2@example.com',
            'so_dien_thoai' => '0901234571',
            'quyen' => 2, // Admin phường
            'don_vi_id' => 2,
        ]);

        // Cán bộ phường 2
        Admin::create([
            'ho_ten' => 'Hoàng Văn E',
            'ten_dang_nhap' => 'canbo3',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo3@example.com',
            'so_dien_thoai' => '0901234572',
            'quyen' => 0, // Cán bộ
            'don_vi_id' => 2,
        ]);
    }
}
