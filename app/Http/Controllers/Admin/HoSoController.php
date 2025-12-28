<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HoSo;
use App\Models\ThongBao;
use App\Models\DonVi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HoSoController extends Controller
{
    /**
     * Danh sách hồ sơ được phân công
     */
    public function index(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        $baseQuery = HoSo::with(['dichVu', 'nguoiDung', 'donVi', 'quanTriVien']);

        // Phân quyền xem hồ sơ
        if ($currentUser->isCanBo()) {
            // Cán bộ: Chỉ xem hồ sơ được phân công cho mình, sắp xếp theo số thứ tự
            $baseQuery->where('quan_tri_vien_id', $currentUser->id)
                      ->orderBy('ngay_hen', 'asc')
                      ->orderBy('so_thu_tu', 'asc');
        } elseif ($currentUser->isAdminPhuong()) {
            // Admin phường: Xem tất cả hồ sơ của phường, sắp xếp theo nhân viên
            $baseQuery->where('don_vi_id', $currentUser->don_vi_id)
                      ->orderBy('quan_tri_vien_id', 'asc')
                      ->orderBy('ngay_hen', 'asc')
                      ->orderBy('so_thu_tu', 'asc');
        } else {
            // Admin tổng: Phải chọn phường trước
            if ($request->filled('don_vi_id')) {
                $baseQuery->where('don_vi_id', $request->don_vi_id)
                          ->orderBy('ngay_hen', 'asc')
                          ->orderBy('so_thu_tu', 'asc');
            } else {
                // Chưa chọn phường, không hiển thị hồ sơ nào
                $baseQuery->whereRaw('1 = 0');
            }
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $baseQuery->where('trang_thai', $request->trang_thai);
        }

        // Lọc theo ngày
        if ($request->filled('tu_ngay')) {
            $baseQuery->whereDate('ngay_hen', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $baseQuery->whereDate('ngay_hen', '<=', $request->den_ngay);
        }

        // Tìm kiếm theo mã hồ sơ hoặc tên người dùng
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function($q) use ($search) {
                $q->where('ma_ho_so', 'like', "%{$search}%")
                  ->orWhereHas('nguoiDung', function($q2) use ($search) {
                      $q2->where('ten', 'like', "%{$search}%")
                         ->orWhere('cccd', 'like', "%{$search}%");
                  });
            });
        }

        // Lấy dữ liệu
        $hoSos = $baseQuery->get();

        // Group theo phường (Admin tổng) hoặc nhân viên (Admin phường)
        $groupedHoSos = [];
        if ($currentUser->isAdmin()) {
            // Admin tổng: Không group nữa vì đã filter theo phường
            $groupedHoSos = [null => $hoSos];
        } elseif ($currentUser->isAdminPhuong()) {
            // Admin phường: Group theo nhân viên
            $groupedHoSos = $hoSos->groupBy('quan_tri_vien_id');
        } else {
            // Cán bộ: Không group, hiển thị trực tiếp
            $groupedHoSos = [null => $hoSos];
        }

        // Thống kê nhanh (theo quyền)
        $statsQuery = HoSo::query();
        if ($currentUser->isCanBo()) {
            $statsQuery->where('quan_tri_vien_id', $currentUser->id);
        } elseif ($currentUser->isAdminPhuong()) {
            $statsQuery->where('don_vi_id', $currentUser->don_vi_id);
        } elseif ($currentUser->isAdmin() && $request->filled('don_vi_id')) {
            $statsQuery->where('don_vi_id', $request->don_vi_id);
        }
        
        $stats = [
            'tong' => $statsQuery->count(),
            'da_tiep_nhan' => (clone $statsQuery)->where('trang_thai', HoSo::STATUS_RECEIVED)->count(),
            'dang_xu_ly' => (clone $statsQuery)->where('trang_thai', HoSo::STATUS_PROCESSING)->count(),
            'can_bo_sung' => (clone $statsQuery)->where('trang_thai', HoSo::STATUS_NEED_SUPPLEMENT)->count(),
            'hoan_tat' => (clone $statsQuery)->where('trang_thai', HoSo::STATUS_COMPLETED)->count(),
        ];

        // Lấy danh sách phường cho Admin tổng
        $donVis = [];
        if ($currentUser->isAdmin()) {
            $donVis = DonVi::orderBy('ten_don_vi')->get();
        }

        // Lấy danh sách cán bộ của phường để phân công (cho Admin phường và Admin tổng)
        $canBoList = [];
        if ($currentUser->isAdminPhuong()) {
            $canBoList = \App\Models\Admin::where('don_vi_id', $currentUser->don_vi_id)
                ->where('quyen', \App\Models\Admin::CAN_BO)
                ->orderBy('ho_ten')
                ->get();
        } elseif ($currentUser->isAdmin() && $request->filled('don_vi_id')) {
            $canBoList = \App\Models\Admin::where('don_vi_id', $request->don_vi_id)
                ->where('quyen', \App\Models\Admin::CAN_BO)
                ->orderBy('ho_ten')
                ->get();
        }

        return view('backend.ho-so.index', compact('hoSos', 'groupedHoSos', 'stats', 'currentUser', 'donVis', 'canBoList'));
    }

    /**
     * Xem chi tiết hồ sơ
     */
    public function show($id)
    {
        $currentUser = Auth::guard('admin')->user();
        
        $hoSo = HoSo::with(['dichVu', 'nguoiDung', 'donVi', 'quanTriVien', 'hoSoFields', 'rating'])
            ->findOrFail($id);

        // Kiểm tra quyền xem
        if ($currentUser->isCanBo() && $hoSo->quan_tri_vien_id != $currentUser->id) {
            abort(403, 'Bạn không có quyền xem hồ sơ này.');
        }
        if ($currentUser->isAdminPhuong() && $hoSo->don_vi_id != $currentUser->don_vi_id) {
            abort(403, 'Bạn không có quyền xem hồ sơ này.');
        }

        return view('backend.ho-so.show', compact('hoSo'));
    }

    /**
     * Cập nhật trạng thái hồ sơ
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|in:' . implode(',', HoSo::STATUS_OPTIONS),
            'ghi_chu_xu_ly' => 'nullable|string|max:1000',
        ], [
            'trang_thai.required' => 'Vui lòng chọn trạng thái.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
        ]);

        $currentUser = Auth::guard('admin')->user();
        $hoSo = HoSo::findOrFail($id);

        // CHỈ CÁN BỘ mới được cập nhật trạng thái
        if (!$currentUser->isCanBo()) {
            return back()->withErrors(['error' => 'Chỉ cán bộ mới được cập nhật trạng thái hồ sơ.']);
        }

        // Kiểm tra quyền cập nhật - chỉ cán bộ được phân công mới được cập nhật
        if ($hoSo->quan_tri_vien_id != $currentUser->id) {
            return back()->withErrors(['error' => 'Bạn chỉ có thể cập nhật hồ sơ được phân công cho mình.']);
        }

        // Nếu chưa có cán bộ xử lý, tự động gán cho người cập nhật
        if (!$hoSo->quan_tri_vien_id) {
            $hoSo->quan_tri_vien_id = $currentUser->id;
        }

        $oldStatus = $hoSo->trang_thai;
        $hoSo->trang_thai = $request->trang_thai;
        
        // Thêm ghi chú xử lý vào ghi_chu hiện tại
        if ($request->filled('ghi_chu_xu_ly')) {
            $timestamp = now()->format('d/m/Y H:i');
            $newNote = "[{$timestamp}] {$currentUser->ho_ten}: {$request->ghi_chu_xu_ly}";
            $hoSo->ghi_chu = $hoSo->ghi_chu ? $hoSo->ghi_chu . "\n" . $newNote : $newNote;
        }

        $hoSo->save();

        // Gửi thông báo cho người dùng
        if ($oldStatus != $request->trang_thai) {
            $message = $this->getStatusChangeMessage($request->trang_thai, $hoSo->ma_ho_so);
            
            ThongBao::create([
                'ho_so_id' => $hoSo->id,
                'nguoi_dung_id' => $hoSo->nguoi_dung_id,
                'dich_vu_id' => $hoSo->dich_vu_id,
                'ngay_hen' => $hoSo->ngay_hen,
                'message' => $message,
                'is_read' => false, // Mặc định chưa đọc
            ]);
        }

        return back()->with('success', 'Cập nhật trạng thái hồ sơ thành công!');
    }

    /**
     * Phân công hồ sơ cho cán bộ (Admin phường)
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'quan_tri_vien_id' => 'nullable|exists:quan_tri_vien,id',
        ]);

        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isAdminPhuong() && !$currentUser->isAdmin()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền phân công hồ sơ.'], 403);
            }
            return back()->withErrors(['error' => 'Bạn không có quyền phân công hồ sơ.']);
        }

        $hoSo = HoSo::findOrFail($id);

        if ($currentUser->isAdminPhuong() && $hoSo->don_vi_id != $currentUser->don_vi_id) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Bạn chỉ có thể phân công hồ sơ của phường mình.'], 403);
            }
            return back()->withErrors(['error' => 'Bạn chỉ có thể phân công hồ sơ của phường mình.']);
        }

        // Kiểm tra cán bộ có thuộc phường không (nếu có chọn cán bộ)
        if ($request->quan_tri_vien_id) {
            $canBo = \App\Models\Admin::find($request->quan_tri_vien_id);
            if ($canBo && $canBo->don_vi_id != $hoSo->don_vi_id) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Cán bộ không thuộc phường của hồ sơ này.'], 400);
                }
                return back()->withErrors(['error' => 'Cán bộ không thuộc phường của hồ sơ này.']);
            }
        }

        $hoSo->quan_tri_vien_id = $request->quan_tri_vien_id ?: null;
        $hoSo->save();
        
        // Load lại relationship để đảm bảo dữ liệu đồng bộ
        $hoSo->load('quanTriVien');

        if ($request->ajax()) {
            $canBoName = $hoSo->quanTriVien ? $hoSo->quanTriVien->ho_ten : 'Chưa phân công';
            return response()->json([
                'success' => true, 
                'message' => 'Phân công hồ sơ thành công!',
                'can_bo_name' => $canBoName
            ]);
        }

        return back()->with('success', 'Phân công hồ sơ thành công!');
    }

    /**
     * Hủy hồ sơ (Admin)
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'ly_do_huy' => 'required|string|max:500',
        ]);

        $currentUser = Auth::guard('admin')->user();
        $hoSo = HoSo::findOrFail($id);

        if (!$hoSo->canBeCancelled()) {
            return back()->withErrors(['error' => 'Không thể hủy hồ sơ đã hoàn tất hoặc đã hủy.']);
        }

        // Kiểm tra quyền
        if ($currentUser->isCanBo()) {
            return back()->withErrors(['error' => 'Cán bộ không có quyền hủy hồ sơ.']);
        }
        if ($currentUser->isAdminPhuong() && $hoSo->don_vi_id != $currentUser->don_vi_id) {
            return back()->withErrors(['error' => 'Bạn chỉ có thể hủy hồ sơ của phường mình.']);
        }

        $hoSo->trang_thai = HoSo::STATUS_CANCELLED;
        $hoSo->ly_do_huy = $request->ly_do_huy;
        $hoSo->cancelled_at = now();
        $hoSo->save();

        // Gửi thông báo
        ThongBao::create([
            'ho_so_id' => $hoSo->id,
            'nguoi_dung_id' => $hoSo->nguoi_dung_id,
            'dich_vu_id' => $hoSo->dich_vu_id,
            'ngay_hen' => $hoSo->ngay_hen,
            'message' => "Hồ sơ {$hoSo->ma_ho_so} đã bị hủy. Lý do: {$request->ly_do_huy}",
        ]);

        return back()->with('success', 'Hủy hồ sơ thành công!');
    }

    /**
     * Lấy message thông báo theo trạng thái
     */
    private function getStatusChangeMessage($status, $maHoSo)
    {
        $messages = [
            HoSo::STATUS_RECEIVED => "Hồ sơ {$maHoSo} đã được tiếp nhận. Chúng tôi sẽ xử lý trong thời gian sớm nhất.",
            HoSo::STATUS_PROCESSING => "Hồ sơ {$maHoSo} đang được xử lý. Vui lòng theo dõi thông báo tiếp theo.",
            HoSo::STATUS_NEED_SUPPLEMENT => "Hồ sơ {$maHoSo} cần bổ sung thêm giấy tờ. Vui lòng kiểm tra chi tiết và liên hệ với chúng tôi.",
            HoSo::STATUS_COMPLETED => "Hồ sơ {$maHoSo} đã hoàn tất! Vui lòng đến nhận kết quả theo lịch hẹn. Đánh giá dịch vụ của chúng tôi nhé!",
            HoSo::STATUS_CANCELLED => "Hồ sơ {$maHoSo} đã bị hủy.",
        ];

        return $messages[$status] ?? "Trạng thái hồ sơ {$maHoSo} đã được cập nhật.";
    }
}
