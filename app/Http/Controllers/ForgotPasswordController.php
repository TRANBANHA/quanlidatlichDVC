<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {

        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => 'required|email|exists:users,email',
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
    public function update(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Đặt lại mật khẩu cho người dùng
        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Mật khẩu của bạn đã được đặt lại thành công!');
        } else {
            // Kiểm tra lỗi cụ thể
            switch ($response) {
                case Password::INVALID_TOKEN:
                    $error_message = 'Token không hợp lệ. Vui lòng thử lại.';
                    break;
                case Password::INVALID_USER:
                    $error_message = 'Email không tồn tại. Vui lòng kiểm tra lại email của bạn.';
                    break;
                default:
                    $error_message = 'Không thể đặt lại mật khẩu, vui lòng thử lại sau.';
                    break;
            }

            return back()->withErrors(['email' => $error_message]);
        }

    }
}
