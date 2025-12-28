<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonVi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller
{
    /**
     * Hiển thị trang cấu hình QR code (Admin phường)
     */
    public function index()
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Chỉ Admin phường mới có quyền
        if (!$currentUser->isAdminPhuong()) {
            abort(403, 'Chỉ Admin phường mới có quyền cấu hình QR code.');
        }

        $donVi = DonVi::findOrFail($currentUser->don_vi_id);

        return view('backend.qr-code.index', compact('donVi'));
    }

    /**
     * Cập nhật cấu hình QR code
     */
    public function update(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Chỉ Admin phường mới có quyền
        if (!$currentUser->isAdminPhuong()) {
            abort(403, 'Chỉ Admin phường mới có quyền cấu hình QR code.');
        }

        $donVi = DonVi::findOrFail($currentUser->don_vi_id);

        $request->validate([
            'qr_bank_name' => 'nullable|string|max:255',
            'qr_account_number' => 'nullable|string|max:50',
            'qr_account_name' => 'nullable|string|max:255',
            'qr_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'qr_bank_name.max' => 'Tên ngân hàng không được vượt quá 255 ký tự.',
            'qr_account_number.max' => 'Số tài khoản không được vượt quá 50 ký tự.',
            'qr_account_name.max' => 'Tên chủ tài khoản không được vượt quá 255 ký tự.',
            'qr_image.image' => 'File phải là hình ảnh.',
            'qr_image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'qr_image.max' => 'Dung lượng ảnh không được vượt quá 2MB.',
        ]);

        $updateData = [
            'qr_bank_name' => $request->qr_bank_name,
            'qr_account_number' => $request->qr_account_number,
            'qr_account_name' => $request->qr_account_name,
        ];

        // Xử lý upload QR code image
        if ($request->hasFile('qr_image')) {
            // Xóa ảnh cũ nếu có
            if ($donVi->qr_image && Storage::disk('public')->exists($donVi->qr_image)) {
                Storage::disk('public')->delete($donVi->qr_image);
            }

            $qrImage = $request->file('qr_image');
            $qrImageName = 'qr_code_' . $donVi->id . '_' . time() . '.' . $qrImage->getClientOriginalExtension();
            $qrImagePath = $qrImage->storeAs('qr_codes', $qrImageName, 'public');
            $updateData['qr_image'] = $qrImagePath;
        }

        $donVi->update($updateData);

        return redirect()->route('admin.qr-code.index')
            ->with('success', 'Cập nhật cấu hình QR code thành công!');
    }
}

