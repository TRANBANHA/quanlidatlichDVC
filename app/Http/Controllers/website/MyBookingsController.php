<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\HoSo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MyBookingsController extends Controller
{
    /**
     * Danh sách lịch hẹn của người dân
     */
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();
        
        $query = HoSo::where('nguoi_dung_id', $user->id)
            ->with(['dichVu', 'donVi', 'quanTriVien', 'rating'])
            ->orderBy('created_at', 'desc');

        // Lọc theo trạng thái
        if ($request->has('trang_thai') && $request->trang_thai) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Lọc theo ngày
        if ($request->has('ngay_tu') && $request->ngay_tu) {
            $query->where('ngay_hen', '>=', $request->ngay_tu);
        }
        if ($request->has('ngay_den') && $request->ngay_den) {
            $query->where('ngay_hen', '<=', $request->ngay_den);
        }

        $hoSos = $query->paginate(10);

        // Thống kê
        $stats = [
            'total' => HoSo::where('nguoi_dung_id', $user->id)->count(),
            'received' => HoSo::where('nguoi_dung_id', $user->id)
                ->where('trang_thai', HoSo::STATUS_RECEIVED)->count(),
            'processing' => HoSo::where('nguoi_dung_id', $user->id)
                ->where('trang_thai', HoSo::STATUS_PROCESSING)->count(),
            'completed' => HoSo::where('nguoi_dung_id', $user->id)
                ->where('trang_thai', HoSo::STATUS_COMPLETED)->count(),
            'cancelled' => HoSo::where('nguoi_dung_id', $user->id)
                ->where('trang_thai', HoSo::STATUS_CANCELLED)->count(),
        ];

        return view('website.my-bookings.index', compact('hoSos', 'stats'));
    }

    /**
     * Chi tiết lịch hẹn
     */
    public function show($id)
    {
        $hoSo = HoSo::with([
            'dichVu', 
            'donVi', 
            'quanTriVien', 
            'hoSoFields',
            'rating'
        ])->findOrFail($id);

        // Kiểm tra quyền
        if ($hoSo->nguoi_dung_id != Auth::guard('web')->id()) {
            abort(403, 'Bạn không có quyền xem hồ sơ này');
        }

        return view('website.my-bookings.detail', compact('hoSo'));
    }

    /**
     * Hủy lịch hẹn
     */
    public function cancel(Request $request, $id)
    {
        $hoSo = HoSo::findOrFail($id);

        // Kiểm tra quyền
        if ($hoSo->nguoi_dung_id != Auth::guard('web')->id()) {
            return back()->withErrors(['error' => 'Bạn không có quyền hủy hồ sơ này']);
        }

        // Kiểm tra có thể hủy không
        if (!$hoSo->canBeCancelled()) {
            return back()->withErrors(['error' => 'Hồ sơ này không thể hủy. Trạng thái hiện tại: ' . $hoSo->trang_thai]);
        }

        $request->validate([
            'ly_do_huy' => 'required|string|max:500'
        ]);

        $hoSo->update([
            'trang_thai' => HoSo::STATUS_CANCELLED,
            'ly_do_huy' => $request->ly_do_huy,
            'cancelled_at' => now(),
        ]);

        // Tạo thông báo
        \App\Models\ThongBao::create([
            'ho_so_id' => $hoSo->id,
            'nguoi_dung_id' => $hoSo->nguoi_dung_id,
            'dich_vu_id' => $hoSo->dich_vu_id,
            'ngay_hen' => $hoSo->ngay_hen,
            'message' => 'Bạn đã hủy lịch hẹn. Mã hồ sơ: ' . $hoSo->ma_ho_so,
        ]);

        return redirect()->route('my-bookings.index')
            ->with('success', 'Đã hủy lịch hẹn thành công');
    }
}

