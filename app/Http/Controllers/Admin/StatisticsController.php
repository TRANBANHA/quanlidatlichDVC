<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HoSo;
use App\Models\Service;
use App\Models\Rating;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    /**
     * Thống kê cho Admin phường
     */
    public function index(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isAdminPhuong()) {
            abort(403, 'Chỉ Admin phường mới có quyền xem thống kê.');
        }

        $tuNgay = $request->get('tu_ngay', now()->subMonth()->format('Y-m-d'));
        $denNgay = $request->get('den_ngay', now()->format('Y-m-d'));

        // Thống kê số lượt theo dịch vụ
        $thongKeTheoDichVu = HoSo::select('dich_vu_id', DB::raw('count(*) as so_luong'))
            ->where('don_vi_id', $currentUser->don_vi_id)
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->with('dichVu')
            ->groupBy('dich_vu_id')
            ->orderByDesc('so_luong')
            ->get();

        // Thời gian chờ trung bình (từ lúc tạo đến khi hoàn tất)
        $thoiGianChoTB = HoSo::where('don_vi_id', $currentUser->don_vi_id)
            ->where('trang_thai', HoSo::STATUS_COMPLETED)
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as tb_ngay')
            ->first();

        // Khung giờ cao điểm (theo giờ hẹn)
        $khungGioCaoDiem = HoSo::select(DB::raw('HOUR(gio_hen) as gio'), DB::raw('count(*) as so_luong'))
            ->where('don_vi_id', $currentUser->don_vi_id)
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->whereNotNull('gio_hen')
            ->groupBy('gio')
            ->orderByDesc('so_luong')
            ->take(5)
            ->get();

        // Thống kê đánh giá
        $thongKeDanhGia = Rating::select(DB::raw('AVG(diem) as diem_tb'), DB::raw('COUNT(*) as so_luong'))
            ->whereHas('hoSo', function($q) use ($currentUser) {
                $q->where('don_vi_id', $currentUser->don_vi_id);
            })
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->first();

        // Phân bố điểm đánh giá
        $phanBoDiem = Rating::select(DB::raw('diem'), DB::raw('COUNT(*) as so_luong'))
            ->whereHas('hoSo', function($q) use ($currentUser) {
                $q->where('don_vi_id', $currentUser->don_vi_id);
            })
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->groupBy('diem')
            ->orderBy('diem')
            ->get();

        // Thống kê theo trạng thái
        $thongKeTrangThai = HoSo::select('trang_thai', DB::raw('count(*) as so_luong'))
            ->where('don_vi_id', $currentUser->don_vi_id)
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->groupBy('trang_thai')
            ->get();

        // Xu hướng theo ngày
        $xuHuong = HoSo::select(DB::raw('DATE(created_at) as ngay'), DB::raw('count(*) as so_luong'))
            ->where('don_vi_id', $currentUser->don_vi_id)
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->groupBy('ngay')
            ->orderBy('ngay')
            ->get();

        return view('backend.statistics.index', compact(
            'thongKeTheoDichVu',
            'thoiGianChoTB',
            'khungGioCaoDiem',
            'thongKeDanhGia',
            'phanBoDiem',
            'thongKeTrangThai',
            'xuHuong',
            'tuNgay',
            'denNgay'
        ));
    }
}

