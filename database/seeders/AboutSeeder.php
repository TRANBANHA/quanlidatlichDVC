<?php

namespace Database\Seeders;

use App\Models\About;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        About::create([
            'tieu_de' => 'Giới thiệu về hệ thống quản lý dịch vụ hành chính',
            'noi_dung' => '<p>Hệ thống quản lý dịch vụ hành chính trực tuyến được xây dựng nhằm mục đích đơn giản hóa các thủ tục hành chính, giúp người dân và doanh nghiệp tiết kiệm thời gian và chi phí.</p>
            <p>Với hệ thống này, bạn có thể đăng ký các dịch vụ hành chính một cách nhanh chóng, theo dõi tiến độ xử lý hồ sơ, và nhận kết quả một cách thuận tiện.</p>
            <p>Chúng tôi cam kết mang đến dịch vụ chất lượng cao, minh bạch và hiệu quả, góp phần xây dựng chính quyền điện tử hiện đại.</p>',
            'su_menh' => '<p>Sứ mệnh của chúng tôi là cung cấp các dịch vụ hành chính trực tuyến chất lượng cao, tiện lợi và minh bạch, góp phần nâng cao chất lượng phục vụ người dân và doanh nghiệp.</p>',
            'tam_nhin' => '<p>Tầm nhìn của chúng tôi là trở thành hệ thống quản lý dịch vụ hành chính hàng đầu, được người dân tin tưởng và sử dụng rộng rãi.</p>',
            'gia_tri' => '<ul>
                <li><strong>Minh bạch:</strong> Mọi thông tin và quy trình đều được công khai, rõ ràng</li>
                <li><strong>Hiệu quả:</strong> Tối ưu hóa quy trình để mang lại kết quả nhanh chóng</li>
                <li><strong>Chất lượng:</strong> Đảm bảo chất lượng dịch vụ cao nhất</li>
                <li><strong>Tiện lợi:</strong> Mang đến trải nghiệm sử dụng tốt nhất cho người dùng</li>
            </ul>',
            'so_dien_thoai' => '1900-1234',
            'email' => 'contact@example.com',
            'dia_chi' => '123 Đường ABC, Phường XYZ, Quận 1, TP. Hồ Chí Minh',
        ]);
    }
}

