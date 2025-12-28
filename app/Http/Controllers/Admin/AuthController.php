<?php

namespace App\Http\Controllers\admin;

use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\NewPasswordMail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{


    public function login()
    {
        // dd(Auth::guard('admin')->name());
        if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->publish == 1) {
            return redirect()->route('admin.index');
        }
        return view("backend.Auth.login");
    }
    public function postLogin(Request $request)
    {
        $request->validate([
            'login' => 'required', // email, sdt hoặc tên đăng nhập
            'password' => 'required',
        ]);

        $loginInput = $request->login;
        $password = $request->password;

        // Xác định kiểu đăng nhập
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } elseif (preg_match('/^[0-9]+$/', $loginInput)) {
            $field = 'so_dien_thoai';
        } else {
            $field = 'ten_dang_nhap';
        }

        $credentials = [
            $field => $loginInput,
            'password' => $password,
        ];
        // dd($credentials);
        // Đăng nhập qua guard admin
        // dd(Auth::guard('admin')->attempt($credentials));
        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();
            // Cho phép đăng nhập với 3 loại quyền: Admin (1), Admin phường (2), Cán bộ (0)
            if (!in_array($admin->quyen, [Admin::ADMIN, Admin::ADMIN_PHUONG, Admin::CAN_BO])) {   
                return redirect()->route('admin.login')
                    ->with('error', "Tài khoản của bạn không đủ quyền truy cập, liên hệ admin để xác thực!!");
            }

            return redirect(RouteServiceProvider::ADMIN);
        }

        return redirect()->route('admin.login')
            ->with('error', "Đăng nhập thất bại, vui lòng kiểm tra thông tin đăng nhập");
    }



    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    // Phương thức gửi email xác nhận
    protected function sendVerificationEmail($user)
    {
        $verificationLink = route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)]);

        Mail::send('emails.verify', ['user' => $user, 'verificationLink' => $verificationLink], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Xác nhận tài khoản');
        });
    }
    public function verify(Request $request, $id)
    {
        $user = Admin::findOrFail($id);

        // Kiểm tra hash của email
        if (sha1($user->email) !== $request->hash) {
            return redirect()->route('login')->with('error', 'Xác thực không hợp lệ.');
        }

        // Cập nhật trường publish thành 1
        $user->publish = 1;
        $user->save();

        return redirect()->route('login')->with('success', 'Tài khoản đã được xác nhận thành công.');
    }
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
        ]);

        // Tìm admin dựa trên email
        $admin = Admin::where('email', $request->email)->first();

        if ($admin) {
            // Tạo mật khẩu ngẫu nhiên 7 ký tự
            $randomPassword = Str::random(7);

            // Cập nhật mật khẩu trong database
            $admin->password = bcrypt($randomPassword);
            $admin->save();

            try {
                // Gửi email với mật khẩu mới
                \Mail::to($admin->email)->send(new ResetPasswordMail($randomPassword));

                return redirect()->back()->with('success', 'Mật khẩu mới đã được gửi đến email của bạn!');
            } catch (\Exception $e) {
                // Log lỗi nếu không gửi được email
                \Log::error('Reset password email error: ' . $e->getMessage());

                return redirect()->back()->with('error', 'Mật khẩu đã được cập nhật, nhưng không thể gửi email. Vui lòng thử lại.');
            }
        }

        return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
    }

}