# 📚 Câu hỏi & trả lời mẫu cho Rasa Chatbot

Tài liệu này tập hợp một số câu hỏi tiêu biểu cùng câu trả lời tương ứng đã được cấu hình trong `domain.yml`. Mỗi mục bao gồm cấu trúc: **Câu hỏi gợi ý** → **Phản hồi Rasa (utter)**.

| Câu hỏi gợi ý | Trả lời mặc định từ Rasa |
| --- | --- |
| **“Tôi cần hướng dẫn sử dụng Cổng Dịch vụ công ở đâu?”** | Bạn truy cập mục "Hướng dẫn sử dụng" trên Cổng Dịch vụ công hoặc xem video hướng dẫn trên kênh chính thức. |
| **“Cần ký số văn bản thế nào?”** | Có, bạn cần có chữ ký số và phần mềm hỗ trợ ký điện tử theo quy định. |
| **“Tra cứu mã số thuế bằng CCCD được không?”** | Bạn truy cập Cổng thông tin thuế, sử dụng tài khoản định danh điện tử hoặc CCCD để tra cứu. |
| **“Lịch sử nộp thuế xem ở đâu?”** | Bạn đăng nhập tài khoản trên Cổng thuế điện tử, vào mục tra cứu lịch sử nộp thuế. |
| **“Có thể nộp báo cáo thuế online không?”** | Có, bạn có thể nộp báo cáo thuế qua Cổng thuế điện tử với tài khoản đã đăng ký. |
| **“Tra cứu quy hoạch đất đai thế nào?”** | Bạn truy cập Cổng thông tin quy hoạch đất đai của tỉnh/thành phố, nhập thông tin địa chỉ cần tra cứu. |
| **“Đăng ký BHXH tự nguyện ra sao?”** | Bạn đăng ký qua Cổng BHXH điện tử hoặc đến trực tiếp cơ quan BHXH nơi cư trú. |
| **“Có thể xin cấp GPLX quốc tế không?”** | Có, bạn đăng ký qua Cổng Giao thông điện tử, cung cấp thông tin và nộp lệ phí online. |
| **“Tra cứu thông tin doanh nghiệp ở đâu?”** | Bạn truy cập Cổng thông tin doanh nghiệp quốc gia, nhập mã số thuế hoặc tên doanh nghiệp cần tra cứu. |
| **“Nhận email thông báo kết quả được không?”** | Bạn đăng ký nhận email thông báo trong phần cài đặt tài khoản trên Cổng Dịch vụ công. |
| **“Đăng ký thẻ BHYT miễn phí thế nào?”** | Bạn đến UBND phường với giấy chứng nhận hộ nghèo/cận nghèo, CMND/CCCD để được cấp thẻ BHYT miễn phí. |
| **“Chế độ thai sản gồm những gì?”** | Lao động nữ được nghỉ thai sản 6 tháng, hưởng 100% lương từ quỹ BHXH. |
| **“Muốn vay vốn ưu đãi thì sao?”** | Có, bạn liên hệ Ngân hàng Chính sách xã hội tại địa phương với giấy chứng nhận hộ nghèo và phương án vay vốn. |
| **“Xét hộ nghèo cần giấy tờ gì?”** | Bạn nộp đơn đề nghị, các giấy tờ chứng minh thu nhập tại UBND phường để được xét duyệt. |
| **“Đăng ký BHYT hộ gia đình ra sao?”** | Bạn đến cơ quan BHXH nơi cư trú với sổ hộ khẩu, CMND/CCCD của các thành viên để đăng ký. |
| **“Hỗ trợ thiên tai có không?”** | Có, bạn cần báo cáo thiệt hại với UBND phường và được hỗ trợ theo quy định của Nhà nước. |
| **“Tính trợ cấp thất nghiệp thế nào?”** | Mức hưởng = 60% mức lương bình quân đóng BHXH của 6 tháng liền kề trước khi thất nghiệp. |
| **“Đăng ký mã số thuế doanh nghiệp ra sao?”** | Bạn nộp hồ sơ đăng ký doanh nghiệp tại Phòng Đăng ký kinh doanh, mã số thuế sẽ được cấp cùng lúc. |

## ✍️ Gợi ý thêm câu hỏi mới

Những câu dưới đây chưa có intent riêng trong mô hình. Có thể thêm vào bộ dữ liệu NLU và cấu hình `responses` để Rasa hiểu tốt hơn:

1. **“Phường hôm nay có cán bộ nào trực hỗ trợ dịch vụ y tế không?”**  
   → Trả lời mong muốn: “Hôm nay bộ phận y tế trực từ 8h-17h...”
2. **“Trạng thái xử lý hồ sơ tiêm chủng của tôi là gì?”**  
   → Mong muốn: cung cấp link/status tra cứu.
3. **“Làm sao liên hệ trực tiếp cán bộ phường?”**  
   → Cần phản hồi hướng dẫn số điện thoại hoặc form liên hệ.
4. **“Tôi muốn đặt lịch khám sức khỏe tại nhà thì đăng ký thế nào?”**  
   → Chuẩn bị intent riêng về đặt lịch khám tại nhà.
5. **“Có hỗ trợ xét nghiệm nhanh lưu động không?”**  
   → Trả lời nêu điều kiện đăng ký và kênh đăng ký.

> Sau khi bổ sung các intent/câu trả lời trên vào `data/nlu.yml`, `domain.yml` và rule/story tương ứng, hãy chạy `rasa train` để mô hình nhận diện được các câu hỏi này.

