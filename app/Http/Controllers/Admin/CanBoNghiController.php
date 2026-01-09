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
            $query->whereHas('canBo', function ($q) use ($currentUser) {
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
            'ngay_nghi'   => 'required|array|min:1',
            'ngay_nghi.*' => 'required|date',
            'ly_do'       => 'nullable|string|max:500',
        ], [
            'ngay_nghi.required'   => 'Vui lòng chọn ít nhất một ngày nghỉ.',
            'ngay_nghi.array'      => 'Định dạng ngày không hợp lệ.',
            'ngay_nghi.*.date'     => 'Định dạng ngày không hợp lệ.',
            'ly_do.max'            => 'Lý do không được quá 500 ký tự.',
        ]);

        // Chỉ cán bộ mới được báo nghỉ
        if (!$currentUser->isCanBo()) {
            return back()->withErrors(['error' => 'Chỉ cán bộ mới có thể báo nghỉ.'])->withInput();
        }

        // Chuẩn hóa và loại trùng
        $dates = collect($request->ngay_nghi)
            ->map(fn($d) => Carbon::parse($d)->startOfDay())
            ->unique()
            ->values();

        // Ràng buộc: chỉ cho phép từ thứ Hai tuần tới
        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->startOfDay();
        $invalid = $dates->first(fn($d) => $d->lt($nextMonday));
        if ($invalid) {
            return back()->withErrors([
                'ngay_nghi' => 'Ngày nghỉ phải từ tuần kế tiếp (từ ' . $nextMonday->format('d/m/Y') . ').'
            ])->withInput();
        }

        // Kiểm tra các ngày đã đăng ký trước đó
        $dupes = CanBoNghi::where('can_bo_id', $currentUser->id)
            ->whereIn('ngay_nghi', $dates->map->toDateString())
            ->pluck('ngay_nghi')
            ->map(fn($d) => Carbon::parse($d)->format('d/m/Y'));

        if ($dupes->isNotEmpty()) {
            return back()->withErrors([
                'ngay_nghi' => 'Bạn đã đăng ký nghỉ ngày: ' . $dupes->implode(', ')
            ])->withInput();
        }

        // Tạo báo nghỉ ở trạng thái chờ duyệt cho từng ngày
        foreach ($dates as $d) {
            CanBoNghi::create([
                'can_bo_id'       => $currentUser->id,
                'ngay_nghi'       => $d->toDateString(),
                'ly_do'           => $request->ly_do,
                'da_chuyen_ho_so' => false,
                'trang_thai'      => CanBoNghi::TRANG_THAI_CHO_DUYET, // Chờ duyệt
            ]);
        }

        return redirect()->route('admin.can-bo-nghi.index')
            ->with('success', 'Đã gửi yêu cầu báo nghỉ. Vui lòng chờ admin phường duyệt.');
    }

    /**
     * Duyệt báo nghỉ
     */
    public function duyet(Request $request, $id)
    {
        $currentUser = Auth::guard('admin')->user();

        if (!$currentUser->isAdminPhuong() && !$currentUser->isAdmin()) {
            abort(403, 'Bạn không có quyền duyệt báo nghỉ.');
        }

        $canBoNghi = CanBoNghi::findOrFail($id);

        if (!$canBoNghi->isChoDuyet()) {
            return back()->withErrors(['error' => 'Báo nghỉ không ở trạng thái chờ duyệt.']);
        }

        $request->validate([
            'ghi_chu_duyet' => 'nullable|string|max:500',
        ]);

        $canBoNghi->trang_thai = CanBoNghi::TRANG_THAI_DA_DUYET;
        $canBoNghi->nguoi_duyet_id = $currentUser->id;
        $canBoNghi->ngay_duyet = now();
        $canBoNghi->ghi_chu_duyet = $request->ghi_chu_duyet;
        $canBoNghi->save();

        // Chuyển hồ sơ sau khi duyệt
        $ketQua = $this->chuyenHoSoService->chuyenHoSoKhiCanBoNghi(
            $canBoNghi->can_bo_id,
            $canBoNghi->ngay_nghi
        );

        if ($ketQua['success']) {
            $message = 'Đã duyệt báo nghỉ thành công.';
            if (isset($ketQua['so_ho_so_chuyen']) && $ketQua['so_ho_so_chuyen'] > 0) {
                $message .= " Đã tự động chuyển {$ketQua['so_ho_so_chuyen']} hồ sơ sang cán bộ khác.";
            }
            return redirect()->route('admin.can-bo-nghi.index')->with('success', $message);
        }

        return redirect()->route('admin.can-bo-nghi.index')->with('success', 'Đã duyệt báo nghỉ thành công.');
    }

    /**
     * Từ chối báo nghỉ
     */
    public function tuChoi(Request $request, $id)
    {
        $currentUser = Auth::guard('admin')->user();

        if (!$currentUser->isAdminPhuong() && !$currentUser->isAdmin()) {
            abort(403, 'Bạn không có quyền từ chối báo nghỉ.');
        }

        $canBoNghi = CanBoNghi::findOrFail($id);

        if (!$canBoNghi->isChoDuyet()) {
            return back()->withErrors(['error' => 'Báo nghỉ không ở trạng thái chờ duyệt.']);
        }

        $request->validate([
            'ly_do_tu_choi' => 'required|string|max:500',
        ]);

        $canBoNghi->trang_thai = CanBoNghi::TRANG_THAI_TU_CHOI;
        $canBoNghi->nguoi_duyet_id = $currentUser->id;
        $canBoNghi->ngay_duyet = now();
        // Lưu lý do từ chối vào ghi_chu_duyet
        $canBoNghi->ghi_chu_duyet = $request->ly_do_tu_choi;
        $canBoNghi->save();

        return redirect()->route('admin.can-bo-nghi.index')->with('success', 'Đã từ chối báo nghỉ.');
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
