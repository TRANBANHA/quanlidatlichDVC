<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class CanboPhuongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'ho_ten' => 'Trần Thị Ái',
            'ten_dang_nhap' => 'tranthiai',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo123@example.com',
            'so_dien_thoai' => '0907000001',
            'quyen' => 0,
            'don_vi_id' => 7,
        ]);

        Admin::create([
            'ho_ten' => 'Nguyễn Văn Cảnh',
            'ten_dang_nhap' => 'nguyenvancanh',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo223@example.com',
            'so_dien_thoai' => '0907000002',
            'quyen' => 0,
            'don_vi_id' => 7,
        ]);

        Admin::create([
            'ho_ten' => 'Lê Thị D',
            'ten_dang_nhap' => 'lethid',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo312@example.com',
            'so_dien_thoai' => '0907000003',
            'quyen' => 0,
            'don_vi_id' => 7,
        ]);

        // ===== Phường Thanh Khê (don_vi_id = 8) =====
        Admin::create([
            'ho_ten' => 'Phạm Văn E',
            'ten_dang_nhap' => 'phamvane',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo4342@example.com',
            'so_dien_thoai' => '0908000001',
            'quyen' => 0,
            'don_vi_id' => 8,
        ]);

        Admin::create([
            'ho_ten' => 'Võ Thị F',
            'ten_dang_nhap' => 'vothif',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo512@example.com',
            'so_dien_thoai' => '0908000002',
            'quyen' => 0,
            'don_vi_id' => 8,
        ]);

        Admin::create([
            'ho_ten' => 'Đặng Văn G',
            'ten_dang_nhap' => 'dangvang',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo612@example.com',
            'so_dien_thoai' => '0908000003',
            'quyen' => 0,
            'don_vi_id' => 8,
        ]);

        // ===== Phường An Khê (don_vi_id = 9) =====
        Admin::create([
            'ho_ten' => 'Trần Văn H',
            'ten_dang_nhap' => 'tranvanh',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo743@example.com',
            'so_dien_thoai' => '0909000001',
            'quyen' => 0,
            'don_vi_id' => 9,
        ]);

        Admin::create([
            'ho_ten' => 'Nguyễn Thị I',
            'ten_dang_nhap' => 'nguyenthi',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo845@example.com',
            'so_dien_thoai' => '0909000002',
            'quyen' => 0,
            'don_vi_id' => 9,
        ]);

        Admin::create([
            'ho_ten' => 'Lê Văn K',
            'ten_dang_nhap' => 'levank',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo923@example.com',
            'so_dien_thoai' => '0909000003',
            'quyen' => 0,
            'don_vi_id' => 9,
        ]);

        // ===== Phường An Hải (don_vi_id = 10) =====
        Admin::create([
            'ho_ten' => 'Phan Thị L',
            'ten_dang_nhap' => 'phanthil',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo10@example.com',
            'so_dien_thoai' => '0910000001',
            'quyen' => 0,
            'don_vi_id' => 10,
        ]);

        Admin::create([
            'ho_ten' => 'Ngô Văn M',
            'ten_dang_nhap' => 'ngovanm',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo11@example.com',
            'so_dien_thoai' => '0910000002',
            'quyen' => 0,
            'don_vi_id' => 10,
        ]);

        Admin::create([
            'ho_ten' => 'Bùi Thị N',
            'ten_dang_nhap' => 'buithin',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo12@example.com',
            'so_dien_thoai' => '0910000003',
            'quyen' => 0,
            'don_vi_id' => 10,
        ]);

        // ===== Phường Sơn Trà (don_vi_id = 11) =====
        Admin::create([
            'ho_ten' => 'Huỳnh Văn O',
            'ten_dang_nhap' => 'huynhvano',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo13@example.com',
            'so_dien_thoai' => '0911000001',
            'quyen' => 0,
            'don_vi_id' => 11,
        ]);

        Admin::create([
            'ho_ten' => 'Đỗ Thị P',
            'ten_dang_nhap' => 'dothip',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo14@example.com',
            'so_dien_thoai' => '0911000002',
            'quyen' => 0,
            'don_vi_id' => 11,
        ]);

        Admin::create([
            'ho_ten' => 'Mai Văn Q',
            'ten_dang_nhap' => 'maivanq',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo15@example.com',
            'so_dien_thoai' => '0911000003',
            'quyen' => 0,
            'don_vi_id' => 11,
        ]);

        // ===== Phường Ngũ Hành Sơn (don_vi_id = 12) =====
        Admin::create([
            'ho_ten' => 'Lý Thị R',
            'ten_dang_nhap' => 'lythir',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo16@example.com',
            'so_dien_thoai' => '0912000001',
            'quyen' => 0,
            'don_vi_id' => 12,
        ]);

        Admin::create([
            'ho_ten' => 'Cao Văn S',
            'ten_dang_nhap' => 'caovans',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo17@example.com',
            'so_dien_thoai' => '0912000002',
            'quyen' => 0,
            'don_vi_id' => 12,
        ]);

        Admin::create([
            'ho_ten' => 'Tạ Thị T',
            'ten_dang_nhap' => 'tathit',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo18@example.com',
            'so_dien_thoai' => '0912000003',
            'quyen' => 0,
            'don_vi_id' => 12,
        ]);

        // ===== Phường Hòa Khánh (don_vi_id = 13) =====
        Admin::create([
            'ho_ten' => 'Trương Văn U',
            'ten_dang_nhap' => 'truongvanu',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo19@example.com',
            'so_dien_thoai' => '0913000001',
            'quyen' => 0,
            'don_vi_id' => 13,
        ]);

        Admin::create([
            'ho_ten' => 'Đinh Thị V',
            'ten_dang_nhap' => 'dinhthiv',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo20@example.com',
            'so_dien_thoai' => '0913000002',
            'quyen' => 0,
            'don_vi_id' => 13,
        ]);

        Admin::create([
            'ho_ten' => 'Hà Văn X',
            'ten_dang_nhap' => 'havannx',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo21@example.com',
            'so_dien_thoai' => '0913000003',
            'quyen' => 0,
            'don_vi_id' => 13,
        ]);

        // ===== Phường Liên Chiểu (don_vi_id = 14) =====
        Admin::create([
            'ho_ten' => 'Phùng Thị Y',
            'ten_dang_nhap' => 'phungthiy',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo22@example.com',
            'so_dien_thoai' => '0914000001',
            'quyen' => 0,
            'don_vi_id' => 14,
        ]);

        Admin::create([
            'ho_ten' => 'La Văn Z',
            'ten_dang_nhap' => 'lavanz',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo23@example.com',
            'so_dien_thoai' => '0914000002',
            'quyen' => 0,
            'don_vi_id' => 14,
        ]);

        Admin::create([
            'ho_ten' => 'Trịnh Thị A',
            'ten_dang_nhap' => 'trinhthia',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo24@example.com',
            'so_dien_thoai' => '0914000003',
            'quyen' => 0,
            'don_vi_id' => 14,
        ]);

        // ===== Phường Cẩm Lệ (don_vi_id = 15) =====
        Admin::create([
            'ho_ten' => 'Phạm Văn B',
            'ten_dang_nhap' => 'phamvanb',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo25@example.com',
            'so_dien_thoai' => '0915000001',
            'quyen' => 0,
            'don_vi_id' => 15,
        ]);

        Admin::create([
            'ho_ten' => 'Hoàng Thị C',
            'ten_dang_nhap' => 'hoangthic',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo26@example.com',
            'so_dien_thoai' => '0915000002',
            'quyen' => 0,
            'don_vi_id' => 15,
        ]);

        Admin::create([
            'ho_ten' => 'Vương Văn D',
            'ten_dang_nhap' => 'vuongvand',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo27@example.com',
            'so_dien_thoai' => '0915000003',
            'quyen' => 0,
            'don_vi_id' => 15,
        ]);

        // ===== Phường Hòa Xuân (don_vi_id = 16) =====
        Admin::create([
            'ho_ten' => 'Đoàn Thị E',
            'ten_dang_nhap' => 'doanthie',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo28@example.com',
            'so_dien_thoai' => '0916000001',
            'quyen' => 0,
            'don_vi_id' => 16,
        ]);

        Admin::create([
            'ho_ten' => 'Lâm Văn F',
            'ten_dang_nhap' => 'lamvanf',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo29@example.com',
            'so_dien_thoai' => '0916000002',
            'quyen' => 0,
            'don_vi_id' => 16,
        ]);

        Admin::create([
            'ho_ten' => 'Kiều Thị G',
            'ten_dang_nhap' => 'kieuthig',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo30@example.com',
            'so_dien_thoai' => '0916000003',
            'quyen' => 0,
            'don_vi_id' => 16,
        ]);

        // ===== Phường Hòa Vang (don_vi_id = 17) =====
        Admin::create([
            'ho_ten' => 'Thạch Văn H',
            'ten_dang_nhap' => 'thachvanh',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo31@example.com',
            'so_dien_thoai' => '0917000001',
            'quyen' => 0,
            'don_vi_id' => 17,
        ]);

        Admin::create([
            'ho_ten' => 'Tôn Thị I',
            'ten_dang_nhap' => 'tonthii',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo32@example.com',
            'so_dien_thoai' => '0917000002',
            'quyen' => 0,
            'don_vi_id' => 17,
        ]);

        Admin::create([
            'ho_ten' => 'Mạc Văn K',
            'ten_dang_nhap' => 'macvank',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo33@example.com',
            'so_dien_thoai' => '0917000003',
            'quyen' => 0,
            'don_vi_id' => 17,
        ]);

        // ===== Phường Hòa Tiến (don_vi_id = 18) =====
        Admin::create([
            'ho_ten' => 'Quách Thị L',
            'ten_dang_nhap' => 'quachthil',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo34@example.com',
            'so_dien_thoai' => '0918000001',
            'quyen' => 0,
            'don_vi_id' => 18,
        ]);

        Admin::create([
            'ho_ten' => 'Triệu Văn M',
            'ten_dang_nhap' => 'trieuvanmm',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo35@example.com',
            'so_dien_thoai' => '0918000002',
            'quyen' => 0,
            'don_vi_id' => 18,
        ]);

        Admin::create([
            'ho_ten' => 'Từ Thị N',
            'ten_dang_nhap' => 'tuthin',
            'mat_khau' => Hash::make('123456'),
            'email' => 'canbo36@example.com',
            'so_dien_thoai' => '0918000003',
            'quyen' => 0,
            'don_vi_id' => 18,
        ]);
    }
}
