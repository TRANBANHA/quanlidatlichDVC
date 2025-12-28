<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceSchedule;
use App\Models\ServicePhuong;
use App\Models\ServiceField;
use App\Models\DonVi;

class FakeServiceDataSeeder extends Seeder
{
    /**
     * Run the database seeds để tạo fake data cho dịch vụ
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ (nếu muốn)
        // Service::truncate();
        // ServiceSchedule::truncate();
        // ServicePhuong::truncate();
        // ServiceField::truncate();

        // Lấy danh sách phường
        $donVis = DonVi::all();
        if ($donVis->isEmpty()) {
            $this->command->warn('Chưa có phường nào. Vui lòng chạy DonViSeeder trước!');
            return;
        }

        // ========== DỊCH VỤ 1: Đăng ký khai sinh ==========
        $service1 = Service::firstOrCreate(
            ['ten_dich_vu' => 'Đăng ký khai sinh'],
            [
                'mo_ta' => 'Dịch vụ đăng ký khai sinh cho trẻ em mới sinh. Thời gian xử lý: 3-5 ngày làm việc.',
            ]
        );

        // Lịch dịch vụ: Chỉ Thứ 2 (1 thứ trong tuần)
        ServiceSchedule::firstOrCreate(
            [
                'dich_vu_id' => $service1->id,
                'thu_trong_tuan' => 1, // Thứ 2
                'gio_bat_dau' => '08:00',
            ],
            [
                'trang_thai' => true,
                'gio_ket_thuc' => '17:00',
                'so_luong_toi_da' => 20,
            ]
        );

        // Cấu hình cho từng phường
        foreach ($donVis as $index => $donVi) {
            ServicePhuong::firstOrCreate(
                [
                    'dich_vu_id' => $service1->id,
                    'don_vi_id' => $donVi->id,
                ],
                [
                    'thoi_gian_xu_ly' => 3,
                    'so_luong_toi_da' => 15 + ($index * 5),
                    'phi_dich_vu' => 0,
                    'kich_hoat' => true,
                    'ghi_chu' => 'Miễn phí đăng ký khai sinh',
                ]
            );
        }

        // Form fields cho dịch vụ khai sinh
        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service1->id, 'ten_truong' => 'ten_tre'],
            [
                'nhan_hien_thi' => 'Họ và tên trẻ',
                'loai_truong' => 'text',
                'bat_buoc' => true,
                'placeholder' => 'Nhập họ và tên đầy đủ của trẻ',
                'thu_tu' => 1,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service1->id, 'ten_truong' => 'ngay_sinh'],
            [
                'nhan_hien_thi' => 'Ngày sinh của trẻ',
                'loai_truong' => 'date',
                'bat_buoc' => true,
                'placeholder' => 'Chọn ngày sinh',
                'thu_tu' => 2,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service1->id, 'ten_truong' => 'gioi_tinh'],
            [
                'nhan_hien_thi' => 'Giới tính',
                'loai_truong' => 'select',
                'bat_buoc' => true,
                'tuy_chon' => ['Nam', 'Nữ'],
                'thu_tu' => 3,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service1->id, 'ten_truong' => 'giay_khai_sinh_bo_me'],
            [
                'nhan_hien_thi' => 'Giấy khai sinh của bố mẹ',
                'loai_truong' => 'file',
                'bat_buoc' => true,
                'goi_y' => 'Upload file PDF hoặc ảnh (Tối đa 5MB)',
                'placeholder' => 'Chọn file',
                'thu_tu' => 4,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service1->id, 'ten_truong' => 'giay_chung_sinh'],
            [
                'nhan_hien_thi' => 'Giấy chứng sinh',
                'loai_truong' => 'file',
                'bat_buoc' => true,
                'goi_y' => 'Upload file PDF hoặc ảnh',
                'thu_tu' => 5,
            ]
        );

        // ========== DỊCH VỤ 2: Đăng ký thường trú ==========
        $service2 = Service::firstOrCreate(
            ['ten_dich_vu' => 'Đăng ký thường trú'],
            [
                'mo_ta' => 'Dịch vụ đăng ký hộ khẩu thường trú. Thời gian xử lý: 7-10 ngày làm việc.',
            ]
        );

        // Lịch dịch vụ: Thứ 3 và Thứ 5 (2 thứ trong tuần)
        ServiceSchedule::firstOrCreate(
            [
                'dich_vu_id' => $service2->id,
                'thu_trong_tuan' => 3, // Thứ 3
                'gio_bat_dau' => '08:00',
            ],
            [
                'trang_thai' => true,
                'gio_ket_thuc' => '16:00',
                'so_luong_toi_da' => 15,
            ]
        );
        ServiceSchedule::firstOrCreate(
            [
                'dich_vu_id' => $service2->id,
                'thu_trong_tuan' => 5, // Thứ 5
                'gio_bat_dau' => '08:00',
            ],
            [
                'trang_thai' => true,
                'gio_ket_thuc' => '16:00',
                'so_luong_toi_da' => 15,
            ]
        );

        // Cấu hình cho từng phường
        foreach ($donVis as $index => $donVi) {
            ServicePhuong::firstOrCreate(
                [
                    'dich_vu_id' => $service2->id,
                    'don_vi_id' => $donVi->id,
                ],
                [
                    'thoi_gian_xu_ly' => 7 + ($index * 2),
                    'so_luong_toi_da' => 10 + ($index * 3),
                    'phi_dich_vu' => 50000,
                    'kich_hoat' => true,
                    'ghi_chu' => 'Phí dịch vụ: 50.000 VNĐ',
                ]
            );
        }

        // Form fields
        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service2->id, 'ten_truong' => 'ho_ten'],
            [
                'nhan_hien_thi' => 'Họ và tên người đăng ký',
                'loai_truong' => 'text',
                'bat_buoc' => true,
                'placeholder' => 'Nhập họ và tên đầy đủ',
                'thu_tu' => 1,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service2->id, 'ten_truong' => 'so_cmnd_cccd'],
            [
                'nhan_hien_thi' => 'Số CMND/CCCD',
                'loai_truong' => 'text',
                'bat_buoc' => true,
                'placeholder' => 'Nhập số CMND/CCCD (12 số)',
                'goi_y' => 'Nhập đầy đủ 12 số',
                'thu_tu' => 2,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service2->id, 'ten_truong' => 'cmnd_cccd_scan'],
            [
                'nhan_hien_thi' => 'Bản sao CMND/CCCD',
                'loai_truong' => 'file',
                'bat_buoc' => true,
                'goi_y' => 'Upload bản sao công chứng CMND/CCCD',
                'thu_tu' => 3,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service2->id, 'ten_truong' => 'so_ho_khau'],
            [
                'nhan_hien_thi' => 'Số hộ khẩu cũ',
                'loai_truong' => 'text',
                'bat_buoc' => false,
                'placeholder' => 'Nhập số hộ khẩu (nếu có)',
                'thu_tu' => 4,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service2->id, 'ten_truong' => 'dia_chi_moi'],
            [
                'nhan_hien_thi' => 'Địa chỉ thường trú mới',
                'loai_truong' => 'textarea',
                'bat_buoc' => true,
                'placeholder' => 'Nhập địa chỉ chi tiết',
                'thu_tu' => 5,
            ]
        );

        // ========== DỊCH VỤ 3: Cấp giấy xác nhận độc thân ==========
        $service3 = Service::firstOrCreate(
            ['ten_dich_vu' => 'Cấp giấy xác nhận độc thân'],
            [
                'mo_ta' => 'Dịch vụ cấp giấy xác nhận tình trạng hôn nhân (độc thân). Thời gian xử lý: 1-2 ngày làm việc.',
            ]
        );

        // Lịch dịch vụ: Chỉ Thứ 4 (1 thứ trong tuần)
        ServiceSchedule::firstOrCreate(
            [
                'dich_vu_id' => $service3->id,
                'thu_trong_tuan' => 4, // Thứ 4
                'gio_bat_dau' => '08:00',
            ],
            [
                'trang_thai' => true,
                'gio_ket_thuc' => '17:00',
                'so_luong_toi_da' => 25,
            ]
        );

        // Cấu hình cho từng phường
        foreach ($donVis as $index => $donVi) {
            ServicePhuong::firstOrCreate(
                [
                    'dich_vu_id' => $service3->id,
                    'don_vi_id' => $donVi->id,
                ],
                [
                    'thoi_gian_xu_ly' => 1,
                    'so_luong_toi_da' => 20 + ($index * 5),
                    'phi_dich_vu' => 20000,
                    'kich_hoat' => true,
                    'ghi_chu' => 'Phí dịch vụ: 20.000 VNĐ',
                ]
            );
        }

        // Form fields
        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service3->id, 'ten_truong' => 'ho_ten_doc_than'],
            [
                'nhan_hien_thi' => 'Họ và tên',
                'loai_truong' => 'text',
                'bat_buoc' => true,
                'placeholder' => 'Nhập họ và tên đầy đủ',
                'thu_tu' => 1,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service3->id, 'ten_truong' => 'ngay_sinh_doc_than'],
            [
                'nhan_hien_thi' => 'Ngày sinh',
                'loai_truong' => 'date',
                'bat_buoc' => true,
                'thu_tu' => 2,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service3->id, 'ten_truong' => 'cmnd_cccd_doc_than'],
            [
                'nhan_hien_thi' => 'CMND/CCCD',
                'loai_truong' => 'file',
                'bat_buoc' => true,
                'goi_y' => 'Upload bản sao CMND/CCCD',
                'thu_tu' => 3,
            ]
        );

        // ========== DỊCH VỤ 4: Cấp giấy chứng nhận cư trú ==========
        $service4 = Service::firstOrCreate(
            ['ten_dich_vu' => 'Cấp giấy chứng nhận cư trú'],
            [
                'mo_ta' => 'Dịch vụ cấp giấy chứng nhận tạm trú, tạm vắng. Thời gian xử lý: 3-5 ngày làm việc.',
            ]
        );

        // Lịch dịch vụ: Thứ 2 và Thứ 6 (2 thứ trong tuần)
        ServiceSchedule::firstOrCreate(
            [
                'dich_vu_id' => $service4->id,
                'thu_trong_tuan' => 2, // Thứ 3
                'gio_bat_dau' => '08:30',
            ],
            [
                'trang_thai' => true,
                'gio_ket_thuc' => '16:30',
                'so_luong_toi_da' => 15,
            ]
        );
        ServiceSchedule::firstOrCreate(
            [
                'dich_vu_id' => $service4->id,
                'thu_trong_tuan' => 6, // Thứ 7
                'gio_bat_dau' => '08:30',
            ],
            [
                'trang_thai' => true,
                'gio_ket_thuc' => '16:30',
                'so_luong_toi_da' => 15,
            ]
        );

        // Cấu hình cho từng phường
        foreach ($donVis as $index => $donVi) {
            ServicePhuong::firstOrCreate(
                [
                    'dich_vu_id' => $service4->id,
                    'don_vi_id' => $donVi->id,
                ],
                [
                    'thoi_gian_xu_ly' => 3,
                    'so_luong_toi_da' => 12 + ($index * 2),
                    'phi_dich_vu' => 30000,
                    'kich_hoat' => true,
                ]
            );
        }

        // Form fields
        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service4->id, 'ten_truong' => 'ho_ten_cu_tru'],
            [
                'nhan_hien_thi' => 'Họ và tên',
                'loai_truong' => 'text',
                'bat_buoc' => true,
                'thu_tu' => 1,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service4->id, 'ten_truong' => 'loai_cu_tru'],
            [
                'nhan_hien_thi' => 'Loại cư trú',
                'loai_truong' => 'select',
                'bat_buoc' => true,
                'tuy_chon' => ['Tạm trú', 'Tạm vắng'],
                'thu_tu' => 2,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service4->id, 'ten_truong' => 'cmnd_cu_tru'],
            [
                'nhan_hien_thi' => 'CMND/CCCD',
                'loai_truong' => 'file',
                'bat_buoc' => true,
                'thu_tu' => 3,
            ]
        );

        // ========== DỊCH VỤ 5: Đăng ký khai tử ==========
        $service5 = Service::firstOrCreate(
            ['ten_dich_vu' => 'Đăng ký khai tử'],
            [
                'mo_ta' => 'Dịch vụ đăng ký khai tử. Thời gian xử lý: 2-3 ngày làm việc.',
            ]
        );

        // Lịch dịch vụ: Chỉ Thứ 5 (1 thứ trong tuần)
        ServiceSchedule::firstOrCreate(
            [
                'dich_vu_id' => $service5->id,
                'thu_trong_tuan' => 5, // Thứ 5
                'gio_bat_dau' => '08:00',
            ],
            [
                'trang_thai' => true,
                'gio_ket_thuc' => '12:00',
                'so_luong_toi_da' => 10,
            ]
        );

        // Cấu hình cho từng phường
        foreach ($donVis as $index => $donVi) {
            ServicePhuong::firstOrCreate(
                [
                    'dich_vu_id' => $service5->id,
                    'don_vi_id' => $donVi->id,
                ],
                [
                    'thoi_gian_xu_ly' => 2,
                    'so_luong_toi_da' => 5,
                    'phi_dich_vu' => 0,
                    'kich_hoat' => true,
                ]
            );
        }

        // Form fields
        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service5->id, 'ten_truong' => 'ho_ten_nguoi_mat'],
            [
                'nhan_hien_thi' => 'Họ và tên người mất',
                'loai_truong' => 'text',
                'bat_buoc' => true,
                'thu_tu' => 1,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service5->id, 'ten_truong' => 'ngay_mat'],
            [
                'nhan_hien_thi' => 'Ngày mất',
                'loai_truong' => 'date',
                'bat_buoc' => true,
                'thu_tu' => 2,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service5->id, 'ten_truong' => 'giay_chung_tu'],
            [
                'nhan_hien_thi' => 'Giấy chứng tử',
                'loai_truong' => 'file',
                'bat_buoc' => true,
                'thu_tu' => 3,
            ]
        );

        // ========== DỊCH VỤ 6: Cấp bản sao giấy tờ ==========
        $service6 = Service::firstOrCreate(
            ['ten_dich_vu' => 'Cấp bản sao giấy tờ'],
            [
                'mo_ta' => 'Dịch vụ cấp bản sao các loại giấy tờ từ sổ hộ khẩu, giấy khai sinh. Thời gian xử lý: 1-2 ngày làm việc.',
            ]
        );

        // Lịch dịch vụ: Chỉ Thứ 6 (1 thứ trong tuần)
        ServiceSchedule::firstOrCreate(
            [
                'dich_vu_id' => $service6->id,
                'thu_trong_tuan' => 6, // Thứ 7
                'gio_bat_dau' => '08:00',
            ],
            [
                'trang_thai' => true,
                'gio_ket_thuc' => '17:00',
                'so_luong_toi_da' => 30,
            ]
        );

        // Cấu hình cho từng phường
        foreach ($donVis as $index => $donVi) {
            ServicePhuong::firstOrCreate(
                [
                    'dich_vu_id' => $service6->id,
                    'don_vi_id' => $donVi->id,
                ],
                [
                    'thoi_gian_xu_ly' => 1,
                    'so_luong_toi_da' => 25 + ($index * 3),
                    'phi_dich_vu' => 10000,
                    'kich_hoat' => true,
                    'ghi_chu' => 'Phí dịch vụ: 10.000 VNĐ/bản',
                ]
            );
        }

        // Form fields
        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service6->id, 'ten_truong' => 'ho_ten_ban_sao'],
            [
                'nhan_hien_thi' => 'Họ và tên người yêu cầu',
                'loai_truong' => 'text',
                'bat_buoc' => true,
                'thu_tu' => 1,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service6->id, 'ten_truong' => 'loai_giay_to'],
            [
                'nhan_hien_thi' => 'Loại giấy tờ cần sao',
                'loai_truong' => 'select',
                'bat_buoc' => true,
                'tuy_chon' => ['Sổ hộ khẩu', 'Giấy khai sinh', 'CMND/CCCD', 'Giấy chứng nhận', 'Khác'],
                'thu_tu' => 2,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service6->id, 'ten_truong' => 'so_ban_sao'],
            [
                'nhan_hien_thi' => 'Số bản cần sao',
                'loai_truong' => 'number',
                'bat_buoc' => true,
                'placeholder' => 'Nhập số bản (tối đa 5 bản)',
                'goi_y' => 'Tối đa 5 bản/lần',
                'thu_tu' => 3,
            ]
        );

        ServiceField::firstOrCreate(
            ['dich_vu_id' => $service6->id, 'ten_truong' => 'ly_do'],
            [
                'nhan_hien_thi' => 'Lý do yêu cầu',
                'loai_truong' => 'textarea',
                'bat_buoc' => false,
                'placeholder' => 'Nhập lý do cần bản sao',
                'thu_tu' => 4,
            ]
        );

        $this->command->info('✅ Đã tạo fake data cho ' . Service::count() . ' dịch vụ!');
        $this->command->info('✅ Đã tạo ' . ServiceSchedule::count() . ' lịch dịch vụ!');
        $this->command->info('✅ Đã tạo ' . ServicePhuong::count() . ' cấu hình dịch vụ phường!');
        $this->command->info('✅ Đã tạo ' . ServiceField::count() . ' trường form!');
    }
}

