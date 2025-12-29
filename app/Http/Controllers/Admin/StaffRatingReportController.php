<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffRatingReportController extends Controller
{
    public function index()
    {
        $currentUser = Auth::guard('admin')->user();

        // Lấy thống kê rating theo nhân viên (cán bộ được đánh giá)
        // Chỉ lấy các cán bộ có đánh giá (quan_tri_vien_id không null)
        $query = Rating::select(
            'quan_tri_vien.id',
            'quan_tri_vien.ho_ten as ten',
            DB::raw('COUNT(ratings.id) as total_ratings'),
            DB::raw('AVG(ratings.diem) as average_rating'),
            DB::raw('COUNT(CASE WHEN ratings.diem = 5 THEN 1 END) as five_star'),
            DB::raw('COUNT(CASE WHEN ratings.diem = 4 THEN 1 END) as four_star'),
            DB::raw('COUNT(CASE WHEN ratings.diem = 3 THEN 1 END) as three_star'), 
            DB::raw('COUNT(CASE WHEN ratings.diem = 2 THEN 1 END) as two_star'),
            DB::raw('COUNT(CASE WHEN ratings.diem = 1 THEN 1 END) as one_star')
        )
        ->join('quan_tri_vien', 'ratings.quan_tri_vien_id', '=', 'quan_tri_vien.id')
        ->whereNotNull('ratings.quan_tri_vien_id'); // Chỉ lấy đánh giá có cán bộ được đánh giá

        // Phân quyền hiển thị:
        if ($currentUser->isCanBo()) {
            // Cán bộ: chỉ hiển thị đánh giá của chính mình
            $query->where('quan_tri_vien.id', $currentUser->id);
        } elseif ($currentUser->isAdminPhuong()) {
            // Admin phường: chỉ hiển thị cán bộ trong phường của họ
            $query->where('quan_tri_vien.don_vi_id', $currentUser->don_vi_id);
        }
        // Admin tổng: hiển thị tất cả (không cần filter thêm)

        $staffRatings = $query->groupBy('quan_tri_vien.id', 'quan_tri_vien.ho_ten')
            ->orderBy('average_rating', 'desc')
            ->get();

        return view('backend.reports.staff-rating', compact('staffRatings'));
    }

    public function getChartData()
    {
        $currentUser = Auth::guard('admin')->user();

        // Lấy dữ liệu cho biểu đồ (theo cán bộ được đánh giá)
        $query = Rating::select(
            'quan_tri_vien.ho_ten as ten',
            DB::raw('AVG(ratings.diem) as average_rating'),
            DB::raw('COUNT(ratings.id) as total_ratings')
        )
        ->join('quan_tri_vien', 'ratings.quan_tri_vien_id', '=', 'quan_tri_vien.id')
        ->whereNotNull('ratings.quan_tri_vien_id'); // Chỉ lấy đánh giá có cán bộ được đánh giá

        // Phân quyền hiển thị (giống như index):
        if ($currentUser->isCanBo()) {
            // Cán bộ: chỉ hiển thị đánh giá của chính mình
            $query->where('quan_tri_vien.id', $currentUser->id);
        } elseif ($currentUser->isAdminPhuong()) {
            // Admin phường: chỉ hiển thị cán bộ trong phường của họ
            $query->where('quan_tri_vien.don_vi_id', $currentUser->don_vi_id);
        }
        // Admin tổng: hiển thị tất cả (không cần filter thêm)

        $chartData = $query->groupBy('quan_tri_vien.id', 'quan_tri_vien.ho_ten')
            ->orderBy('average_rating', 'desc')
            ->get();

        return response()->json($chartData);
    }

    /**
     * Hiển thị chi tiết đánh giá của một nhân viên
     */
    public function show($staffId)
    {
        $currentUser = Auth::guard('admin')->user();

        // Lấy thông tin nhân viên
        $staff = Admin::findOrFail($staffId);

        // Kiểm tra quyền truy cập
        if ($currentUser->isCanBo()) {
            // Cán bộ: chỉ xem được đánh giá của chính mình
            if ($staff->id != $currentUser->id) {
                abort(403, 'Bạn không có quyền xem đánh giá của nhân viên này.');
            }
        } elseif ($currentUser->isAdminPhuong()) {
            // Admin phường: chỉ xem được cán bộ trong phường của họ
            if ($staff->don_vi_id != $currentUser->don_vi_id) {
                abort(403, 'Bạn không có quyền xem đánh giá của nhân viên này.');
            }
        }
        // Admin tổng: xem được tất cả (không cần kiểm tra)

        // Lấy tất cả đánh giá của nhân viên này
        $ratings = Rating::where('quan_tri_vien_id', $staffId)
            ->whereNotNull('quan_tri_vien_id')
            ->with(['hoSo.dichVu', 'hoSo.donVi', 'nguoiDung'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Tính thống kê
        $stats = [
            'total_ratings' => Rating::where('quan_tri_vien_id', $staffId)
                ->whereNotNull('quan_tri_vien_id')
                ->count(),
            'average_rating' => Rating::where('quan_tri_vien_id', $staffId)
                ->whereNotNull('quan_tri_vien_id')
                ->avg('diem') ?? 0,
            'avg_thai_do' => Rating::where('quan_tri_vien_id', $staffId)
                ->whereNotNull('quan_tri_vien_id')
                ->whereNotNull('diem_thai_do')
                ->avg('diem_thai_do') ?? 0,
            'avg_thoi_gian' => Rating::where('quan_tri_vien_id', $staffId)
                ->whereNotNull('quan_tri_vien_id')
                ->whereNotNull('diem_thoi_gian')
                ->avg('diem_thoi_gian') ?? 0,
            'five_star' => Rating::where('quan_tri_vien_id', $staffId)
                ->whereNotNull('quan_tri_vien_id')
                ->where('diem', 5)
                ->count(),
            'four_star' => Rating::where('quan_tri_vien_id', $staffId)
                ->whereNotNull('quan_tri_vien_id')
                ->where('diem', 4)
                ->count(),
            'three_star' => Rating::where('quan_tri_vien_id', $staffId)
                ->whereNotNull('quan_tri_vien_id')
                ->where('diem', 3)
                ->count(),
            'two_star' => Rating::where('quan_tri_vien_id', $staffId)
                ->whereNotNull('quan_tri_vien_id')
                ->where('diem', 2)
                ->count(),
            'one_star' => Rating::where('quan_tri_vien_id', $staffId)
                ->whereNotNull('quan_tri_vien_id')
                ->where('diem', 1)
                ->count(),
        ];

        return view('backend.reports.staff-rating-detail', compact('staff', 'stats', 'ratings'));
    }
}