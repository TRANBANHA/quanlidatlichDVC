<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServiceSchedule;
use Carbon\Carbon;
use App\Models\Admin;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\ServiceAssignment;
use App\Http\Controllers\Controller;

class ServiceAssignmentController extends Controller
{
    public function index()
    {
        // 👉 Lấy danh sách Thứ 2 → Thứ 6 của tuần thứ 2 tới
        $startDate = Carbon::now()->startOfWeek()->addWeeks(2);
        $dates = [];
        for ($i = 0; $i < 5; $i++) {
            $dates[] = [
                'date' => $startDate->copy()->addDays($i)->toDateString(),
                'thu' => $startDate->copy()->addDays($i)->dayOfWeekIso,
            ];
        }
        // 👉 Lấy danh sách dịch vụ
        $serviceSchedule = ServiceSchedule::with('service')->get();
        $services = Service::all();
        $serviceAssignments = ServiceAssignment::all()->keyBy('ngay_phan_cong');
        // Lấy danh sách admin phường và cán bộ để phân công
        $canBoList = Admin::whereIn('quyen', [Admin::ADMIN_PHUONG, Admin::CAN_BO])->get();
        return view('backend.services_assignment.index', compact('dates', 'services', 'canBoList', 'serviceSchedule', 'serviceAssignments'));
    }

    public function store(Request $request)
    {
        $assignments = $request->input('assignments', []);
        // Xóa tất cả phân công dịch vụ hiện có để tránh trùng lặp
        foreach ($assignments as $ngay => $data) {
            $dateExist = ServiceAssignment::where('ngay_phan_cong', $ngay)->first();
            if ($dateExist) {
                $dateExist->update([
                    'ma_can_bo' => json_encode($data['ma_can_bo']) ?? [],
                    'ma_dich_vu' => $data['ma_dich_vu'] ?? null,
                    'ghi_chu' => $data['ghi_chu'] ?? null,
                ]);
            } else {
                ServiceAssignment::create([
                    'ma_can_bo' => json_encode($data['ma_can_bo']) ?? [],
                    'ma_dich_vu' => $data['ma_dich_vu'] ?? null,
                    'ngay_phan_cong' => $ngay,
                    'ghi_chu' => $data['ghi_chu'] ?? null,
                ]);
            }
        }

        return back()->with('success', 'Đã lưu phân công dịch vụ thành công!');
    }
}
