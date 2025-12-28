<?php

namespace App\Http\Controllers\website;

use App\Models\User;
use App\Models\DonVi;
use Illuminate\Support\Str;
use App\Mail\VerifyCodeMail;
use Illuminate\Http\Request;
use App\Mail\ForgotPasswordMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{


    public function index(Request $request)
    {
        return view('website.auth.login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',  // Nhận email hoặc số điện thoại
            'password' => 'required',
        ]);

        // Tìm kiếm người dùng
        $user = User::where('email', $request->login)
            ->orWhere('so_dien_thoai', $request->login)
            ->where('tinh_trang', 1)
            ->first();

        // Kiểm tra mật khẩu
        if ($user && Hash::check($request->password, $user->mat_khau)) {
            // Đăng nhập với guard 'web'
            Auth::guard('web')->login($user);
            
            // Regenerate session để tránh session fixation
            $request->session()->regenerate();
            
            return redirect()->intended(route('index'))->with('success', "Đăng nhập thành công");
        }

        return redirect()->back()->withInput($request->only('login'))->with('error', "Đăng nhập thất bại, vui lòng kiểm tra email hoặc số điện thoại và mật khẩu");
    }



    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('index');
    }

    public function register(Request $request)
    {
        $donVis = DonVi::all();
        return view('website.auth.register', compact('donVis'));
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
        $user = User::findOrFail($id);

        // Kiểm tra hash của email
        if (sha1($user->email) !== $request->hash) {
            return redirect()->route('login')->with('error', 'Xác thực không hợp lệ.');
        }

        // Cập nhật trường publish thành 1
        $user->publish = 1;
        $user->save();

        return redirect()->route('login')->with('success', 'Tài khoản đã được xác nhận thành công.');
    }

    public function postRegister(RegisterRequest $request)
    {
        $data = $request->except('_token');

        // Tạo mã xác minh 7 số
        $verificationCode = rand(1000000, 9999999);

        // Lưu user vào bảng nguoi_dung
        $user = User::create([
            "ten" => $data['ten'],
            "email" => $data['email'],
            "so_dien_thoai" => $data['so_dien_thoai'],
            "cccd" => $data['cccd'],
            "mat_khau" => Hash::make($data['mat_khau']),
            "tinh_trang" => 0, // chưa xác thực
            "don_vi_id" => $data['don_vi_id'] ?? null,
            "dia_chi" => $data['dia_chi'] ?? null,
            "code" => $verificationCode,
        ]);

        // Gửi email chứa mã xác minh
        Mail::to($user->email)->send(new VerifyCodeMail($verificationCode));

        // Lưu email vào session để dùng cho bước xác minh
        session(['verify_email' => $user->email]);

        // Chuyển hướng đến trang nhập mã xác minh
        return redirect()->route('web.verifyCode')->with('success', 'Chúng tôi đã gửi mã xác nhận đến email của bạn.');
    }
    public function showVerifyForm()
    {
        return view('website.auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:7',
        ]);

        $email = session('verify_email');
        $user = User::where('email', $email)->where('code', $request->code)->first();

        if (!$user) {
            return back()->with('error', 'Mã xác minh không hợp lệ!');
        }

        // Cập nhật trạng thái xác thực
        $user->update([
            'tinh_trang' => 1,
        ]);

        session()->forget('verify_email');

        return redirect()->route('login')->with('success', 'Xác minh thành công! Bạn có thể đăng nhập.');
    }
    public function sendResetLinkEmail(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => 'required|email|exists:nguoi_dung,email',
        ]);

        // Gửi email yêu cầu đặt lại mật khẩu
        $response = Password::sendResetLink(
            $request->only('email')
        );

        // Kiểm tra nếu gửi thành công
        if ($response == Password::RESET_LINK_SENT) {
            return redirect()->back()->with('success', 'Đã gửi email yêu cầu đặt lại mật khẩu!');
        } else {
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }
    }
}