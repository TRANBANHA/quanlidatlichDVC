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
}