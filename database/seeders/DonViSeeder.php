<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DonVi;

class DonViSeeder extends Seeder
{
    public function run(): void
    {
        $donVis = [
            ['ten_don_vi' => 'Phường 1', 'mo_ta' => 'Phường 1 - Quận 1'],
            ['ten_don_vi' => 'Phường Bến Nghé', 'mo_ta' => 'Phường Bến Nghé - Quận 1'],
            ['ten_don_vi' => 'Phường Bến Thành', 'mo_ta' => 'Phường Bến Thành - Quận 1'],
            ['ten_don_vi' => 'Phường Cầu Ông Lãnh', 'mo_ta' => 'Phường Cầu Ông Lãnh - Quận 1'],
            ['ten_don_vi' => 'Phường Tân Định', 'mo_ta' => 'Phường Tân Định - Quận 1'],
        ];

        foreach ($donVis as $donVi) {
            DonVi::firstOrCreate(
                ['ten_don_vi' => $donVi['ten_don_vi']],
                $donVi
            );
        }
    }
}
