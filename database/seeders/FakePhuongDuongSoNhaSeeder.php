<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Phuong;
use App\Models\Duong;
use App\Models\SoNha;

class FakePhuongDuongSoNhaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Danh sách các phường (nếu chưa có)
        $phuongs = [
            ['ten_phuong' => 'Phường Bến Nghé', 'mo_ta' => 'Phường Bến Nghé - Quận 1, TP.HCM'],
            ['ten_phuong' => 'Phường Đa Kao', 'mo_ta' => 'Phường Đa Kao - Quận 1, TP.HCM'],
            ['ten_phuong' => 'Phường Cô Giang', 'mo_ta' => 'Phường Cô Giang - Quận 1, TP.HCM'],
            ['ten_phuong' => 'Phường Cầu Kho', 'mo_ta' => 'Phường Cầu Kho - Quận 1, TP.HCM'],
            ['ten_phuong' => 'Phường Nguyễn Thái Bình', 'mo_ta' => 'Phường Nguyễn Thái Bình - Quận 1, TP.HCM'],
            ['ten_phuong' => 'Phường Phạm Ngũ Lão', 'mo_ta' => 'Phường Phạm Ngũ Lão - Quận 1, TP.HCM'],
            ['ten_phuong' => 'Phường Cầu Ông Lãnh', 'mo_ta' => 'Phường Cầu Ông Lãnh - Quận 1, TP.HCM'],
            ['ten_phuong' => 'Phường Tân Định', 'mo_ta' => 'Phường Tân Định - Quận 1, TP.HCM'],
            ['ten_phuong' => 'Phường Bến Thành', 'mo_ta' => 'Phường Bến Thành - Quận 1, TP.HCM'],
            ['ten_phuong' => 'Phường Thạnh Xuân', 'mo_ta' => 'Phường Thạnh Xuân - Quận 12, TP.HCM'],
            ['ten_phuong' => 'Phường Thạnh Lộc', 'mo_ta' => 'Phường Thạnh Lộc - Quận 12, TP.HCM'],
            ['ten_phuong' => 'Phường Hiệp Thành', 'mo_ta' => 'Phường Hiệp Thành - Quận 12, TP.HCM'],
            ['ten_phuong' => 'Phường Thới An', 'mo_ta' => 'Phường Thới An - Quận 12, TP.HCM'],
            ['ten_phuong' => 'Phường Linh Xuân', 'mo_ta' => 'Phường Linh Xuân - Quận Thủ Đức, TP.HCM'],
            ['ten_phuong' => 'Phường Bình Chiểu', 'mo_ta' => 'Phường Bình Chiểu - Quận Thủ Đức, TP.HCM'],
            ['ten_phuong' => 'Phường Linh Trung', 'mo_ta' => 'Phường Linh Trung - Quận Thủ Đức, TP.HCM'],
            ['ten_phuong' => 'Phường Tam Bình', 'mo_ta' => 'Phường Tam Bình - Quận Thủ Đức, TP.HCM'],
            ['ten_phuong' => 'Phường Tân Phú', 'mo_ta' => 'Phường Tân Phú - Quận Tân Phú, TP.HCM'],
            ['ten_phuong' => 'Phường Tân Sơn Nhì', 'mo_ta' => 'Phường Tân Sơn Nhì - Quận Tân Phú, TP.HCM'],
            ['ten_phuong' => 'Phường Tây Thạnh', 'mo_ta' => 'Phường Tây Thạnh - Quận Tân Phú, TP.HCM'],
        ];

        // Tạo phường nếu chưa có
        foreach ($phuongs as $phuongData) {
            $phuong = Phuong::firstOrCreate(
                ['ten_phuong' => $phuongData['ten_phuong']],
                $phuongData
            );
        }

        // Lấy tất cả phường
        $allPhuongs = Phuong::all();

        if ($allPhuongs->isEmpty()) {
            $this->command->warn('Chưa có phường nào. Vui lòng chạy PhuongSeeder trước!');
            return;
        }

        // Danh sách tên đường phổ biến ở Việt Nam
        $tenDuongs = [
            'Nguyễn Huệ', 'Lê Lợi', 'Đồng Khởi', 'Nguyễn Du', 'Pasteur',
            'Lý Tự Trọng', 'Nam Kỳ Khởi Nghĩa', 'Võ Văn Tần', 'Nguyễn Thị Minh Khai',
            'Cách Mạng Tháng Tám', 'Lê Văn Sỹ', 'Nguyễn Văn Cừ', 'Hoàng Văn Thụ',
            'Nguyễn Trãi', 'Trần Hưng Đạo', 'Lý Thường Kiệt', 'Phạm Ngũ Lão',
            'Điện Biên Phủ', 'Cộng Hòa', 'Lê Đức Thọ', 'Nguyễn Oanh',
            'Quang Trung', 'Trường Chinh', 'Tân Hương', 'Tân Quý',
            'Lê Văn Việt', 'Nguyễn Văn Bá', 'Tân Kỳ Tân Quý', 'Tân Thắng',
            'Linh Trung', 'Linh Xuân', 'Linh Đông', 'Linh Tây',
            'Bình Chiểu', 'Bình Thọ', 'Trường Thọ', 'Long Bình',
            'Thạnh Xuân', 'Thạnh Lộc', 'Hiệp Thành', 'Thới An',
            'Tân Phú', 'Tân Sơn Nhì', 'Tây Thạnh', 'Sơn Kỳ',
        ];

        // Tạo đường cho mỗi phường (mỗi phường có 3-5 đường)
        foreach ($allPhuongs as $phuong) {
            $soDuong = rand(3, 5);
            $duongsTrongPhuong = [];

            for ($i = 0; $i < $soDuong; $i++) {
                $tenDuong = $tenDuongs[array_rand($tenDuongs)];
                
                // Đảm bảo không trùng tên đường trong cùng phường
                while (in_array($tenDuong, $duongsTrongPhuong)) {
                    $tenDuong = $tenDuongs[array_rand($tenDuongs)];
                }
                $duongsTrongPhuong[] = $tenDuong;

                $duong = Duong::firstOrCreate(
                    [
                        'ten_duong' => $tenDuong,
                        'phuong_id' => $phuong->id,
                    ],
                    [
                        'mo_ta' => 'Đường ' . $tenDuong . ' - ' . $phuong->ten_phuong,
                    ]
                );

                // Tạo số nhà cho mỗi đường (mỗi đường có 10-20 số nhà)
                $soNhaCount = rand(10, 20);
                for ($j = 1; $j <= $soNhaCount; $j++) {
                    // Tạo số nhà từ 1 đến 999, có thể có số lẻ/chẵn
                    $soNha = rand(1, 999);
                    
                    // 30% khả năng có số lẻ (ví dụ: 15A, 23B)
                    if (rand(1, 100) <= 30) {
                        $soNha = $soNha . chr(65 + rand(0, 2)); // A, B, hoặc C
                    }

                    SoNha::firstOrCreate(
                        [
                            'so_nha' => (string)$soNha,
                            'duong_id' => $duong->id,
                        ],
                        [
                            'mo_ta' => 'Số ' . $soNha . ' đường ' . $tenDuong . ' - ' . $phuong->ten_phuong,
                        ]
                    );
                }
            }
        }

        $this->command->info('✅ Đã tạo ' . Phuong::count() . ' phường!');
        $this->command->info('✅ Đã tạo ' . Duong::count() . ' đường!');
        $this->command->info('✅ Đã tạo ' . SoNha::count() . ' số nhà!');
    }
}
