<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'ten' => 'Nguyễn Văn An',
                'email' => 'nguyenvanan@example.com',
                'so_dien_thoai' => '0912345678',
                'cccd' => '001234567890',
                'mat_khau' => Hash::make('123456'),
                'tinh_trang' => 1,
                'don_vi_id' => 1,
                'dia_chi' => '123 Đường A, Quận 1',
                'code' => null,
            ],
            [
                'ten' => 'Trần Thị Bình',
                'email' => 'tranthibinh@example.com',
                'so_dien_thoai' => '0912345679',
                'cccd' => '001234567891',
                'mat_khau' => Hash::make('123456'),
                'tinh_trang' => 1,
                'don_vi_id' => 1,
                'dia_chi' => '456 Đường B, Quận 1',
                'code' => null,
            ],
            [
                'ten' => 'Lê Văn Cường',
                'email' => 'levancuong@example.com',
                'so_dien_thoai' => '0912345680',
                'cccd' => '001234567892',
                'mat_khau' => Hash::make('123456'),
                'tinh_trang' => 1,
                'don_vi_id' => 2,
                'dia_chi' => '789 Đường C, Quận 1',
                'code' => null,
            ],
            [
                'ten' => 'Phạm Thị Dung',
                'email' => 'phamthidung@example.com',
                'so_dien_thoai' => '0912345681',
                'cccd' => '001234567893',
                'mat_khau' => Hash::make('123456'),
                'tinh_trang' => 1,
                'don_vi_id' => 2,
                'dia_chi' => '12 Đường D, Quận 1',
                'code' => null,
            ],
            [
                'ten' => 'Hoàng Văn Em',
                'email' => 'hoangvanem@example.com',
                'so_dien_thoai' => '0912345682',
                'cccd' => '001234567894',
                'mat_khau' => Hash::make('123456'),
                'tinh_trang' => 1,
                'don_vi_id' => 3,
                'dia_chi' => '34 Đường E, Quận 1',
                'code' => null,
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
