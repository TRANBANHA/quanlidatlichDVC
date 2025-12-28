<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AccountController extends Controller
{
    public function profile()
    {
        $admin = Auth::guard('admin')->user();
        return view('backend.account.profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:quan_tri_vien,email,' . $admin->id,
            'so_dien_thoai' => 'nullable|string|max:20',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'phone.max' => 'Số điện thoại quá dài',
            'avatar.image' => 'File phải là hình ảnh',
            'avatar.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg',
            'avatar.max' => 'Kích thước hình ảnh tối đa là 2MB'
        ]);

        $data = $request->only(['name', 'email', 'so_dien_thoai']);

        if ($request->hasFile('hinh_anh')) {
            // Tạo thư mục nếu chưa tồn tại
            $path = public_path('uploads/avatars');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $hinh_anh = $request->file('hinh_anh');
            $filename = time() . '_' . $hinh_anh->getClientOriginalName();
            
            // Di chuyển file vào thư mục uploads/avatars
            $hinh_anh->move($path, $filename);
            
            // Lưu đường dẫn relative vào database
            $data['hinh_anh'] = 'uploads/avatars/' . $filename;

            // Xóa ảnh cũ nếu có
            if ($admin->hinh_anh && file_exists(public_path($admin->hinh_anh))) {
                unlink(public_path($admin->hinh_anh));
            }
        }
        
        $admin->update([
            'ho_ten' => $data['name'],
            'email' => $data['email'],
            'so_dien_thoai' => $data['so_dien_thoai'] ?? $admin->so_dien_thoai,
            'hinh_anh' => $data['hinh_anh'] ?? $admin->hinh_anh,
        ]);

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
    }

    public function changePassword()
    {
        return view('backend.account.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp'
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        $admin->update([
            'mat_khau' => Hash::make($request->password)
        ]);

        return redirect()->back()->with('success', 'Đổi mật khẩu thành công!');
    }
}