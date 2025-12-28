<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\DB;

class StaffRatingReportController extends Controller
{
    public function index()
    {
        // Lấy thống kê rating theo nhân viên (cán bộ - quan_tri_vien)
        $staffRatings = Rating::select(
            'quan_tri_vien.id',
            'quan_tri_vien.ho_ten',
            DB::raw('COUNT(ratings.id) as total_ratings'),
            DB::raw('AVG(ratings.diem) as average_rating'),
            DB::raw('COUNT(CASE WHEN ratings.diem = 5 THEN 1 END) as five_star'),
            DB::raw('COUNT(CASE WHEN ratings.diem = 4 THEN 1 END) as four_star'),
            DB::raw('COUNT(CASE WHEN ratings.diem = 3 THEN 1 END) as three_star'), 
            DB::raw('COUNT(CASE WHEN ratings.diem = 2 THEN 1 END) as two_star'),
            DB::raw('COUNT(CASE WHEN ratings.diem = 1 THEN 1 END) as one_star')
        )
        ->join('quan_tri_vien', 'ratings.quan_tri_vien_id', '=', 'quan_tri_vien.id')
        ->whereNotNull('ratings.quan_tri_vien_id')
        ->groupBy('quan_tri_vien.id', 'quan_tri_vien.ho_ten')
        ->orderBy('average_rating', 'desc')
        ->get();

        return view('backend.reports.staff-rating', compact('staffRatings'));
    }

    /**
     * Xem chi tiết đánh giá của một nhân viên
     */
    public function show($staffId)
    {
        $staff = \App\Models\Admin::findOrFail($staffId);
        
        // Lấy tất cả đánh giá của nhân viên này
        $ratings = Rating::where('quan_tri_vien_id', $staffId)
            ->with(['nguoiDung', 'hoSo.dichVu', 'hoSo.donVi'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Thống kê tổng quan
        $stats = [
            'total_ratings' => Rating::where('quan_tri_vien_id', $staffId)->count(),
            'average_rating' => Rating::where('quan_tri_vien_id', $staffId)->avg('diem') ?? 0,
            'five_star' => Rating::where('quan_tri_vien_id', $staffId)->where('diem', 5)->count(),
            'four_star' => Rating::where('quan_tri_vien_id', $staffId)->where('diem', 4)->count(),
            'three_star' => Rating::where('quan_tri_vien_id', $staffId)->where('diem', 3)->count(),
            'two_star' => Rating::where('quan_tri_vien_id', $staffId)->where('diem', 2)->count(),
            'one_star' => Rating::where('quan_tri_vien_id', $staffId)->where('diem', 1)->count(),
            'avg_thai_do' => Rating::where('quan_tri_vien_id', $staffId)->avg('diem_thai_do') ?? 0,
            'avg_thoi_gian' => Rating::where('quan_tri_vien_id', $staffId)->avg('diem_thoi_gian') ?? 0,
            'avg_chat_luong' => Rating::where('quan_tri_vien_id', $staffId)->avg('diem_chat_luong') ?? 0,
            'avg_co_so_vat_chat' => Rating::where('quan_tri_vien_id', $staffId)->avg('diem_co_so_vat_chat') ?? 0,
        ];

        return view('backend.reports.staff-rating-detail', compact('staff', 'ratings', 'stats'));
    }

    public function getChartData()
    {
        // Lấy dữ liệu cho biểu đồ theo cán bộ
        $chartData = Rating::select(
            'quan_tri_vien.ho_ten as ten',
            DB::raw('AVG(ratings.diem) as average_rating'),
            DB::raw('COUNT(ratings.id) as total_ratings')
        )
        ->join('quan_tri_vien', 'ratings.quan_tri_vien_id', '=', 'quan_tri_vien.id')
        ->whereNotNull('ratings.quan_tri_vien_id')
        ->groupBy('quan_tri_vien.id', 'quan_tri_vien.ho_ten')
        ->orderBy('average_rating', 'desc')
        ->get();

        return response()->json($chartData);
    }
}