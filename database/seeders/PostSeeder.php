<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Hướng dẫn đăng ký dịch vụ trực tuyến',
                'excerpt' => 'Bài viết hướng dẫn chi tiết cách đăng ký các dịch vụ hành chính trực tuyến một cách nhanh chóng và tiện lợi.',
                'content' => '<p>Trong thời đại công nghệ số hiện nay, việc đăng ký dịch vụ hành chính trực tuyến đã trở nên phổ biến và tiện lợi hơn bao giờ hết. Bài viết này sẽ hướng dẫn bạn các bước cơ bản để đăng ký dịch vụ một cách nhanh chóng và hiệu quả.</p>
                <h3>Bước 1: Truy cập hệ thống</h3>
                <p>Đầu tiên, bạn cần truy cập vào trang web chính thức của hệ thống. Tại đây, bạn sẽ tìm thấy các dịch vụ có sẵn và thông tin chi tiết về từng dịch vụ.</p>
                <h3>Bước 2: Chọn dịch vụ</h3>
                <p>Sau khi đã xem qua các dịch vụ, bạn hãy chọn dịch vụ mà mình cần đăng ký. Mỗi dịch vụ sẽ có các yêu cầu và tài liệu cần thiết khác nhau.</p>
                <h3>Bước 3: Điền thông tin</h3>
                <p>Bạn cần điền đầy đủ thông tin cá nhân và các thông tin liên quan đến dịch vụ. Hãy đảm bảo thông tin chính xác để tránh các vấn đề sau này.</p>
                <h3>Bước 4: Tải lên tài liệu</h3>
                <p>Bạn cần tải lên các tài liệu cần thiết theo yêu cầu của dịch vụ. Các tài liệu thường được yêu cầu ở định dạng PDF hoặc hình ảnh.</p>
                <h3>Bước 5: Xác nhận và gửi</h3>
                <p>Cuối cùng, hãy kiểm tra lại toàn bộ thông tin và tài liệu trước khi gửi đơn đăng ký. Sau khi gửi, bạn sẽ nhận được mã hồ sơ để theo dõi tiến độ xử lý.</p>',
                'author' => 'Ban Quản Trị',
                'status' => 'published',
                'is_featured' => true,
            ],
            [
                'title' => 'Quy trình xử lý hồ sơ hành chính',
                'excerpt' => 'Tìm hiểu về quy trình xử lý hồ sơ hành chính từ khi tiếp nhận đến khi hoàn tất, giúp bạn nắm rõ các bước và thời gian xử lý.',
                'content' => '<p>Quy trình xử lý hồ sơ hành chính được thực hiện qua nhiều bước để đảm bảo tính chính xác và minh bạch. Dưới đây là các giai đoạn chính:</p>
                <h3>Giai đoạn 1: Tiếp nhận hồ sơ</h3>
                <p>Sau khi bạn gửi đơn đăng ký, hệ thống sẽ tự động tiếp nhận và tạo mã hồ sơ. Bạn sẽ nhận được thông báo xác nhận qua email hoặc tin nhắn.</p>
                <h3>Giai đoạn 2: Kiểm tra và phân công</h3>
                <p>Hồ sơ sẽ được kiểm tra tính đầy đủ của tài liệu. Nếu thiếu sót, bạn sẽ được thông báo để bổ sung. Sau đó, hồ sơ sẽ được phân công cho cán bộ xử lý phù hợp.</p>
                <h3>Giai đoạn 3: Xử lý hồ sơ</h3>
                <p>Cán bộ được phân công sẽ tiến hành xử lý hồ sơ theo đúng quy định. Thời gian xử lý tùy thuộc vào loại dịch vụ và độ phức tạp của hồ sơ.</p>
                <h3>Giai đoạn 4: Hoàn tất</h3>
                <p>Sau khi xử lý xong, bạn sẽ nhận được thông báo về kết quả. Nếu hồ sơ được duyệt, bạn có thể đến nhận kết quả tại địa điểm đã đăng ký.</p>',
                'author' => 'Ban Quản Trị',
                'status' => 'published',
                'is_featured' => true,
            ],
            [
                'title' => 'Lợi ích của dịch vụ hành chính trực tuyến',
                'excerpt' => 'Khám phá những lợi ích to lớn mà dịch vụ hành chính trực tuyến mang lại cho người dân và doanh nghiệp.',
                'content' => '<p>Dịch vụ hành chính trực tuyến đã và đang mang lại nhiều lợi ích thiết thực cho người dân và doanh nghiệp:</p>
                <h3>Tiết kiệm thời gian</h3>
                <p>Bạn không cần phải đến trực tiếp cơ quan hành chính, tiết kiệm được thời gian đi lại và chờ đợi. Mọi thủ tục có thể được thực hiện ngay tại nhà hoặc văn phòng.</p>
                <h3>Tiện lợi 24/7</h3>
                <p>Hệ thống hoạt động 24/7, cho phép bạn đăng ký dịch vụ bất cứ lúc nào, kể cả ngoài giờ hành chính và ngày nghỉ.</p>
                <h3>Minh bạch và theo dõi</h3>
                <p>Bạn có thể theo dõi tiến độ xử lý hồ sơ một cách minh bạch thông qua mã hồ sơ. Mọi thông tin đều được cập nhật kịp thời.</p>
                <h3>Giảm chi phí</h3>
                <p>Không cần in ấn tài liệu nhiều, không tốn chi phí đi lại, giúp tiết kiệm đáng kể cho người dân và doanh nghiệp.</p>
                <h3>Bảo mật thông tin</h3>
                <p>Hệ thống được bảo mật cao, đảm bảo thông tin cá nhân của bạn được bảo vệ an toàn.</p>',
                'author' => 'Ban Quản Trị',
                'status' => 'published',
                'is_featured' => false,
            ],
            [
                'title' => 'Các dịch vụ phổ biến nhất',
                'excerpt' => 'Tổng hợp các dịch vụ hành chính được sử dụng nhiều nhất, giúp bạn dễ dàng lựa chọn dịch vụ phù hợp.',
                'content' => '<p>Dưới đây là danh sách các dịch vụ hành chính được sử dụng nhiều nhất hiện nay:</p>
                <h3>1. Đăng ký tạm vắng</h3>
                <p>Dịch vụ đăng ký tạm vắng cho phép bạn thông báo về việc tạm thời vắng mặt tại nơi cư trú. Đây là dịch vụ được sử dụng rất phổ biến.</p>
                <h3>2. Cấp giấy chứng nhận</h3>
                <p>Bao gồm các loại giấy chứng nhận khác nhau như chứng nhận cư trú, chứng nhận hộ khẩu, v.v.</p>
                <h3>3. Đăng ký khai sinh, khai tử</h3>
                <p>Các thủ tục liên quan đến đăng ký khai sinh và khai tử có thể được thực hiện trực tuyến một cách nhanh chóng.</p>
                <h3>4. Cấp đổi chứng minh nhân dân</h3>
                <p>Thủ tục cấp đổi chứng minh nhân dân cũng đã được số hóa, giúp người dân tiết kiệm thời gian.</p>
                <p>Để biết thêm chi tiết về các dịch vụ, vui lòng truy cập trang đăng ký dịch vụ của chúng tôi.</p>',
                'author' => 'Ban Quản Trị',
                'status' => 'published',
                'is_featured' => false,
            ],
            [
                'title' => 'Hướng dẫn sử dụng hệ thống tra cứu hồ sơ',
                'excerpt' => 'Hướng dẫn chi tiết cách sử dụng tính năng tra cứu hồ sơ để theo dõi tiến độ xử lý đơn đăng ký của bạn.',
                'content' => '<p>Hệ thống tra cứu hồ sơ cho phép bạn theo dõi tiến độ xử lý đơn đăng ký một cách dễ dàng và thuận tiện.</p>
                <h3>Cách tra cứu</h3>
                <p>Bạn chỉ cần nhập mã hồ sơ mà bạn đã nhận được khi đăng ký dịch vụ. Mã hồ sơ thường có định dạng như: HS-2024-001234.</p>
                <h3>Thông tin hiển thị</h3>
                <p>Sau khi nhập mã hồ sơ, hệ thống sẽ hiển thị các thông tin sau:</p>
                <ul>
                    <li>Trạng thái hiện tại của hồ sơ</li>
                    <li>Thông tin dịch vụ đã đăng ký</li>
                    <li>Ngày hẹn và giờ hẹn</li>
                    <li>Lịch sử cập nhật</li>
                    <li>Thông tin cán bộ xử lý</li>
                </ul>
                <h3>Lưu ý</h3>
                <p>Mã hồ sơ là thông tin quan trọng, vui lòng lưu giữ cẩn thận. Nếu quên mã hồ sơ, bạn có thể liên hệ với chúng tôi để được hỗ trợ.</p>',
                'author' => 'Ban Quản Trị',
                'status' => 'published',
                'is_featured' => false,
            ],
            [
                'title' => 'Câu hỏi thường gặp về dịch vụ hành chính',
                'excerpt' => 'Tổng hợp các câu hỏi thường gặp và câu trả lời chi tiết về dịch vụ hành chính trực tuyến.',
                'content' => '<p>Dưới đây là các câu hỏi thường gặp về dịch vụ hành chính trực tuyến:</p>
                <h3>1. Tôi có cần đăng nhập để sử dụng dịch vụ không?</h3>
                <p>Có, bạn cần đăng ký tài khoản và đăng nhập để sử dụng các dịch vụ. Điều này giúp bảo mật thông tin và theo dõi hồ sơ của bạn.</p>
                <h3>2. Thời gian xử lý hồ sơ là bao lâu?</h3>
                <p>Thời gian xử lý tùy thuộc vào loại dịch vụ. Thông thường từ 3-7 ngày làm việc. Bạn có thể theo dõi tiến độ qua mã hồ sơ.</p>
                <h3>3. Tôi có thể hủy đơn đăng ký không?</h3>
                <p>Có, bạn có thể hủy đơn đăng ký nếu hồ sơ chưa được xử lý. Vui lòng liên hệ với chúng tôi hoặc sử dụng tính năng hủy trong hệ thống.</p>
                <h3>4. Làm sao để biết hồ sơ của tôi đã được duyệt?</h3>
                <p>Bạn sẽ nhận được thông báo qua email hoặc tin nhắn khi hồ sơ được duyệt. Bạn cũng có thể tra cứu trạng thái qua mã hồ sơ.</p>
                <h3>5. Tôi có thể đăng ký nhiều dịch vụ cùng lúc không?</h3>
                <p>Có, bạn có thể đăng ký nhiều dịch vụ khác nhau. Mỗi dịch vụ sẽ có một mã hồ sơ riêng để bạn theo dõi.</p>',
                'author' => 'Ban Quản Trị',
                'status' => 'published',
                'is_featured' => false,
            ],
        ];

        // foreach ($posts as $postData) {
        //     $postData['slug'] = Str::slug($postData['title']);
        //     Post::firstOrCreate(
        //         ['slug' => $postData['slug']],
        //         $postData
        //     );
        // }
    }
}

