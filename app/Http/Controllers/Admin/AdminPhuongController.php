<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\HoSo;
use App\Models\DonVi;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPhuongController extends Controller
{
    /**
     * Quản lý cán bộ trong phường
     */
    public function staffIndex(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isAdminPhuong()) {
            abort(403, 'Chỉ Admin phường mới có quyền quản lý cán bộ.');
        }

        $query = Admin::where('don_vi_id', $currentUser->don_vi_id)
            ->where('quyen', Admin::CAN_BO)
            ->with('donVi');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ho_ten', 'like', "%{$search}%")
                  ->orWhere('ten_dang_nhap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $staffs = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('backend.admin-phuong.staff.index', compact('staffs'));
    }

    /**
     * Xem hiệu quả công việc theo từng cán bộ
     */
    public function staffPerformance(Request $request, $staffId = null)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isAdminPhuong()) {
            abort(403);
        }

        $staffId = $staffId ?? $request->get('staff_id');

        $query = Admin::where('don_vi_id', $currentUser->don_vi_id)
            ->where('quyen', Admin::CAN_BO);

        if ($staffId) {
            $query->where('id', $staffId);
        }

        $staffs = $query->get();

        $tuNgay = $request->get('tu_ngay', now()->subMonth()->format('Y-m-d'));
        $denNgay = $request->get('den_ngay', now()->format('Y-m-d'));

        $performanceData = [];

        foreach ($staffs as $staff) {
            // Số lượng hồ sơ đã xử lý
            $soHoSo = HoSo::where('quan_tri_vien_id', $staff->id)
                ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                ->count();

            // Số hồ sơ hoàn tất
            $soHoSoHoanTat = HoSo::where('quan_tri_vien_id', $staff->id)
                ->where('trang_thai', HoSo::STATUS_COMPLETED)
                ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                ->count();

            // Thời gian xử lý trung bình (tính từ ngày tiếp nhận đến hoàn tất)
            $thoiGianTB = HoSo::where('quan_tri_vien_id', $staff->id)
                ->where('trang_thai', HoSo::STATUS_COMPLETED)
                ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as tb_ngay')
                ->first();

            // Đánh giá trung bình
            $danhGia = Rating::where('quan_tri_vien_id', $staff->id)
                ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                ->selectRaw('AVG(diem) as diem_tb, COUNT(*) as so_luong')
                ->first();

            $performanceData[] = [
                'staff' => $staff,
                'so_ho_so' => $soHoSo,
                'so_ho_so_hoan_tat' => $soHoSoHoanTat,
                'ty_le_hoan_tat' => $soHoSo > 0 ? round(($soHoSoHoanTat / $soHoSo) * 100, 2) : 0,
                'thoi_gian_tb' => $thoiGianTB ? round($thoiGianTB->tb_ngay, 1) : 0,
                'diem_tb' => $danhGia ? round($danhGia->diem_tb, 1) : 0,
                'so_danh_gia' => $danhGia ? $danhGia->so_luong : 0,
            ];
        }

        // Sắp xếp theo số hồ sơ hoàn tất
        usort($performanceData, function($a, $b) {
            return $b['so_ho_so_hoan_tat'] <=> $a['so_ho_so_hoan_tat'];
        });

        return view('backend.admin-phuong.staff.performance', compact('performanceData', 'staffs', 'tuNgay', 'denNgay', 'staffId'));
    }

    /**
     * Quản lý người dân trong phường
     */
    public function usersIndex(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isAdminPhuong()) {
            abort(403);
        }

        $query = User::whereHas('hoSos', function($q) use ($currentUser) {
            $q->where('don_vi_id', $currentUser->don_vi_id);
        })->withCount(['hoSos' => function($q) use ($currentUser) {
            $q->where('don_vi_id', $currentUser->don_vi_id);
        }]);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ten', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cccd', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('backend.admin-phuong.users.index', compact('users'));
    }

    /**
     * Xem lịch sử đặt lịch/hồ sơ của người dân
     */
    public function userHistory($userId)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isAdminPhuong()) {
            abort(403);
        }

        $user = User::findOrFail($userId);

        $hoSos = HoSo::where('nguoi_dung_id', $userId)
            ->where('don_vi_id', $currentUser->don_vi_id)
            ->with(['dichVu', 'quanTriVien', 'rating'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.admin-phuong.users.history', compact('user', 'hoSos'));
    }
}

