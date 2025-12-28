<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public $hoTen;
    public $tenDangNhap;
    public $matKhau;
    public $email;
    public $donVi;

    /**
     * Create a new message instance.
     */
    public function __construct($hoTen, $tenDangNhap, $matKhau, $email, $donVi = null)
    {
        $this->hoTen = $hoTen;
        $this->tenDangNhap = $tenDangNhap;
        $this->matKhau = $matKhau;
        $this->email = $email;
        $this->donVi = $donVi;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Thông tin tài khoản cán bộ phường')
            ->view('emails.admin-account')
            ->with([
                'hoTen' => $this->hoTen,
                'tenDangNhap' => $this->tenDangNhap,
                'matKhau' => $this->matKhau,
                'email' => $this->email,
                'donVi' => $this->donVi,
            ]);
    }
}
