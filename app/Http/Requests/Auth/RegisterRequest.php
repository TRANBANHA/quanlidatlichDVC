<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'ten' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:nguoi_dung,email'],
            'so_dien_thoai' => ['required', 'digits:10'],
            'cccd' => ['required', 'digits:12', 'unique:nguoi_dung,cccd'],
            'mat_khau' => ['required', 'string', 'min:6'],
            'don_vi_id' => ['required', 'integer', 'exists:don_vi,id'],
            'dia_chi' => ['required', 'string', 'max:500'],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'ten.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được đăng ký.',
            'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại.',
            'so_dien_thoai.digits' => 'Số điện thoại phải có đúng 10 chữ số.',
            'cccd.required' => 'Vui lòng nhập số CCCD.',
            'cccd.digits' => 'Số CCCD phải có đúng 12 chữ số.',
            'cccd.unique' => 'CCCD này đã được đăng ký.',
            'mat_khau.required' => 'Vui lòng nhập mật khẩu.',
            'mat_khau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ];
    }
}
