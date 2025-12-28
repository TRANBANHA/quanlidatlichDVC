<?php

namespace App\Http\Controllers\Admin;

use App\Models\DonVi;
use App\Models\ServicePhuong;
use App\Models\ServiceSchedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DonViController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentUser = Auth::guard('admin')->user();
        
        if ($currentUser->isAdmin()) {
            // Admin tổng: Xem tất cả đơn vị
            $donVis = DonVi::withCount('admins')->orderBy('id', 'desc')->paginate(15);
        } elseif ($currentUser->isAdminPhuong()) {
            // Admin phường: Chỉ xem đơn vị của mình
            $donVis = DonVi::where('id', $currentUser->don_vi_id)
                ->withCount('admins')
                ->orderBy('id', 'desc')
                ->paginate(15);
        } else {
            // Cán bộ: Không có quyền xem danh sách đơn vị
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }
        
        return view('backend.don_vi.index', compact('donVis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.don_vi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ten_don_vi' => 'required|string|max:255|unique:don_vi,ten_don_vi',
            'mo_ta' => 'nullable|string|max:500',
        ], [
            'ten_don_vi.required' => 'Vui lòng nhập tên đơn vị/phường.',
            'ten_don_vi.unique' => 'Tên đơn vị/phường đã tồn tại.',
            'mo_ta.max' => 'Mô tả không được vượt quá 500 ký tự.',
        ]);

        DonVi::create([
            'ten_don_vi' => $request->ten_don_vi,
            'mo_ta' => $request->mo_ta,
        ]);

        return redirect()->route('don-vi.index')->with('success', 'Tạo đơn vị/phường thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $donVi = DonVi::with('admins')->findOrFail($id);
        
        // Lấy tất cả dịch vụ của phường này
        $servicePhuongs = ServicePhuong::where('don_vi_id', $id)
            ->with('dichVu')
            ->where('kich_hoat', true)
            ->get();
        
        // Lấy lịch làm việc cho từng dịch vụ
        foreach ($servicePhuongs as $servicePhuong) {
            $servicePhuong->schedules = ServiceSchedule::where('dich_vu_id', $servicePhuong->dich_vu_id)
                ->where('trang_thai', true)
                ->orderBy('thu_trong_tuan')
                ->get();
        }
        
        return view('backend.don_vi.show', compact('donVi', 'servicePhuongs'));
    }
    
    /**
     * Helper function để chuyển đổi thu_trong_tuan sang tên thứ
     */
    private function getDayName($thuTrongTuan)
    {
        $days = [
            1 => 'Thứ 2',
            2 => 'Thứ 3',
            3 => 'Thứ 4',
            4 => 'Thứ 5',
            5 => 'Thứ 6',
            6 => 'Thứ 7',
            7 => 'Chủ nhật',
        ];
        return $days[$thuTrongTuan] ?? '';
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $donVi = DonVi::findOrFail($id);
        return view('backend.don_vi.edit', compact('donVi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $donVi = DonVi::findOrFail($id);

        $currentUser = Auth::guard('admin')->user();
        
        // Kiểm tra quyền: Admin phường chỉ được sửa phường của mình
        if ($currentUser->isAdminPhuong() && $donVi->id != $currentUser->don_vi_id) {
            abort(403, 'Bạn không có quyền sửa đơn vị/phường này.');
        }

        $request->validate([
            'ten_don_vi' => 'required|string|max:255|unique:don_vi,ten_don_vi,' . $id,
            'mo_ta' => 'nullable|string|max:500',
            // QR code fields (chỉ Admin phường mới có thể cập nhật)
            'qr_bank_name' => 'nullable|string|max:255',
            'qr_account_number' => 'nullable|string|max:50',
            'qr_account_name' => 'nullable|string|max:255',
            'qr_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'ten_don_vi.required' => 'Vui lòng nhập tên đơn vị/phường.',
            'ten_don_vi.unique' => 'Tên đơn vị/phường đã tồn tại.',
            'mo_ta.max' => 'Mô tả không được vượt quá 500 ký tự.',
        ]);

        $updateData = [
            'ten_don_vi' => $request->ten_don_vi,
            'mo_ta' => $request->mo_ta,
        ];

        // Chỉ Admin phường mới có thể cập nhật QR code
        if ($currentUser->isAdminPhuong() || $currentUser->isAdmin()) {
            $updateData['qr_bank_name'] = $request->qr_bank_name;
            $updateData['qr_account_number'] = $request->qr_account_number;
            $updateData['qr_account_name'] = $request->qr_account_name;

            // Xử lý upload QR code image
            if ($request->hasFile('qr_image')) {
                $qrImage = $request->file('qr_image');
                $qrImageName = 'qr_code_' . $donVi->id . '_' . time() . '.' . $qrImage->getClientOriginalExtension();
                $qrImagePath = $qrImage->storeAs('qr_codes', $qrImageName, 'public');
                $updateData['qr_image'] = $qrImagePath;
            }
        }

        $donVi->update($updateData);

        return redirect()->route('don-vi.index')->with('success', 'Cập nhật đơn vị/phường thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $donVi = DonVi::findOrFail($id);

        // Kiểm tra xem có cán bộ nào thuộc đơn vị này không
        if ($donVi->admins()->count() > 0) {
            return redirect()->back()->with('error', 'Không thể xóa đơn vị/phường này vì đang có cán bộ thuộc đơn vị.');
        }

        $donVi->delete();

        return redirect()->route('don-vi.index')->with('success', 'Xóa đơn vị/phường thành công!');
    }
}
