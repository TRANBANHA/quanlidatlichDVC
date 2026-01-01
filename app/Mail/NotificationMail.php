<?php

namespace App\Mail;

use App\Models\ThongBao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $thongBao;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(ThongBao $thongBao)
    {
        // Load relationships để đảm bảo có đủ dữ liệu
        $thongBao->load(['NguoiDung', 'hoSo', 'dichVu']);
        $this->thongBao = $thongBao;
        $this->user = $thongBao->NguoiDung;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Thông báo mới từ hệ thống đặt lịch')
            ->view('emails.notification')
            ->with([
                'thongBao' => $this->thongBao,
                'user' => $this->user,
            ]);
    }
}
