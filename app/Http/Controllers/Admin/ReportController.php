<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HoSo;
use App\Models\DonVi;
use App\Models\Service;
use App\Models\ServicePhuong;
use App\Models\ServiceScheduleStaff;
use App\Models\Payment;
use App\Models\Rating;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Báo cáo theo role
     */
    public function index(Request $request)
    {
        try {
            $currentUser = Auth::guard('admin')->user();
            
            if (!$currentUser) {
                abort(403, 'Bạn chưa đăng nhập.');
            }
            
            // Phân loại theo role
            if ($currentUser->isAdmin()) {
                return $this->adminTongReport($request);
            } elseif ($currentUser->isAdminPhuong()) {
                return $this->adminPhuongReport($request);
            } elseif ($currentUser->isCanBo()) {
                return $this->canBoReport($request);
            }
            
            abort(403, 'Bạn không có quyền xem báo cáo.');
        } catch (\Exception $e) {
            \Log::error('ReportController index error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Báo cáo tổng hợp (Admin tổng): Các phường, dịch vụ, phí, số người làm
     */
    private function adminTongReport(Request $request)
    {
        // Lọc theo thời gian
        $tuNgay = $request->get('tu_ngay', now()->subMonth()->format('Y-m-d'));
        $denNgay = $request->get('den_ngay', now()->format('Y-m-d'));

        // Tổng số hồ sơ
        $tongHoSo = HoSo::whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])->count();
        
        // Phân bố theo phường
        $hoSoTheoPhuong = HoSo::select('don_vi_id', DB::raw('count(*) as so_luong'))
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->with('donVi')
            ->groupBy('don_vi_id')
            ->orderByDesc('so_luong')
            ->get();

        // Phân bố theo dịch vụ
        $hoSoTheoDichVu = HoSo::select('dich_vu_id', DB::raw('count(*) as so_luong'))
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->with('dichVu')
            ->groupBy('dich_vu_id')
            ->orderByDesc('so_luong')
            ->get();

        // Tổng tiền theo phường (lấy từ ho_so)
        $tongTienTheoPhuong = Payment::select('ho_so.don_vi_id', DB::raw('sum(thanh_toan.so_tien) as tong_tien'))
            ->join('ho_so', 'thanh_toan.ho_so_id', '=', 'ho_so.id')
            ->where('thanh_toan.trang_thai_thanh_toan', 'da_thanh_toan')
            ->whereBetween('thanh_toan.created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->whereNotNull('ho_so.don_vi_id')
            ->groupBy('ho_so.don_vi_id')
            ->orderByDesc('tong_tien')
            ->get()
            ->map(function($item) {
                $item->donVi = DonVi::find($item->don_vi_id);
                return $item;
            });

        // Tổng tiền theo dịch vụ (lấy từ ho_so)
        $tongTienTheoDichVu = Payment::select('ho_so.dich_vu_id', DB::raw('sum(thanh_toan.so_tien) as tong_tien'))
            ->join('ho_so', 'thanh_toan.ho_so_id', '=', 'ho_so.id')
            ->where('thanh_toan.trang_thai_thanh_toan', 'da_thanh_toan')
            ->whereBetween('thanh_toan.created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->whereNotNull('ho_so.dich_vu_id')
            ->groupBy('ho_so.dich_vu_id')
            ->orderByDesc('tong_tien')
            ->get()
            ->map(function($item) {
                $item->dichVu = Service::find($item->dich_vu_id);
                return $item;
            });

        // Xu hướng theo thời gian (theo ngày)
        $xuHuong = HoSo::select(DB::raw('DATE(created_at) as ngay'), DB::raw('count(*) as so_luong'))
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->groupBy('ngay')
            ->orderBy('ngay')
            ->get();

        // Bảng xếp hạng phường (theo số lượng hồ sơ)
        $bangXepHangPhuong = $hoSoTheoPhuong->take(10);

        // Bảng xếp hạng cán bộ (theo số lượng hồ sơ xử lý) với đánh giá chi tiết
        $bangXepHangCanBo = HoSo::select('quan_tri_vien_id', DB::raw('count(*) as so_luong'))
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->whereNotNull('quan_tri_vien_id')
            ->with('quanTriVien')
            ->groupBy('quan_tri_vien_id')
            ->orderByDesc('so_luong')
            ->take(10)
            ->get()
            ->map(function($item) use ($tuNgay, $denNgay) {
                // Lấy đánh giá chi tiết cho từng cán bộ
                $ratings = Rating::where('quan_tri_vien_id', $item->quan_tri_vien_id)
                    ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                    ->get();
                
                $item->total_ratings = $ratings->count();
                $item->average_rating = $ratings->avg('diem') ?? 0;
                $item->five_star = $ratings->where('diem', 5)->count();
                $item->four_star = $ratings->where('diem', 4)->count();
                $item->three_star = $ratings->where('diem', 3)->count();
                $item->two_star = $ratings->where('diem', 2)->count();
                $item->one_star = $ratings->where('diem', 1)->count();
                $item->avg_thai_do = $ratings->avg('diem_thai_do') ?? 0;
                $item->avg_thoi_gian = $ratings->avg('diem_thoi_gian') ?? 0;
                $item->avg_chat_luong = $ratings->avg('diem_chat_luong') ?? 0;
                
                return $item;
            });

        // Thống kê đánh giá
        $thongKeDanhGia = Rating::select(DB::raw('avg(diem) as diem_tb'), DB::raw('count(*) as so_luong'))
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->first();
        
        // Đảm bảo thongKeDanhGia không null
        if (!$thongKeDanhGia) {
            $thongKeDanhGia = (object)['diem_tb' => 0, 'so_luong' => 0];
        }

        // Thống kê đánh giá theo nhân viên (tất cả cán bộ)
        $danhGiaNhanVien = Rating::select(
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
            ->whereDate('ratings.created_at', '>=', $tuNgay)
            ->whereDate('ratings.created_at', '<=', $denNgay)
            ->groupBy('quan_tri_vien.id', 'quan_tri_vien.ho_ten')
            ->orderByDesc('average_rating')
            ->get();
        
        // Nếu không có dữ liệu trong khoảng thời gian, hiển thị tất cả đánh giá
        if ($danhGiaNhanVien->isEmpty()) {
            $danhGiaNhanVien = Rating::select(
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
                ->orderByDesc('average_rating')
                ->get();
        }

        $donVis = DonVi::all();

        // Số người làm việc theo phường (cán bộ)
        $soNguoiLamTheoPhuong = Admin::select('don_vi_id', DB::raw('count(*) as so_luong'))
            ->where('quyen', Admin::CAN_BO)
            ->groupBy('don_vi_id')
            ->get()
            ->map(function($item) {
                $item->donVi = DonVi::find($item->don_vi_id);
                return $item;
            });

        // Chi tiết dịch vụ theo phường
        $chiTietDichVuTheoPhuong = HoSo::select('don_vi_id', 'dich_vu_id', DB::raw('count(*) as so_luong'))
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->with(['donVi', 'dichVu'])
            ->groupBy('don_vi_id', 'dich_vu_id')
            ->orderByDesc('so_luong')
            ->get();

        return view('backend.reports.admin-tong', compact(
            'tongHoSo',
            'hoSoTheoPhuong',
            'hoSoTheoDichVu',
            'tongTienTheoPhuong',
            'tongTienTheoDichVu',
            'xuHuong',
            'bangXepHangPhuong',
            'bangXepHangCanBo',
            'thongKeDanhGia',
            'danhGiaNhanVien',
            'tuNgay',
            'denNgay',
            'donVis',
            'soNguoiLamTheoPhuong',
            'chiTietDichVuTheoPhuong'
        ));
    }

    /**
     * Chi tiết báo cáo theo phường
     */
    public function showPhuongDetail(Request $request, $donViId)
    {
        try {
            $currentUser = Auth::guard('admin')->user();
            
            if (!$currentUser || !$currentUser->isAdmin()) {
                abort(403, 'Bạn không có quyền xem báo cáo này.');
            }

            $donVi = DonVi::findOrFail($donViId);
            
            // Lọc theo thời gian
            $tuNgay = $request->get('tu_ngay', now()->subMonth()->format('Y-m-d'));
            $denNgay = $request->get('den_ngay', now()->format('Y-m-d'));

            // Tổng số hồ sơ của phường
            $tongHoSo = HoSo::where('don_vi_id', $donViId)
                ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                ->count();

            // Hồ sơ theo dịch vụ
            $hoSoTheoDichVu = HoSo::select('dich_vu_id', DB::raw('count(*) as so_luong'))
                ->where('don_vi_id', $donViId)
                ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                ->whereNotNull('dich_vu_id')
                ->with('dichVu')
                ->groupBy('dich_vu_id')
                ->orderByDesc('so_luong')
                ->get();

            // Tổng phí theo dịch vụ
            $tongTienTheoDichVu = Payment::join('ho_so', 'thanh_toan.ho_so_id', '=', 'ho_so.id')
                ->where('ho_so.don_vi_id', $donViId)
                ->where('thanh_toan.trang_thai_thanh_toan', 'da_thanh_toan')
                ->whereBetween('thanh_toan.created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                ->whereNotNull('ho_so.dich_vu_id')
                ->select('ho_so.dich_vu_id', DB::raw('sum(thanh_toan.so_tien) as tong_tien'))
                ->groupBy('ho_so.dich_vu_id')
                ->get()
                ->map(function($item) {
                    $item->dichVu = Service::find($item->dich_vu_id);
                    return $item;
                });

            // Tổng phí của phường
            $tongTien = Payment::join('ho_so', 'thanh_toan.ho_so_id', '=', 'ho_so.id')
                ->where('ho_so.don_vi_id', $donViId)
                ->where('thanh_toan.trang_thai_thanh_toan', 'da_thanh_toan')
                ->whereBetween('thanh_toan.created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                ->whereNotNull('ho_so.don_vi_id')
                ->sum('thanh_toan.so_tien');

            // Hồ sơ theo trạng thái
            $hoSoTheoTrangThai = HoSo::select('trang_thai', DB::raw('count(*) as so_luong'))
                ->where('don_vi_id', $donViId)
                ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                ->groupBy('trang_thai')
                ->get()
                ->pluck('so_luong', 'trang_thai');

            // Xu hướng theo ngày
            $xuHuong = HoSo::select(DB::raw('DATE(created_at) as ngay'), DB::raw('count(*) as so_luong'))
                ->where('don_vi_id', $donViId)
                ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                ->groupBy('ngay')
                ->orderBy('ngay')
                ->get();

            // Danh sách cán bộ trong phường
            $danhSachCanBo = Admin::where('don_vi_id', $donViId)
                ->where('quyen', Admin::CAN_BO)
                ->withCount(['hoSo' => function($query) use ($tuNgay, $denNgay) {
                    $query->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59']);
                }])
                ->get();

            // Đánh giá trung bình
            $thongKeDanhGia = Rating::join('ho_so', 'ratings.ho_so_id', '=', 'ho_so.id')
                ->where('ho_so.don_vi_id', $donViId)
                ->whereBetween('ratings.created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                ->select(DB::raw('avg(ratings.diem) as diem_tb'), DB::raw('count(*) as so_luong'))
                ->first();

            if (!$thongKeDanhGia) {
                $thongKeDanhGia = (object)['diem_tb' => 0, 'so_luong' => 0];
            }

            // Hồ sơ theo cán bộ
            $hoSoTheoCanBo = HoSo::select('quan_tri_vien_id', DB::raw('count(*) as so_luong'))
                ->where('don_vi_id', $donViId)
                ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                ->whereNotNull('quan_tri_vien_id')
                ->with('quanTriVien')
                ->groupBy('quan_tri_vien_id')
                ->orderByDesc('so_luong')
                ->take(10)
                ->get();

            // Đánh giá nhân viên chi tiết
            $danhGiaNhanVien = Rating::select(
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
                ->join('ho_so', 'ratings.ho_so_id', '=', 'ho_so.id')
                ->join('quan_tri_vien', 'ho_so.quan_tri_vien_id', '=', 'quan_tri_vien.id')
                ->where('ho_so.don_vi_id', $donViId)
                ->whereBetween('ratings.created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                ->whereNotNull('ho_so.quan_tri_vien_id')
                ->groupBy('quan_tri_vien.id', 'quan_tri_vien.ho_ten')
                ->orderByDesc('average_rating')
                ->get();

            return view('backend.reports.phuong-detail', compact(
                'donVi',
                'tongHoSo',
                'hoSoTheoDichVu',
                'tongTienTheoDichVu',
                'tongTien',
                'hoSoTheoTrangThai',
                'xuHuong',
                'danhSachCanBo',
                'thongKeDanhGia',
                'hoSoTheoCanBo',
                'danhGiaNhanVien',
                'tuNgay',
                'denNgay'
            ));
        } catch (\Exception $e) {
            \Log::error('ReportController showPhuongDetail error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('reports.index')->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Báo cáo Admin phường: Dịch vụ, nhân viên, phí
     */
    private function adminPhuongReport(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        $donViId = $currentUser->don_vi_id;

        if (!$donViId) {
            abort(403, 'Bạn chưa được gán vào phường nào.');
        }

        $tuNgay = $request->get('tu_ngay', now()->subMonth()->format('Y-m-d'));
        $denNgay = $request->get('den_ngay', now()->format('Y-m-d'));

        // Báo cáo dịch vụ
        $dichVuTheoPhuong = HoSo::select('dich_vu_id', DB::raw('count(*) as so_luong'))
            ->where('don_vi_id', $donViId)
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->with('dichVu')
            ->groupBy('dich_vu_id')
            ->orderByDesc('so_luong')
            ->get();

        // Báo cáo nhân viên (cán bộ) với đánh giá chi tiết
        $nhanVienTheoPhuong = HoSo::select('quan_tri_vien_id', DB::raw('count(*) as so_luong'))
            ->where('don_vi_id', $donViId)
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->whereNotNull('quan_tri_vien_id')
            ->with('quanTriVien')
            ->groupBy('quan_tri_vien_id')
            ->orderByDesc('so_luong')
            ->get()
            ->map(function($item) use ($tuNgay, $denNgay) {
                // Lấy đánh giá chi tiết cho từng nhân viên
                $ratings = Rating::where('quan_tri_vien_id', $item->quan_tri_vien_id)
                    ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                    ->get();
                
                $item->total_ratings = $ratings->count();
                $item->average_rating = $ratings->avg('diem') ?? 0;
                $item->five_star = $ratings->where('diem', 5)->count();
                $item->four_star = $ratings->where('diem', 4)->count();
                $item->three_star = $ratings->where('diem', 3)->count();
                $item->two_star = $ratings->where('diem', 2)->count();
                $item->one_star = $ratings->where('diem', 1)->count();
                $item->avg_thai_do = $ratings->avg('diem_thai_do') ?? 0;
                $item->avg_thoi_gian = $ratings->avg('diem_thoi_gian') ?? 0;
                $item->avg_chat_luong = $ratings->avg('diem_chat_luong') ?? 0;
                
                return $item;
            });

        // Tổng phí theo dịch vụ
        $phiTheoDichVu = Payment::select('ho_so.dich_vu_id', DB::raw('sum(thanh_toan.so_tien) as tong_tien'), DB::raw('count(*) as so_luong'))
            ->join('ho_so', 'thanh_toan.ho_so_id', '=', 'ho_so.id')
            ->where('ho_so.don_vi_id', $donViId)
            ->where('thanh_toan.trang_thai_thanh_toan', 'da_thanh_toan')
            ->whereBetween('thanh_toan.created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->whereNotNull('ho_so.dich_vu_id')
            ->groupBy('ho_so.dich_vu_id')
            ->orderByDesc('tong_tien')
            ->get()
            ->map(function($item) {
                $item->dichVu = Service::find($item->dich_vu_id);
                return $item;
            });

        // Tổng phí
        $tongPhi = Payment::join('ho_so', 'thanh_toan.ho_so_id', '=', 'ho_so.id')
            ->where('ho_so.don_vi_id', $donViId)
            ->where('thanh_toan.trang_thai_thanh_toan', 'da_thanh_toan')
            ->whereBetween('thanh_toan.created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->sum('thanh_toan.so_tien');

        // Danh sách nhân viên trong phường
        $danhSachNhanVien = Admin::where('don_vi_id', $donViId)
            ->where('quyen', Admin::CAN_BO)
            ->get();

        return view('backend.reports.admin-phuong', compact(
            'dichVuTheoPhuong',
            'nhanVienTheoPhuong',
            'phiTheoDichVu',
            'tongPhi',
            'danhSachNhanVien',
            'tuNgay',
            'denNgay',
            'donViId'
        ));
    }

    /**
     * Báo cáo Cán bộ: Hiệu quả làm việc, lịch làm việc
     */
    private function canBoReport(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        $canBoId = $currentUser->id;

        $tuNgay = $request->get('tu_ngay', now()->subMonth()->format('Y-m-d'));
        $denNgay = $request->get('den_ngay', now()->format('Y-m-d'));

        // Hiệu quả làm việc
        $hoSoDaXuLy = HoSo::where('quan_tri_vien_id', $canBoId)
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->get();

        $thongKeHieuQua = [
            'tong_ho_so' => $hoSoDaXuLy->count(),
            'da_hoan_tat' => $hoSoDaXuLy->where('trang_thai', HoSo::STATUS_COMPLETED)->count(),
            'dang_xu_ly' => $hoSoDaXuLy->where('trang_thai', HoSo::STATUS_PROCESSING)->count(),
            'can_bo_sung' => $hoSoDaXuLy->where('trang_thai', HoSo::STATUS_NEED_SUPPLEMENT)->count(),
        ];

        // Đánh giá từ người dùng với chi tiết
        $danhGia = Rating::where('quan_tri_vien_id', $canBoId)
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->with(['nguoiDung', 'hoSo.dichVu'])
            ->orderBy('created_at', 'desc')
            ->get();

        $thongKeDanhGia = [
            'tong_danh_gia' => $danhGia->count(),
            'diem_trung_binh' => $danhGia->avg('diem') ?? 0,
            'diem_thai_do_tb' => $danhGia->avg('diem_thai_do') ?? 0,
            'diem_thoi_gian_tb' => $danhGia->avg('diem_thoi_gian') ?? 0,
            'diem_chat_luong_tb' => $danhGia->avg('diem_chat_luong') ?? 0,
            'diem_co_so_vat_chat_tb' => $danhGia->avg('diem_co_so_vat_chat') ?? 0,
            'five_star' => $danhGia->where('diem', 5)->count(),
            'four_star' => $danhGia->where('diem', 4)->count(),
            'three_star' => $danhGia->where('diem', 3)->count(),
            'two_star' => $danhGia->where('diem', 2)->count(),
            'one_star' => $danhGia->where('diem', 1)->count(),
            'chi_tiet' => $danhGia, // Danh sách đánh giá chi tiết
        ];

        // Lịch làm việc (schedule)
        $lichLamViec = ServiceScheduleStaff::where('can_bo_id', $canBoId)
            ->with(['schedule.service', 'schedule'])
            ->get()
            ->groupBy(function($item) {
                return $item->schedule->thu_trong_tuan ?? 0;
            });

        // Hồ sơ theo ngày
        $hoSoTheoNgay = HoSo::select(DB::raw('DATE(created_at) as ngay'), DB::raw('count(*) as so_luong'))
            ->where('quan_tri_vien_id', $canBoId)
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->groupBy('ngay')
            ->orderBy('ngay')
            ->get();

        return view('backend.reports.can-bo', compact(
            'thongKeHieuQua',
            'thongKeDanhGia',
            'lichLamViec',
            'hoSoTheoNgay',
            'tuNgay',
            'denNgay',
            'canBoId'
        ));
    }

    /**
     * Xem hồ sơ theo phường (Admin tổng)
     */
    public function hoSoTheoPhuong(Request $request, $donViId = null)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isAdmin()) {
            abort(403);
        }

        $donViId = $donViId ?? $request->get('don_vi_id');
        
        $query = HoSo::with(['dichVu', 'nguoiDung', 'quanTriVien'])
            ->orderBy('created_at', 'desc');

        if ($donViId) {
            $query->where('don_vi_id', $donViId);
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Lọc theo thời gian
        if ($request->filled('tu_ngay')) {
            $query->whereDate('created_at', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->whereDate('created_at', '<=', $request->den_ngay);
        }

        $hoSos = $query->paginate(20);
        $donVis = DonVi::all();
        $donViSelected = $donViId ? DonVi::find($donViId) : null;

        return view('backend.reports.ho-so-phuong', compact('hoSos', 'donVis', 'donViSelected'));
    }

    /**
     * Tổng tiền theo phí dịch vụ theo phường
     */
    public function tongTienTheoPhuong(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isAdmin()) {
            abort(403);
        }

        $tuNgay = $request->get('tu_ngay', now()->subMonth()->format('Y-m-d'));
        $denNgay = $request->get('den_ngay', now()->format('Y-m-d'));

        // Tổng tiền theo phường (lấy từ ho_so)
        $tongTienTheoPhuong = Payment::select('ho_so.don_vi_id', DB::raw('sum(thanh_toan.so_tien) as tong_tien'), DB::raw('count(*) as so_luong'))
            ->join('ho_so', 'thanh_toan.ho_so_id', '=', 'ho_so.id')
            ->where('thanh_toan.trang_thai_thanh_toan', 'da_thanh_toan')
            ->whereBetween('thanh_toan.created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->groupBy('ho_so.don_vi_id')
            ->orderByDesc('tong_tien')
            ->get()
            ->map(function($item) {
                $item->donVi = DonVi::find($item->don_vi_id);
                return $item;
            });

        // Chi tiết theo dịch vụ trong từng phường
        $chiTietTheoDichVu = Payment::select('ho_so.don_vi_id', 'ho_so.dich_vu_id', DB::raw('sum(thanh_toan.so_tien) as tong_tien'), DB::raw('count(*) as so_luong'))
            ->join('ho_so', 'thanh_toan.ho_so_id', '=', 'ho_so.id')
            ->where('thanh_toan.trang_thai_thanh_toan', 'da_thanh_toan')
            ->whereBetween('thanh_toan.created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->groupBy('ho_so.don_vi_id', 'ho_so.dich_vu_id')
            ->orderByDesc('tong_tien')
            ->get()
            ->map(function($item) {
                $item->donVi = DonVi::find($item->don_vi_id);
                $item->dichVu = Service::find($item->dich_vu_id);
                return $item;
            });

        $tongTien = $tongTienTheoPhuong->sum('tong_tien');
        $donVis = DonVi::all();

        return view('backend.reports.tong-tien-phuong', compact(
            'tongTienTheoPhuong',
            'chiTietTheoDichVu',
            'tongTien',
            'tuNgay',
            'denNgay',
            'donVis'
        ));
    }

    /**
     * Xuất báo cáo Excel
     */
    public function exportExcel(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isAdmin()) {
            abort(403);
        }

        $tuNgay = $request->get('tu_ngay', now()->subMonth()->format('Y-m-d'));
        $denNgay = $request->get('den_ngay', now()->format('Y-m-d'));

        // Tạo dữ liệu báo cáo
        $data = [];
        
        // Tổng hợp theo phường
        $hoSoTheoPhuong = HoSo::select('don_vi_id', DB::raw('count(*) as so_luong'))
            ->whereBetween('created_at', [$tuNgay, $denNgay . ' 23:59:59'])
            ->with('donVi')
            ->groupBy('don_vi_id')
            ->get();

        foreach ($hoSoTheoPhuong as $item) {
            $tongTien = Payment::join('ho_so', 'thanh_toan.ho_so_id', '=', 'ho_so.id')
                ->where('ho_so.don_vi_id', $item->don_vi_id)
                ->where('thanh_toan.trang_thai_thanh_toan', 'da_thanh_toan')
                ->whereBetween('thanh_toan.created_at', [$tuNgay, $denNgay . ' 23:59:59'])
                ->sum('thanh_toan.so_tien');

            $data[] = [
                'Phường/Đơn vị' => $item->donVi->ten_don_vi ?? 'N/A',
                'Số lượng hồ sơ' => $item->so_luong,
                'Tổng tiền' => number_format($tongTien) . ' VNĐ',
            ];
        }

        return Excel::download(new \App\Exports\ReportExport($data), 'bao-cao-tong-hop-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Xuất báo cáo PDF
     */
    public function exportPDF(Request $request)
    {
        // Tương tự exportExcel nhưng xuất PDF
        // Cần cài đặt package như dompdf hoặc barryvdh/laravel-dompdf
        return redirect()->back()->with('info', 'Chức năng xuất PDF đang được phát triển.');
    }
}

