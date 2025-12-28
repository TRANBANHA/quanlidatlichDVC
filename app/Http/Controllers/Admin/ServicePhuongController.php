<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServicePhuong;
use App\Models\ServiceSchedule;
use App\Models\ServiceAssignment;
use App\Models\ServiceField;
use App\Models\Admin;
use App\Models\DonVi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicePhuongController extends Controller
{
    /**
     * Danh sách dịch vụ của phường (Admin phường)
     */
    public function index()
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Cho phép Admin phường và Cán bộ phường xem
        if (!$currentUser->isAdminPhuong() && !$currentUser->isCanBo()) {
            abort(403, 'Chỉ Admin phường và Cán bộ phường mới có quyền xem dịch vụ phường.');
        }

        // Lấy tất cả dịch vụ tổng
        $allServices = Service::with(['servicePhuongs' => function($query) use ($currentUser) {
            $query->where('don_vi_id', $currentUser->don_vi_id);
        }])->get();

        // Lấy dịch vụ đã được cấu hình cho phường
        $servicePhuongs = ServicePhuong::where('don_vi_id', $currentUser->don_vi_id)
            ->with('dichVu')
            ->get()
            ->keyBy('dich_vu_id');

        // Lấy lịch dịch vụ cho từng dịch vụ
        $schedulesByService = [];
        foreach ($servicePhuongs as $servicePhuong) {
            $schedulesByService[$servicePhuong->dich_vu_id] = ServiceSchedule::where('dich_vu_id', $servicePhuong->dich_vu_id)
                ->where('trang_thai', true)
                ->orderBy('thu_trong_tuan')
                ->get();
        }

        return view('backend.service-phuong.index', compact('allServices', 'servicePhuongs', 'schedulesByService'));
    }

    /**
     * Sao chép dịch vụ từ tổng về phường
     */
    public function copyFromTotal($serviceId)
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Cho phép Admin phường và Cán bộ phường sao chép dịch vụ
        if (!$currentUser->isAdminPhuong() && !$currentUser->isCanBo()) {
            abort(403, 'Chỉ Admin phường và Cán bộ phường mới có quyền sao chép dịch vụ.');
        }

        $service = Service::findOrFail($serviceId);

        // Kiểm tra xem đã có chưa
        $existing = ServicePhuong::where('dich_vu_id', $serviceId)
            ->where('don_vi_id', $currentUser->don_vi_id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Dịch vụ này đã được sao chép.');
        }

        // Tạo bản sao với giá trị mặc định
        ServicePhuong::create([
            'dich_vu_id' => $serviceId,
            'don_vi_id' => $currentUser->don_vi_id,
            'thoi_gian_xu_ly' => 7, // 7 ngày mặc định
            'so_luong_toi_da' => 10, // 10 hồ sơ/ngày mặc định
            'phi_dich_vu' => 0, // Miễn phí mặc định
            'kich_hoat' => true,
        ]);

        return redirect()->back()->with('success', 'Sao chép dịch vụ thành công!');
    }

    /**
     * Cập nhật cấu hình dịch vụ phường
     */
    public function update(Request $request, $id)
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Cho phép Admin phường và Cán bộ phường cập nhật
        if (!$currentUser->isAdminPhuong() && !$currentUser->isCanBo()) {
            abort(403, 'Chỉ Admin phường và Cán bộ phường mới có quyền cập nhật dịch vụ.');
        }

        $servicePhuong = ServicePhuong::findOrFail($id);

        // Kiểm tra quyền
        if ($servicePhuong->don_vi_id != $currentUser->don_vi_id) {
            abort(403, 'Bạn chỉ có thể sửa dịch vụ của phường mình.');
        }

        $request->validate([
            'thoi_gian_xu_ly' => 'required|integer|min:1|max:365',
            'so_luong_toi_da' => 'required|integer|min:1|max:1000',
            'phi_dich_vu' => 'required|numeric|min:0',
            'kich_hoat' => 'boolean',
            'ghi_chu' => 'nullable|string|max:500',
        ], [
            'thoi_gian_xu_ly.required' => 'Vui lòng nhập thời gian xử lý.',
            'thoi_gian_xu_ly.min' => 'Thời gian xử lý tối thiểu 1 ngày.',
            'so_luong_toi_da.required' => 'Vui lòng nhập số lượng tối đa.',
            'so_luong_toi_da.min' => 'Số lượng tối đa tối thiểu 1.',
            'phi_dich_vu.required' => 'Vui lòng nhập phí dịch vụ.',
            'phi_dich_vu.min' => 'Phí dịch vụ không được âm.',
        ]);

        $servicePhuong->update([
            'thoi_gian_xu_ly' => $request->thoi_gian_xu_ly,
            'so_luong_toi_da' => $request->so_luong_toi_da,
            'phi_dich_vu' => $request->phi_dich_vu,
            'kich_hoat' => $request->has('kich_hoat'),
            'ghi_chu' => $request->ghi_chu,
        ]);

        return redirect()->back()->with('success', 'Cập nhật cấu hình dịch vụ thành công!');
    }

    /**
     * Xóa dịch vụ khỏi phường (không xóa dịch vụ tổng)
     */
    public function destroy($id)
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Cho phép Admin phường và Cán bộ phường xóa
        if (!$currentUser->isAdminPhuong() && !$currentUser->isCanBo()) {
            abort(403, 'Chỉ Admin phường và Cán bộ phường mới có quyền xóa dịch vụ.');
        }

        $servicePhuong = ServicePhuong::findOrFail($id);

        if ($servicePhuong->don_vi_id != $currentUser->don_vi_id) {
            abort(403);
        }

        $servicePhuong->delete();

        return redirect()->back()->with('success', 'Xóa dịch vụ khỏi phường thành công!');
    }

    /**
     * Quản lý lịch dịch vụ (Admin phường)
     */
    public function schedule()
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Cho phép Admin phường và Cán bộ phường xem
        if (!$currentUser->isAdminPhuong() && !$currentUser->isCanBo()) {
            abort(403, 'Chỉ Admin phường và Cán bộ phường mới có quyền xem lịch dịch vụ.');
        }

        // Lấy dịch vụ đã kích hoạt của phường
        $servicePhuongs = ServicePhuong::where('don_vi_id', $currentUser->don_vi_id)
            ->where('kich_hoat', true)
            ->with('dichVu')
            ->get();

        // Lấy lịch dịch vụ cho từng dịch vụ
        $schedulesByService = [];
        foreach ($servicePhuongs as $servicePhuong) {
            $schedulesByService[$servicePhuong->dich_vu_id] = ServiceSchedule::where('dich_vu_id', $servicePhuong->dich_vu_id)
                ->where('trang_thai', true)
                ->orderBy('thu_trong_tuan')
                ->get();
        }

        return view('backend.service-phuong.schedule', compact('servicePhuongs', 'schedulesByService'));
    }

    /**
     * Lưu lịch dịch vụ
     */
    public function storeSchedule(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Cho phép Admin phường và Cán bộ phường lưu/chỉnh sửa lịch
        if (!$currentUser->isAdminPhuong() && !$currentUser->isCanBo()) {
            abort(403, 'Chỉ Admin phường và Cán bộ phường mới có quyền quản lý lịch dịch vụ.');
        }

        $request->validate([
            'dich_vu_id' => 'required|exists:dich_vu,id',
            'thu_trong_tuan' => 'required|integer|between:1,7',
            'gio_bat_dau' => 'required',
            'gio_ket_thuc' => 'required',
            'so_luong_toi_da' => 'required|integer|min:1',
        ]);

        // Kiểm tra dịch vụ thuộc phường
        $servicePhuong = ServicePhuong::where('dich_vu_id', $request->dich_vu_id)
            ->where('don_vi_id', $currentUser->don_vi_id)
            ->firstOrFail();

        // Kiểm tra số lượng lịch hiện có cho dịch vụ này (tối đa 2 thứ)
        $existingSchedulesCount = ServiceSchedule::where('dich_vu_id', $request->dich_vu_id)
            ->where('trang_thai', true)
            ->count();
        
        // Nếu đang tạo mới (không phải cập nhật), kiểm tra số lượng
        $isUpdating = ServiceSchedule::where('dich_vu_id', $request->dich_vu_id)
            ->where('thu_trong_tuan', $request->thu_trong_tuan)
            ->exists();
        
        if (!$isUpdating && $existingSchedulesCount >= 2) {
            return redirect()->back()->with('error', 'Mỗi dịch vụ chỉ có thể có tối đa 2 thứ trong tuần. Vui lòng xóa một thứ hiện có trước khi thêm mới.');
        }

        // Tạo hoặc cập nhật lịch
        $schedule = ServiceSchedule::updateOrCreate(
            [
                'dich_vu_id' => $request->dich_vu_id,
                'thu_trong_tuan' => $request->thu_trong_tuan,
            ],
            [
                'gio_bat_dau' => $request->gio_bat_dau,
                'gio_ket_thuc' => $request->gio_ket_thuc,
                'so_luong_toi_da' => $request->so_luong_toi_da,
                'trang_thai' => true,
                'ghi_chu' => $request->ghi_chu,
            ]
        );

        return redirect()->back()->with('success', 'Đã lưu lịch dịch vụ thành công!');
    }

    /**
     * Hiển thị form tạo dịch vụ mới (Admin phường/Cán bộ)
     */
    public function create()
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Cho phép Admin phường và Cán bộ phường tạo dịch vụ
        if (!$currentUser->isAdminPhuong() && !$currentUser->isCanBo()) {
            abort(403, 'Chỉ Admin phường và Cán bộ phường mới có quyền tạo dịch vụ.');
        }

        return view('backend.service-phuong.create');
    }

    /**
     * Lưu dịch vụ mới (Admin phường/Cán bộ)
     */
    public function store(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Cho phép Admin phường và Cán bộ phường tạo dịch vụ
        if (!$currentUser->isAdminPhuong() && !$currentUser->isCanBo()) {
            abort(403, 'Chỉ Admin phường và Cán bộ phường mới có quyền tạo dịch vụ.');
        }

        $request->validate([
            'ten_dich_vu' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
        ]);

        // Tạo dịch vụ mới
        $service = Service::create([
            'ten_dich_vu' => $request->ten_dich_vu,
            'mo_ta' => $request->mo_ta,
        ]);

        // Tự động tạo ServicePhuong cho phường này
        ServicePhuong::create([
            'dich_vu_id' => $service->id,
            'don_vi_id' => $currentUser->don_vi_id,
            'thoi_gian_xu_ly' => 7, // 7 ngày mặc định
            'so_luong_toi_da' => 10, // 10 hồ sơ/ngày mặc định
            'phi_dich_vu' => 0, // Miễn phí mặc định
            'kich_hoat' => true,
        ]);

        return redirect()->route('service-phuong.edit', $service->id)
            ->with('success', 'Tạo dịch vụ thành công! Vui lòng cấu hình form đăng ký.');
    }

    /**
     * Hiển thị form chỉnh sửa dịch vụ và cấu hình form fields (Admin phường/Cán bộ)
     */
    public function edit($serviceId)
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Cho phép Admin phường và Cán bộ phường sửa dịch vụ
        if (!$currentUser->isAdminPhuong() && !$currentUser->isCanBo()) {
            abort(403, 'Chỉ Admin phường và Cán bộ phường mới có quyền sửa dịch vụ.');
        }

        $service = Service::with('serviceFields')->findOrFail($serviceId);

        // Kiểm tra dịch vụ thuộc phường này
        $servicePhuong = ServicePhuong::where('dich_vu_id', $serviceId)
            ->where('don_vi_id', $currentUser->don_vi_id)
            ->first();

        if (!$servicePhuong) {
            abort(403, 'Bạn không có quyền chỉnh sửa dịch vụ này.');
        }

        return view('backend.service-phuong.edit', compact('service', 'servicePhuong'));
    }

    /**
     * Cập nhật thông tin dịch vụ (Admin phường/Cán bộ)
     */
    public function updateService(Request $request, $serviceId)
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Cho phép Admin phường và Cán bộ phường cập nhật
        if (!$currentUser->isAdminPhuong() && !$currentUser->isCanBo()) {
            abort(403, 'Chỉ Admin phường và Cán bộ phường mới có quyền cập nhật dịch vụ.');
        }

        // Kiểm tra dịch vụ thuộc phường này
        $servicePhuong = ServicePhuong::where('dich_vu_id', $serviceId)
            ->where('don_vi_id', $currentUser->don_vi_id)
            ->firstOrFail();

        $request->validate([
            'ten_dich_vu' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
        ]);

        $service = Service::findOrFail($serviceId);
        $service->update([
            'ten_dich_vu' => $request->ten_dich_vu,
            'mo_ta' => $request->mo_ta,
        ]);

        return redirect()->route('service-phuong.edit', $serviceId)
            ->with('success', 'Cập nhật dịch vụ thành công!');
    }

    /**
     * Hiển thị form tạo field mới (Admin phường/Cán bộ)
     */
    public function createField($serviceId)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isAdminPhuong() && !$currentUser->isCanBo()) {
            abort(403);
        }

        // Kiểm tra dịch vụ thuộc phường này
        $servicePhuong = ServicePhuong::where('dich_vu_id', $serviceId)
            ->where('don_vi_id', $currentUser->don_vi_id)
            ->firstOrFail();

        $service = Service::findOrFail($serviceId);
        return view('backend.service-phuong.fields.create', compact('service'));
    }

    /**
     * Lưu field mới (Admin phường/Cán bộ)
     */
    public function storeField(Request $request, $serviceId)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isAdminPhuong() && !$currentUser->isCanBo()) {
            abort(403);
        }

        // Kiểm tra dịch vụ thuộc phường này
        $servicePhuong = ServicePhuong::where('dich_vu_id', $serviceId)
            ->where('don_vi_id', $currentUser->don_vi_id)
            ->firstOrFail();

        // Merge giá trị mặc định cho checkbox nếu không có
        $request->merge([
            'bat_buoc' => $request->has('bat_buoc') ? true : false
        ]);

        $request->validate([
            'ten_truong' => 'required|string|max:255|regex:/^[a-z0-9_]+$/',
            'nhan_hien_thi' => 'required|string|max:255',
            'loai_truong' => 'required|in:text,textarea,file,date,select,number,email',
            'bat_buoc' => 'required|boolean',
            'placeholder' => 'nullable|string|max:255',
            'goi_y' => 'nullable|string|max:500',
            'thu_tu' => 'nullable|integer|min:0',
            'tuy_chon' => 'nullable|string',
        ]);

        $service = Service::findOrFail($serviceId);

        // Kiểm tra tên trường đã tồn tại chưa
        $existingField = ServiceField::where('dich_vu_id', $serviceId)
            ->where('ten_truong', $request->ten_truong)
            ->first();

        if ($existingField) {
            return back()->withErrors(['ten_truong' => 'Tên trường này đã tồn tại cho dịch vụ này.'])->withInput();
        }

        // Xử lý tùy chọn cho select
        $tuyChon = null;
        if ($request->loai_truong === 'select' && $request->tuy_chon) {
            $options = array_filter(array_map('trim', explode("\n", $request->tuy_chon)));
            $tuyChon = json_encode($options);
        }

        // Lấy thứ tự mặc định
        $thuTu = $request->thu_tu ?? ServiceField::where('dich_vu_id', $serviceId)->max('thu_tu') + 1;

        ServiceField::create([
            'dich_vu_id' => $serviceId,
            'ten_truong' => $request->ten_truong,
            'nhan_hien_thi' => $request->nhan_hien_thi,
            'loai_truong' => $request->loai_truong,
            'bat_buoc' => $request->bat_buoc ?? false,
            'placeholder' => $request->placeholder,
            'goi_y' => $request->goi_y,
            'thu_tu' => $thuTu,
            'tuy_chon' => $tuyChon,
        ]);

        return redirect()->route('service-phuong.edit', $serviceId)
            ->with('success', 'Thêm trường form thành công!');
    }

    /**
     * Hiển thị form sửa field (Admin phường/Cán bộ)
     */
    public function editField($serviceId, $fieldId)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isAdminPhuong() && !$currentUser->isCanBo()) {
            abort(403);
        }

        // Kiểm tra dịch vụ thuộc phường này
        $servicePhuong = ServicePhuong::where('dich_vu_id', $serviceId)
            ->where('don_vi_id', $currentUser->don_vi_id)
            ->firstOrFail();

        $service = Service::findOrFail($serviceId);
        $field = ServiceField::where('dich_vu_id', $serviceId)
            ->findOrFail($fieldId);

        return view('backend.service-phuong.fields.edit', compact('service', 'field'));
    }

    /**
     * Cập nhật field (Admin phường/Cán bộ)
     */
    public function updateField(Request $request, $serviceId, $fieldId)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isAdminPhuong() && !$currentUser->isCanBo()) {
            abort(403);
        }

        // Kiểm tra dịch vụ thuộc phường này
        $servicePhuong = ServicePhuong::where('dich_vu_id', $serviceId)
            ->where('don_vi_id', $currentUser->don_vi_id)
            ->firstOrFail();

        // Merge giá trị mặc định cho checkbox nếu không có
        $request->merge([
            'bat_buoc' => $request->has('bat_buoc') ? true : false
        ]);

        $request->validate([
            'nhan_hien_thi' => 'required|string|max:255',
            'loai_truong' => 'required|in:text,textarea,file,date,select,number,email',
            'bat_buoc' => 'required|boolean',
            'placeholder' => 'nullable|string|max:255',
            'goi_y' => 'nullable|string|max:500',
            'thu_tu' => 'nullable|integer|min:0',
            'tuy_chon' => 'nullable|string',
        ]);

        $field = ServiceField::where('dich_vu_id', $serviceId)
            ->findOrFail($fieldId);

        // Xử lý tùy chọn cho select
        $tuyChon = null;
        if ($request->loai_truong === 'select' && $request->tuy_chon) {
            $options = array_filter(array_map('trim', explode("\n", $request->tuy_chon)));
            $tuyChon = json_encode($options);
        }

        $field->update([
            'nhan_hien_thi' => $request->nhan_hien_thi,
            'loai_truong' => $request->loai_truong,
            'bat_buoc' => $request->bat_buoc ?? false,
            'placeholder' => $request->placeholder,
            'goi_y' => $request->goi_y,
            'thu_tu' => $request->thu_tu ?? $field->thu_tu,
            'tuy_chon' => $tuyChon,
        ]);

        return redirect()->route('service-phuong.edit', $serviceId)
            ->with('success', 'Cập nhật trường form thành công!');
    }

    /**
     * Xóa field (Admin phường/Cán bộ)
     */
    public function destroyField($serviceId, $fieldId)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isAdminPhuong() && !$currentUser->isCanBo()) {
            abort(403);
        }

        // Kiểm tra dịch vụ thuộc phường này
        $servicePhuong = ServicePhuong::where('dich_vu_id', $serviceId)
            ->where('don_vi_id', $currentUser->don_vi_id)
            ->firstOrFail();

        $field = ServiceField::where('dich_vu_id', $serviceId)
            ->findOrFail($fieldId);

        $field->delete();

        return redirect()->route('service-phuong.edit', $serviceId)
            ->with('success', 'Xóa trường form thành công!');
    }
}

