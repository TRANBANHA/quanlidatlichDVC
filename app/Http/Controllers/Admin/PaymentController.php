<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\HoSo;
use App\Models\DonVi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Danh sách thanh toán (Chỉ dành cho Admin phường)
     */
    public function index(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Chỉ Admin phường mới được truy cập
        if (!$currentUser || !$currentUser->isAdminPhuong()) {
            abort(404, 'Bạn không có quyền truy cập trang này.');
        }

        $query = Payment::with(['user', 'hoSo.dichVu', 'hoSo.donVi'])
            ->whereHas('hoSo', function($q) use ($currentUser) {
                $q->where('don_vi_id', $currentUser->don_vi_id);
            });

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai_thanh_toan', $request->trang_thai);
        }

        // Tìm kiếm theo mã hồ sơ, mã giao dịch, tên người dùng
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ma_giao_dich', 'like', "%{$search}%")
                  ->orWhereHas('hoSo', function($q) use ($search) {
                      $q->where('ma_ho_so', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('ten', 'like', "%{$search}%")
                        ->orWhere('cccd', 'like', "%{$search}%");
                  });
            });
        }

        // Lọc theo khoảng thời gian
        if ($request->filled('tu_ngay')) {
            $query->whereDate('created_at', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->whereDate('created_at', '<=', $request->den_ngay);
        }

        $payments = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Thống kê
        $tongTien = Payment::whereHas('hoSo', function($q) use ($currentUser) {
                $q->where('don_vi_id', $currentUser->don_vi_id);
            })
            ->where('trang_thai_thanh_toan', 'da_thanh_toan')
            ->sum('so_tien');

        $tongSoGiaoDich = Payment::whereHas('hoSo', function($q) use ($currentUser) {
                $q->where('don_vi_id', $currentUser->don_vi_id);
            })
            ->count();

        $thongKeTrangThai = Payment::whereHas('hoSo', function($q) use ($currentUser) {
                $q->where('don_vi_id', $currentUser->don_vi_id);
            })
            ->select('trang_thai_thanh_toan', DB::raw('count(*) as so_luong'), DB::raw('sum(so_tien) as tong_tien'))
            ->groupBy('trang_thai_thanh_toan')
            ->get();

        return view('backend.payments.index', compact(
            'payments',
            'tongTien',
            'tongSoGiaoDich',
            'thongKeTrangThai'
        ));
    }

    /**
     * Chi tiết thanh toán
     */
    public function show($id)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser || !$currentUser->isAdminPhuong()) {
            abort(404, 'Bạn không có quyền truy cập trang này.');
        }

        $payment = Payment::with(['user', 'hoSo.dichVu', 'hoSo.donVi', 'hoSo.quanTriVien'])
            ->whereHas('hoSo', function($q) use ($currentUser) {
                $q->where('don_vi_id', $currentUser->don_vi_id);
            })
            ->findOrFail($id);

        return view('backend.payments.show', compact('payment'));
    }

    /**
     * Xác nhận thanh toán (Admin phường)
     */
    public function approve($id)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser || !$currentUser->isAdminPhuong()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $payment = Payment::with('hoSo')
            ->whereHas('hoSo', function($q) use ($currentUser) {
                $q->where('don_vi_id', $currentUser->don_vi_id);
            })
            ->findOrFail($id);

        // Kiểm tra trạng thái
        if ($payment->trang_thai_thanh_toan != 'cho_thanh_toan') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể xác nhận thanh toán đang chờ xử lý.'
            ], 400);
        }

        // Cập nhật trạng thái thanh toán
        $payment->trang_thai_thanh_toan = 'da_thanh_toan';
        $payment->ngay_thanh_toan = now();
        $payment->save();

        return response()->json([
            'success' => true,
            'message' => 'Xác nhận thanh toán thành công!'
        ]);
    }

    /**
     * Từ chối thanh toán (Admin phường)
     */
    public function reject(Request $request, $id)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser || !$currentUser->isAdminPhuong()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        $request->validate([
            'ly_do' => 'required|string|max:500',
        ], [
            'ly_do.required' => 'Vui lòng nhập lý do từ chối.',
            'ly_do.max' => 'Lý do không được vượt quá 500 ký tự.',
        ]);

        $payment = Payment::with('hoSo')
            ->whereHas('hoSo', function($q) use ($currentUser) {
                $q->where('don_vi_id', $currentUser->don_vi_id);
            })
            ->findOrFail($id);

        // Kiểm tra trạng thái
        if ($payment->trang_thai_thanh_toan != 'cho_thanh_toan') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể từ chối thanh toán đang chờ xử lý.'
            ], 400);
        }

        // Cập nhật trạng thái thanh toán
        $payment->trang_thai_thanh_toan = 'that_bai';
        $payment->giai_trinh = $request->ly_do;
        $payment->save();

        return response()->json([
            'success' => true,
            'message' => 'Từ chối thanh toán thành công!'
        ]);
    }
}

