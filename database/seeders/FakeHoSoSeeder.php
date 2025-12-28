<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HoSo;
use App\Models\HoSoField;
use App\Models\User;
use App\Models\Service;
use App\Models\ServicePhuong;
use App\Models\DonVi;
use Carbon\Carbon;

class FakeHoSoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $services = Service::all();
        $donVis = DonVi::all();

        if ($users->isEmpty() || $services->isEmpty() || $donVis->isEmpty()) {
            $this->command->warn('Chưa có đủ dữ liệu. Vui lòng chạy UserSeeder, ServiceSeeder, DonViSeeder trước!');
            return;
        }

        $statuses = [
            HoSo::STATUS_RECEIVED,
            HoSo::STATUS_PROCESSING,
            HoSo::STATUS_COMPLETED,
        ];

        // Tạo 20-30 hồ sơ fake
        for ($i = 0; $i < 25; $i++) {
            $user = $users->random();
            $service = $services->random();
            $donVi = $donVis->random();

            // Kiểm tra xem dịch vụ có được kích hoạt cho phường này không
            $servicePhuong = ServicePhuong::where('dich_vu_id', $service->id)
                ->where('don_vi_id', $donVi->id)
                ->where('kich_hoat', true)
                ->first();

            if (!$servicePhuong) {
                continue; // Bỏ qua nếu dịch vụ chưa được kích hoạt cho phường này
            }

            // Tạo ngày hẹn trong tương lai (từ 1 tuần đến 2 tháng sau)
            $ngayHen = Carbon::now()->addDays(rand(7, 60));
            
            // Đếm số lượng đã đăng ký trong ngày này
            $bookedCount = HoSo::where('dich_vu_id', $service->id)
                ->where('don_vi_id', $donVi->id)
                ->where('ngay_hen', $ngayHen->format('Y-m-d'))
                ->where('trang_thai', '!=', HoSo::STATUS_CANCELLED)
                ->count();

            // Tính số thứ tự
            $soThuTu = $bookedCount + 1;

            // Tạo hồ sơ
            $hoSo = HoSo::create([
                'ma_ho_so' => HoSo::generateCode(),
                'dich_vu_id' => $service->id,
                'nguoi_dung_id' => $user->id,
                'don_vi_id' => $donVi->id,
                'ngay_hen' => $ngayHen->format('Y-m-d'),
                'gio_hen' => rand(8, 16) . ':00',
                'so_thu_tu' => $soThuTu,
                'trang_thai' => $statuses[array_rand($statuses)],
                'ghi_chu' => rand(0, 1) ? 'Ghi chú test cho hồ sơ #' . ($i + 1) : null,
            ]);

            // Tạo dữ liệu cho các trường động
            $serviceFields = $service->serviceFields;
            foreach ($serviceFields as $field) {
                $fieldValue = null;

                switch ($field->loai_truong) {
                    case 'text':
                        $fieldValue = 'Giá trị test cho ' . $field->nhan_hien_thi;
                        break;
                    case 'email':
                        $fieldValue = 'test' . $i . '@example.com';
                        break;
                    case 'number':
                        $fieldValue = rand(1, 100);
                        break;
                    case 'date':
                        $fieldValue = Carbon::now()->subYears(rand(20, 50))->format('Y-m-d');
                        break;
                    case 'textarea':
                        $fieldValue = 'Nội dung test cho trường ' . $field->nhan_hien_thi;
                        break;
                    case 'select':
                        if ($field->tuy_chon && is_array($field->tuy_chon) && !empty($field->tuy_chon)) {
                            $fieldValue = $field->tuy_chon[array_rand($field->tuy_chon)];
                        }
                        break;
                    case 'file':
                        // Giả lập đường dẫn file
                        $fieldValue = 'ho-so/' . $hoSo->ma_ho_so . '/file_' . $field->ten_truong . '.pdf';
                        break;
                }

                if ($fieldValue !== null) {
                    HoSoField::create([
                        'ho_so_id' => $hoSo->id,
                        'ten_truong' => $field->ten_truong,
                        'gia_tri' => $fieldValue,
                    ]);
                }
            }
        }

        $this->command->info('✅ Đã tạo ' . HoSo::count() . ' hồ sơ fake!');
    }
}
