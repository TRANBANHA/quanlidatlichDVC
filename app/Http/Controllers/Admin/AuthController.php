<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;

class AuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function login()
    {
        // If already authenticated as admin, redirect to appropriate page based on role
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            if ($admin->isAdminPhuong() || $admin->isCanBo()) {
                return redirect()->route('reports.index');
            }
            return redirect(RouteServiceProvider::ADMIN);
        }
        
        return view('backend.Auth.login');
    }

    /**
     * Handle admin login request.
     */
    public function postLogin(Request $request)
    {
        $request->validate([
            'login' => 'required',  // Nhận email, số điện thoại hoặc tên đăng nhập
            'password' => 'required',
        ]);

        // Tìm kiếm admin theo email, số điện thoại hoặc tên đăng nhập
        $admin = Admin::where('email', $request->login)
            ->orWhere('so_dien_thoai', $request->login)
            ->orWhere('ten_dang_nhap', $request->login)
            ->first();
        if(Auth::guard('admin')->check()){
            return redirect(RouteServiceProvider::ADMIN)->with('error', "Bạn đã đăng nhập, vui lòng đăng xuất để đăng nhập với tài khoản khác");
        }
        // Kiểm tra mật khẩu
        if ($admin && Hash::check($request->password, $admin->mat_khau)) {
            // Đăng nhập với guard 'admin'
            Auth::guard('admin')->login($admin);
            
            // Regenerate session để tránh session fixation
            $request->session()->regenerate();
            
            // Redirect theo role: Admin phường và Cán bộ vào báo cáo, Admin tổng vào reports
            if ($admin->isAdminPhuong() || $admin->isCanBo()) {
                return redirect()->route('reports.index')->with('success', "Đăng nhập thành công");
            }
            
            return redirect()->intended(RouteServiceProvider::ADMIN)->with('success', "Đăng nhập thành công");
        }

        return redirect()->back()->withInput($request->only('login'))->with('error', "Đăng nhập thất bại, vui lòng kiểm tra thông tin đăng nhập và mật khẩu");
    }

    /**
     * Handle admin logout request.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Đăng xuất thành công');
    }

    /**
     * Send password reset link email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => 'required|email|exists:quan_tri_vien,email',
        ]);

        // TODO: Implement password reset functionality for admin
        // For now, just return success message
        return redirect()->back()->with('success', 'Chức năng đặt lại mật khẩu đang được phát triển. Vui lòng liên hệ quản trị viên.');
    }

    /**
     * Reset password.
     */
    public function resetPassword(Request $request)
    {
        // TODO: Implement password reset functionality
        return redirect()->back()->with('success', 'Chức năng đặt lại mật khẩu đang được phát triển.');
    }
}
