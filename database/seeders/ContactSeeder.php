<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'ten' => 'Nguyễn Văn A',
                'email' => 'nguyenvana@example.com',
                'so_dien_thoai' => '0901234567',
                'chu_de' => 'Hỏi về thủ tục đăng ký',
                'tin_nhan' => 'Tôi muốn hỏi về thủ tục đăng ký dịch vụ tạm vắng. Cần những giấy tờ gì?',
                'trang_thai' => 'new',
            ],
            [
                'ten' => 'Trần Thị B',
                'email' => 'tranthib@example.com',
                'so_dien_thoai' => '0912345678',
                'chu_de' => 'Yêu cầu hỗ trợ',
                'tin_nhan' => 'Tôi gặp vấn đề khi đăng ký dịch vụ trực tuyến. Mong được hỗ trợ sớm.',
                'trang_thai' => 'read',
            ],
            [
                'ten' => 'Lê Văn C',
                'email' => 'levanc@example.com',
                'so_dien_thoai' => '0923456789',
                'chu_de' => 'Góp ý về hệ thống',
                'tin_nhan' => 'Hệ thống rất tiện lợi nhưng tôi có một số góp ý để cải thiện trải nghiệm người dùng.',
                'trang_thai' => 'replied',
                'phan_hoi' => 'Cảm ơn bạn đã góp ý. Chúng tôi sẽ xem xét và cải thiện hệ thống.',
                'ngay_phan_hoi' => now(),
            ],
        ];

        foreach ($contacts as $contact) {
            Contact::create($contact);
        }
    }
}

