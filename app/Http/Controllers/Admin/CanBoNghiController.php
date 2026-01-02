<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CanBoNghi;
use App\Models\Admin;
use App\Services\ChuyenHoSoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CanBoNghiController extends Controller
{
    protected $chuyenHoSoService;

    public function __construct(ChuyenHoSoService $chuyenHoSoService)
    {
        $this->chuyenHoSoService = $chuyenHoSoService;
    }

    /**
     * Danh sách cán bộ báo nghỉ
     */
    public function index(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        $query = CanBoNghi::with(['canBo.donVi', 'nguoiDuyet'])
            ->orderBy('ngay_nghi', 'desc')
            ->orderBy('created_at', 'desc');

        // Phân quyền: Admin phường chỉ xem cán bộ của phường mình
        if ($currentUser->isAdminPhuong()) {
            $query->whereHas('canBo', function($q) use ($currentUser) {
                $q->where('don_vi_id', $currentUser->don_vi_id);
            });
        } elseif ($currentUser->isCanBo()) {
            // Cán bộ chỉ xem lịch nghỉ của mình
            $query->where('can_bo_id', $currentUser->id);
        }

        // Lọc theo ngày
        if ($request->filled('tu_ngay')) {
            $query->whereDate('ngay_nghi', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->whereDate('ngay_nghi', '<=', $request->den_ngay);
        }

        // Lọc theo cán bộ
        if ($request->filled('can_bo_id')) {
            $query->where('can_bo_id', $request->can_bo_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Admin phường: Ưu tiên hiển thị các báo nghỉ chờ duyệt
        if ($currentUser->isAdminPhuong()) {
            // Có thể thêm logic sắp xếp ưu tiên chờ duyệt
        }

        $danhSachNghi = $query->paginate(20)->withQueryString();

        // Lấy danh sách cán bộ để filter
        $canBoList = Admin::where('quyen', Admin::CAN_BO);
        if ($currentUser->isAdminPhuong()) {
            $canBoList->where('don_vi_id', $currentUser->don_vi_id);
        }
        $canBoList = $canBoList->get();

        return view('backend.can-bo-nghi.index', compact('danhSachNghi', 'canBoList'));
    }

    /**
     * Form báo nghỉ (cho cán bộ)
     */
    public function create()
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Chỉ cán bộ mới được báo nghỉ
        if (!$currentUser->isCanBo()) {
            abort(403, 'Chỉ cán bộ mới có thể báo nghỉ.');
        }

        return view('backend.can-bo-nghi.create');
    }

    /**
     * Lưu báo nghỉ
     */
    public function store(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        $request->validate([
            'ngay_nghi' => 'required|date|after_or_equal:today',
            'ly_do' => 'nullable|string|max:500',
        ], [
            'ngay_nghi.required' => 'Vui lòng chọn ngày nghỉ.',
            'ngay_nghi.after_or_equal' => 'Ngày nghỉ phải từ hôm nay trở đi.',
            'ly_do.max' => 'Lý do không được quá 500 ký tự.',
        ]);

        // Chỉ cán bộ mới được báo nghỉ
        if (!$currentUser->isCanBo()) {
            return back()->withErrors(['error' => 'Chỉ cán bộ mới có thể báo nghỉ.'])->withInput();
        }

        // Kiểm tra đã báo nghỉ chưa
        $daBaoNghi = CanBoNghi::where('can_bo_id', $currentUser->id)
            ->whereDate('ngay_nghi', $request->ngay_nghi)
            ->exists();

        if ($daBaoNghi) {
            return back()->withErrors(['ngay_nghi' => 'Bạn đã báo nghỉ ngày này rồi.'])->withInput();
        }

        // Tạo báo nghỉ ở trạng thái chờ duyệt
        $canBoNghi = CanBoNghi::create([
            'can_bo_id' => $currentUser->id,
            'ngay_nghi' => $request->ngay_nghi,
            'ly_do' => $request->ly_do,
            'da_chuyen_ho_so' => false,
            'trang_thai' => CanBoNghi::TRANG_THAI_CHO_DUYET, // Chờ duyệt
        ]);

        return redirect()->route('admin.can-bo-nghi.index')
            ->with('success', 'Đã gửi yêu cầu báo nghỉ. Vui lòng chờ admin phường duyệt.');
    }

    /**
     * Admin phường báo nghỉ cho cán bộ
     */
    public function storeByAdmin(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        $request->validate([
            'can_bo_id' => 'required|exists:quan_tri_vien,id',
            'ngay_nghi' => 'required|date|after_or_equal:today',
            'ly_do' => 'nullable|string|max:500',
        ], [
            'can_bo_id.required' => 'Vui lòng chọn cán bộ.',
            'can_bo_id.exists' => 'Cán bộ không tồn tại.',
            'ngay_nghi.required' => 'Vui lòng chọn ngày nghỉ.',
            'ngay_nghi.after_or_equal' => 'Ngày nghỉ phải từ hôm nay trở đi.',
        ]);

        // Chỉ admin phường và admin tổng mới được báo nghỉ cho cán bộ
        if (!$currentUser->isAdminPhuong() && !$currentUser->isAdmin()) {
            return back()->withErrors(['error' => 'Bạn không có quyền báo nghỉ cho cán bộ.'])->withInput();
        }

        $canBo = Admin::findOrFail($request->can_bo_id);

        // Admin phường chỉ được báo nghỉ cho cán bộ của phường mình
        if ($currentUser->isAdminPhuong() && $canBo->don_vi_id != $currentUser->don_vi_id) {
            return back()->withErrors(['error' => 'Bạn chỉ có thể báo nghỉ cho cán bộ của phường mình.'])->withInput();
        }

        // Kiểm tra đã báo nghỉ chưa
        $daBaoNghi = CanBoNghi::where('can_bo_id', $request->can_bo_id)
            ->whereDate('ngay_nghi', $request->ngay_nghi)
            ->exists();

        if ($daBaoNghi) {
            return back()->withErrors(['ngay_nghi' => 'Cán bộ này đã báo nghỉ ngày này rồi.'])->withInput();
        }

        // Admin phường báo nghỉ cho cán bộ - tự động duyệt luôn
        $canBoNghi = CanBoNghi::create([
            'can_bo_id' => $request->can_bo_id,
            'ngay_nghi' => $request->ngay_nghi,
            'ly_do' => $request->ly_do,
            'da_chuyen_ho_so' => false,
            'trang_thai' => CanBoNghi::TRANG_THAI_DA_DUYET, // Tự động duyệt
            'nguoi_duyet_id' => $currentUser->id,
            'ngay_duyet' => now(),
        ]);

        // Tự động chuyển hồ sơ vì đã được duyệt
        $ketQua = $this->chuyenHoSoService->chuyenHoSoKhiCanBoNghi(
            $request->can_bo_id,
            $request->ngay_nghi
        );

        if ($ketQua['success']) {
            $message = "Đã báo nghỉ cho cán bộ {$canBo->ho_ten} thành công!";
            if ($ketQua['so_ho_so_chuyen'] > 0) {
                $message .= " Đã tự động chuyển {$ketQua['so_ho_so_chuyen']} hồ sơ sang cán bộ khác.";
            }
            return redirect()->route('admin.can-bo-nghi.index')->with('success', $message);
        } else {
            return back()->withErrors(['error' => $ketQua['message']])->withInput();
        }
    }

    /**
     * Xóa báo nghỉ
     */
    public function destroy($id)
    {
        $currentUser = Auth::guard('admin')->user();
        
        $canBoNghi = CanBoNghi::findOrFail($id);

        // Kiểm tra quyền
        if ($currentUser->isCanBo() && $canBoNghi->can_bo_id != $currentUser->id) {
            abort(403, 'Bạn chỉ có thể xóa báo nghỉ của mình.');
        }

        if ($currentUser->isAdminPhuong()) {
            $canBo = $canBoNghi->canBo;
            if ($canBo->don_vi_id != $currentUser->don_vi_id) {
                abort(403, 'Bạn chỉ có thể xóa báo nghỉ của cán bộ phường mình.');
            }
        }

        $canBoNghi->delete();

        return back()->with('success', 'Đã xóa báo nghỉ thành công.');
    }
}
