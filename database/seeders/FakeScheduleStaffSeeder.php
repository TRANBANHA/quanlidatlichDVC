<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceScheduleStaff;
use App\Models\ServiceSchedule;
use App\Models\Admin;
use App\Models\DonVi;

class FakeScheduleStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = ServiceSchedule::where('trang_thai', true)->get();
        $donVis = DonVi::all();

        if ($schedules->isEmpty() || $donVis->isEmpty()) {
            $this->command->warn('Chưa có lịch dịch vụ hoặc phường. Vui lòng chạy FakeServiceDataSeeder và DonViSeeder trước!');
            return;
        }

        // Lấy tất cả cán bộ (quyen = 0)
        $canBos = Admin::where('quyen', 0)->get();

        if ($canBos->isEmpty()) {
            $this->command->warn('Chưa có cán bộ nào. Vui lòng chạy AdminSeeder trước!');
            return;
        }

        // Phân công cán bộ cho các lịch dịch vụ
        foreach ($schedules as $schedule) {
            // Lấy dịch vụ và phường từ schedule
            $service = $schedule->dichVu;
            if (!$service) {
                continue;
            }

            // Lấy các phường có dịch vụ này
            $servicePhuongs = \App\Models\ServicePhuong::where('dich_vu_id', $service->id)
                ->where('kich_hoat', true)
                ->get();

            foreach ($servicePhuongs as $servicePhuong) {
                // Lấy cán bộ của phường này
                $canBosPhuong = $canBos->where('don_vi_id', $servicePhuong->don_vi_id);

                if ($canBosPhuong->isEmpty()) {
                    continue;
                }

                // Phân công 1-2 cán bộ cho mỗi lịch (tùy số lượng cán bộ có sẵn)
                $soCanBo = min(rand(1, 2), $canBosPhuong->count());
                $selectedCanBos = $canBosPhuong->random($soCanBo);

                foreach ($selectedCanBos as $canBo) {
                    ServiceScheduleStaff::firstOrCreate(
                        [
                            'schedule_id' => $schedule->id,
                            'can_bo_id' => $canBo->id,
                        ]
                    );
                }
            }
        }

        $this->command->info('✅ Đã phân công cán bộ cho ' . ServiceScheduleStaff::count() . ' lịch dịch vụ!');
    }
}
