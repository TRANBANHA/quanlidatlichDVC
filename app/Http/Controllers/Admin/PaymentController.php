<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\HoSo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Danh sách thanh toán
     */
    public function index(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        $query = Payment::with(['user', 'hoSo.dichVu', 'hoSo.donVi'])
            ->orderBy('created_at', 'desc');

        // Phân quyền
        if ($currentUser->isAdminPhuong()) {
            // Admin phường: Chỉ xem thanh toán của phường mình
            $query->whereHas('hoSo', function($q) use ($currentUser) {
                $q->where('don_vi_id', $currentUser->don_vi_id);
            });
        } elseif ($currentUser->isCanBo()) {
            // Cán bộ: Không có quyền xem
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }
        // Admin tổng: Xem tất cả

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai_thanh_toan', $request->trang_thai);
        }

        // Lọc theo phương thức thanh toán
        if ($request->filled('phuong_thuc')) {
            $query->where('phuong_thuc_thanh_toan', $request->phuong_thuc);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ma_giao_dich', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('ten', 'like', '%' . $search . '%')
                        ->orWhere('so_dien_thoai', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('hoSo', function($q) use ($search) {
                      $q->where('ma_ho_so', 'like', '%' . $search . '%');
                  });
            });
        }

        $payments = $query->paginate(20);

        $stats = [
            'total' => Payment::when($currentUser->isAdminPhuong(), function($q) use ($currentUser) {
                $q->whereHas('hoSo', function($q) use ($currentUser) {
                    $q->where('don_vi_id', $currentUser->don_vi_id);
                });
            })->count(),
            'cho_thanh_toan' => Payment::where('trang_thai_thanh_toan', 'cho_thanh_toan')
                ->when($currentUser->isAdminPhuong(), function($q) use ($currentUser) {
                    $q->whereHas('hoSo', function($q) use ($currentUser) {
                        $q->where('don_vi_id', $currentUser->don_vi_id);
                    });
                })->count(),
            'da_thanh_toan' => Payment::where('trang_thai_thanh_toan', 'da_thanh_toan')
                ->when($currentUser->isAdminPhuong(), function($q) use ($currentUser) {
                    $q->whereHas('hoSo', function($q) use ($currentUser) {
                        $q->where('don_vi_id', $currentUser->don_vi_id);
                    });
                })->count(),
            'co_anh' => Payment::whereNotNull('hinh_anh')
                ->where('trang_thai_thanh_toan', 'cho_thanh_toan')
                ->when($currentUser->isAdminPhuong(), function($q) use ($currentUser) {
                    $q->whereHas('hoSo', function($q) use ($currentUser) {
                        $q->where('don_vi_id', $currentUser->don_vi_id);
                    });
                })->count(),
        ];

        return view('backend.payments.index', compact('payments', 'stats'));
    }

    /**
     * Xem chi tiết thanh toán
     */
    public function show($id)
    {
        $payment = Payment::with(['user', 'hoSo.dichVu', 'hoSo.donVi'])->findOrFail($id);
        
        $currentUser = Auth::guard('admin')->user();

        // Kiểm tra quyền
        if ($currentUser->isAdminPhuong()) {
            if ($payment->hoSo->don_vi_id != $currentUser->don_vi_id) {
                abort(403, 'Bạn không có quyền xem thanh toán này.');
            }
        } elseif ($currentUser->isCanBo()) {
            abort(403, 'Bạn không có quyền xem thanh toán này.');
        }

        return view('backend.payments.show', compact('payment'));
    }

    /**
     * Xác nhận thanh toán
     */
    public function approve($id)
    {
        $payment = Payment::with('hoSo')->findOrFail($id);
        
        $currentUser = Auth::guard('admin')->user();

        // Kiểm tra quyền - CHỈ Admin phường mới được xác nhận
        if ($currentUser->isAdmin()) {
            // Admin tổng KHÔNG được xác nhận
            return response()->json([
                'success' => false,
                'message' => 'Admin tổng chỉ có quyền xem, không được xác nhận thanh toán.'
            ], 403);
        } elseif ($currentUser->isAdminPhuong()) {
            if ($payment->hoSo->don_vi_id != $currentUser->don_vi_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xác nhận thanh toán này.'
                ], 403);
            }
        } else {
            // Cán bộ không có quyền
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xác nhận thanh toán.'
            ], 403);
        }

        if ($payment->trang_thai_thanh_toan == 'da_thanh_toan') {
            return response()->json([
                'success' => false,
                'message' => 'Thanh toán này đã được xác nhận rồi.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $payment->update([
                'trang_thai_thanh_toan' => 'da_thanh_toan',
                'ngay_thanh_toan' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xác nhận thanh toán thành công!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Từ chối thanh toán
     */
    public function reject($id, Request $request)
    {
        $payment = Payment::with('hoSo')->findOrFail($id);
        
        $currentUser = Auth::guard('admin')->user();

        // Kiểm tra quyền - CHỈ Admin phường mới được từ chối
        if ($currentUser->isAdmin()) {
            // Admin tổng KHÔNG được từ chối
            return response()->json([
                'success' => false,
                'message' => 'Admin tổng chỉ có quyền xem, không được từ chối thanh toán.'
            ], 403);
        } elseif ($currentUser->isAdminPhuong()) {
            if ($payment->hoSo->don_vi_id != $currentUser->don_vi_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền từ chối thanh toán này.'
                ], 403);
            }
        } else {
            // Cán bộ không có quyền
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền từ chối thanh toán.'
            ], 403);
        }

        $request->validate([
            'ly_do' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $payment->update([
                'trang_thai_thanh_toan' => 'that_bai',
                'giai_trinh' => ($payment->giai_trinh ? $payment->giai_trinh . "\n\n" : '') . 
                               'Lý do từ chối: ' . $request->ly_do . ' (Bởi: ' . $currentUser->ho_ten . ' - ' . now()->format('d/m/Y H:i') . ')',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã từ chối thanh toán!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}

