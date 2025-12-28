<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceSchedule;
use App\Models\ServicePhuong;
use App\Models\ServiceField;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Dịch vụ 1: Đăng ký khai sinh
        $service1 = Service::create([
            'ten_dich_vu' => 'Đăng ký khai sinh',
            'mo_ta' => 'Dịch vụ đăng ký khai sinh cho trẻ em mới sinh',
        ]);

        // Lịch dịch vụ (Thứ 2, 3, 4, 5, 6)
        for ($i = 1; $i <= 5; $i++) {
            ServiceSchedule::create([
                'dich_vu_id' => $service1->id,
                'thu_trong_tuan' => $i,
                'trang_thai' => true,
                'gio_bat_dau' => '08:00',
                'gio_ket_thuc' => '16:00',
                'so_luong_toi_da' => 20,
            ]);
        }

        // Cấu hình cho từng phường
        ServicePhuong::create([
            'dich_vu_id' => $service1->id,
            'don_vi_id' => 1,
            'thoi_gian_xu_ly' => 3,
            'so_luong_toi_da' => 20,
            'phi_dich_vu' => 0,
            'kich_hoat' => true,
        ]);

        ServicePhuong::create([
            'dich_vu_id' => $service1->id,
            'don_vi_id' => 2,
            'thoi_gian_xu_ly' => 3,
            'so_luong_toi_da' => 15,
            'phi_dich_vu' => 0,
            'kich_hoat' => true,
        ]);

        // Form fields cho dịch vụ
        ServiceField::create([
            'dich_vu_id' => $service1->id,
            'ten_truong' => 'ten_tre',
            'nhan_hien_thi' => 'Họ tên trẻ',
            'loai_truong' => 'text',
            'bat_buoc' => true,
            'thu_tu' => 1,
        ]);

        ServiceField::create([
            'dich_vu_id' => $service1->id,
            'ten_truong' => 'ngay_sinh',
            'nhan_hien_thi' => 'Ngày sinh',
            'loai_truong' => 'date',
            'bat_buoc' => true,
            'thu_tu' => 2,
        ]);

        ServiceField::create([
            'dich_vu_id' => $service1->id,
            'ten_truong' => 'giay_khai_sinh_bo_me',
            'nhan_hien_thi' => 'Giấy khai sinh bố mẹ',
            'loai_truong' => 'file',
            'bat_buoc' => true,
            'goi_y' => 'Upload file PDF hoặc ảnh',
            'thu_tu' => 3,
        ]);

        // Dịch vụ 2: Đăng ký thường trú
        $service2 = Service::create([
            'ten_dich_vu' => 'Đăng ký thường trú',
            'mo_ta' => 'Dịch vụ đăng ký hộ khẩu thường trú',
        ]);

        for ($i = 1; $i <= 5; $i++) {
            ServiceSchedule::create([
                'dich_vu_id' => $service2->id,
                'thu_trong_tuan' => $i,
                'trang_thai' => true,
                'gio_bat_dau' => '08:00',
                'gio_ket_thuc' => '16:00',
                'so_luong_toi_da' => 15,
            ]);
        }

        ServicePhuong::create([
            'dich_vu_id' => $service2->id,
            'don_vi_id' => 1,
            'thoi_gian_xu_ly' => 7,
            'so_luong_toi_da' => 15,
            'phi_dich_vu' => 50000,
            'kich_hoat' => true,
        ]);

        ServiceField::create([
            'dich_vu_id' => $service2->id,
            'ten_truong' => 'ho_ten',
            'nhan_hien_thi' => 'Họ và tên',
            'loai_truong' => 'text',
            'bat_buoc' => true,
            'thu_tu' => 1,
        ]);

        ServiceField::create([
            'dich_vu_id' => $service2->id,
            'ten_truong' => 'cmnd_cccd',
            'nhan_hien_thi' => 'CMND/CCCD',
            'loai_truong' => 'file',
            'bat_buoc' => true,
            'thu_tu' => 2,
        ]);

        // Dịch vụ 3: Cấp giấy xác nhận độc thân
        $service3 = Service::create([
            'ten_dich_vu' => 'Cấp giấy xác nhận độc thân',
            'mo_ta' => 'Dịch vụ cấp giấy xác nhận tình trạng hôn nhân',
        ]);

        for ($i = 1; $i <= 5; $i++) {
            ServiceSchedule::create([
                'dich_vu_id' => $service3->id,
                'thu_trong_tuan' => $i,
                'trang_thai' => true,
                'gio_bat_dau' => '08:00',
                'gio_ket_thuc' => '16:00',
                'so_luong_toi_da' => 10,
            ]);
        }

        ServicePhuong::create([
            'dich_vu_id' => $service3->id,
            'don_vi_id' => 1,
            'thoi_gian_xu_ly' => 1,
            'so_luong_toi_da' => 10,
            'phi_dich_vu' => 20000,
            'kich_hoat' => true,
        ]);

        ServicePhuong::create([
            'dich_vu_id' => $service3->id,
            'don_vi_id' => 2,
            'thoi_gian_xu_ly' => 1,
            'so_luong_toi_da' => 10,
            'phi_dich_vu' => 20000,
            'kich_hoat' => true,
        ]);

        ServiceField::create([
            'dich_vu_id' => $service3->id,
            'ten_truong' => 'cmnd_cccd',
            'nhan_hien_thi' => 'CMND/CCCD',
            'loai_truong' => 'file',
            'bat_buoc' => true,
            'thu_tu' => 1,
        ]);
    }
}
