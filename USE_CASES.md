# TÀI LIỆU USE CASE - HỆ THỐNG ĐẶT LỊCH DỊCH VỤ HÀNH CHÍNH CÔNG

## 📋 MỤC LỤC
1. [Người dân (Citizen)](#1-người-dân-citizen)
2. [Cán bộ (Staff/Officer)](#2-cán-bộ-staffofficer)
3. [Admin phường (Ward Admin)](#3-admin-phường-ward-admin)
4. [Admin tổng (Super Admin)](#4-admin-tổng-super-admin)

---

## 1. NGƯỜI DÂN (CITIZEN)

### 1.1. Quản lý tài khoản

#### UC-1.1.1: Đăng ký tài khoản
- **Mô tả**: Người dân đăng ký tài khoản mới trên hệ thống
- **Tiền điều kiện**: Chưa có tài khoản
- **Luồng chính**:
  1. Truy cập trang đăng ký (`/registers`)
  2. Nhập thông tin: Email, mật khẩu, họ tên, CCCD, địa chỉ, phường
  3. Xác thực email qua mã OTP
  4. Hoàn tất đăng ký
- **Luồng phụ**: Email đã tồn tại → Thông báo lỗi
- **Kết quả**: Tài khoản được tạo thành công

#### UC-1.1.2: Đăng nhập
- **Mô tả**: Người dân đăng nhập vào hệ thống
- **Tiền điều kiện**: Đã có tài khoản
- **Luồng chính**:
  1. Truy cập trang đăng nhập (`/login`)
  2. Nhập email và mật khẩu
  3. Xác thực thành công
  4. Chuyển đến trang chủ
- **Kết quả**: Đăng nhập thành công

#### UC-1.1.3: Quên mật khẩu
- **Mô tả**: Người dân yêu cầu reset mật khẩu
- **Luồng chính**:
  1. Click "Quên mật khẩu"
  2. Nhập email
  3. Nhận link reset qua email
  4. Đặt mật khẩu mới
- **Kết quả**: Mật khẩu được đặt lại

#### UC-1.1.4: Xem và cập nhật thông tin cá nhân
- **Mô tả**: Người dân xem và chỉnh sửa thông tin cá nhân
- **Luồng chính**:
  1. Vào "Thông tin cá nhân" (`/info`)
  2. Xem thông tin hiện tại
  3. Chỉnh sửa thông tin (nếu cần)
  4. Lưu thay đổi
- **Kết quả**: Thông tin được cập nhật

#### UC-1.1.5: Đổi mật khẩu
- **Mô tả**: Người dân thay đổi mật khẩu tài khoản
- **Luồng chính**:
  1. Vào "Thông tin cá nhân" → "Đổi mật khẩu"
  2. Nhập mật khẩu cũ
  3. Nhập mật khẩu mới (2 lần)
  4. Xác nhận đổi mật khẩu
- **Kết quả**: Mật khẩu được thay đổi

### 1.2. Đặt lịch dịch vụ

#### UC-1.2.1: Chọn phường
- **Mô tả**: Người dân chọn phường để đặt lịch dịch vụ
- **Luồng chính**:
  1. Vào "Đặt lịch dịch vụ" (`/dat-lich/chon-phuong`)
  2. Xem danh sách các phường
  3. Chọn phường muốn đặt lịch
  4. Chuyển sang bước chọn dịch vụ
- **Kết quả**: Phường được chọn

#### UC-1.2.2: Chọn dịch vụ
- **Mô tả**: Người dân chọn loại dịch vụ cần đặt lịch
- **Tiền điều kiện**: Đã chọn phường
- **Luồng chính**:
  1. Xem danh sách dịch vụ có sẵn tại phường đã chọn
  2. Xem thông tin dịch vụ (phí, thời gian xử lý, mô tả)
  3. Chọn dịch vụ
  4. Chuyển sang bước chọn ngày
- **Kết quả**: Dịch vụ được chọn

#### UC-1.2.3: Chọn ngày và giờ hẹn
- **Mô tả**: Người dân chọn ngày và giờ hẹn phù hợp
- **Tiền điều kiện**: Đã chọn phường và dịch vụ
- **Luồng chính**:
  1. Xem lịch có sẵn (theo lịch dịch vụ)
  2. Chọn ngày (chỉ hiển thị ngày có lịch)
  3. Chọn giờ hẹn (nếu có nhiều khung giờ)
  4. Kiểm tra số lượng còn lại
  5. Chuyển sang bước upload hồ sơ
- **Luồng phụ**: Ngày đã hết chỗ → Thông báo, đề xuất ngày khác
- **Kết quả**: Ngày và giờ được chọn

#### UC-1.2.4: Upload hồ sơ
- **Mô tả**: Người dân upload các file hồ sơ cần thiết
- **Tiền điều kiện**: Đã chọn ngày, giờ và đăng nhập
- **Luồng chính**:
  1. Xem form yêu cầu hồ sơ (động theo dịch vụ)
  2. Điền thông tin vào form
  3. Upload các file đính kèm (theo yêu cầu)
  4. Xem lại thông tin
  5. Chuyển sang bước xác nhận
- **Kết quả**: Hồ sơ được upload

#### UC-1.2.5: Xác nhận và hoàn tất đặt lịch
- **Mô tả**: Người dân xác nhận và hoàn tất việc đặt lịch
- **Tiền điều kiện**: Đã upload hồ sơ
- **Luồng chính**:
  1. Xem lại toàn bộ thông tin đặt lịch
  2. Xác nhận thông tin
  3. Thanh toán (nếu dịch vụ có phí)
  4. Nhận mã hồ sơ
  5. Nhận thông báo xác nhận
- **Kết quả**: Đặt lịch thành công, hồ sơ được tạo

### 1.3. Quản lý lịch hẹn

#### UC-1.3.1: Xem danh sách lịch hẹn
- **Mô tả**: Người dân xem tất cả lịch hẹn của mình
- **Luồng chính**:
  1. Vào "Lịch hẹn của tôi" (`/my-bookings`)
  2. Xem danh sách lịch hẹn (có phân trang)
  3. Lọc theo trạng thái, ngày
  4. Xem thống kê (tổng số, đã tiếp nhận, đang xử lý, hoàn tất, hủy)
- **Kết quả**: Hiển thị danh sách lịch hẹn

#### UC-1.3.2: Xem chi tiết lịch hẹn
- **Mô tả**: Người dân xem thông tin chi tiết một lịch hẹn
- **Luồng chính**:
  1. Click vào một lịch hẹn
  2. Xem thông tin: Mã hồ sơ, dịch vụ, phường, ngày giờ hẹn, trạng thái
  3. Xem file đã upload
  4. Xem ghi chú từ cán bộ (nếu có)
  5. Xem lịch sử thay đổi trạng thái
- **Kết quả**: Hiển thị chi tiết lịch hẹn

#### UC-1.3.3: Hủy lịch hẹn
- **Mô tả**: Người dân hủy lịch hẹn (nếu chưa được xử lý)
- **Tiền điều kiện**: Lịch hẹn ở trạng thái "Đang chờ xử lý"
- **Luồng chính**:
  1. Vào chi tiết lịch hẹn
  2. Click "Hủy lịch hẹn"
  3. Nhập lý do hủy
  4. Xác nhận hủy
  5. Nhận thông báo xác nhận
- **Luồng phụ**: Hồ sơ đã được tiếp nhận → Không thể hủy
- **Kết quả**: Lịch hẹn được hủy, trạng thái chuyển sang "Đã hủy"

#### UC-1.3.4: Chỉnh sửa hồ sơ (nếu chưa xử lý)
- **Mô tả**: Người dân chỉnh sửa thông tin hồ sơ trước khi được tiếp nhận
- **Tiền điều kiện**: Hồ sơ ở trạng thái "Đang chờ xử lý"
- **Luồng chính**:
  1. Vào chi tiết lịch hẹn
  2. Click "Chỉnh sửa"
  3. Sửa thông tin hoặc upload file mới
  4. Lưu thay đổi
- **Kết quả**: Hồ sơ được cập nhật

### 1.4. Tra cứu hồ sơ

#### UC-1.4.1: Tra cứu theo CCCD
- **Mô tả**: Người dân tra cứu hồ sơ bằng số CCCD
- **Luồng chính**:
  1. Vào "Tra cứu hồ sơ" (`/tra-cuu`)
  2. Chọn "Tra cứu theo CCCD"
  3. Nhập số CCCD
  4. Xem danh sách hồ sơ liên quan
- **Kết quả**: Hiển thị danh sách hồ sơ

#### UC-1.4.2: Tra cứu theo mã hồ sơ
- **Mô tả**: Người dân tra cứu hồ sơ bằng mã hồ sơ
- **Luồng chính**:
  1. Vào "Tra cứu hồ sơ"
  2. Chọn "Tra cứu theo mã hồ sơ"
  3. Nhập mã hồ sơ
  4. Xem thông tin chi tiết hồ sơ
- **Kết quả**: Hiển thị thông tin hồ sơ

#### UC-1.4.3: Xem trạng thái hồ sơ (public)
- **Mô tả**: Người dân xem trạng thái hồ sơ mà không cần đăng nhập
- **Luồng chính**:
  1. Tra cứu bằng mã hồ sơ
  2. Xem thông tin: Trạng thái, ngày hẹn, cán bộ xử lý, ghi chú
  3. Xem lịch sử xử lý
- **Kết quả**: Hiển thị trạng thái hồ sơ

### 1.5. Thông báo

#### UC-1.5.1: Xem danh sách thông báo
- **Mô tả**: Người dân xem tất cả thông báo của mình
- **Luồng chính**:
  1. Vào "Thông báo" (`/notifications`)
  2. Xem danh sách thông báo (chưa đọc/đã đọc)
  3. Lọc theo loại thông báo
- **Kết quả**: Hiển thị danh sách thông báo

#### UC-1.5.2: Đánh dấu đã đọc
- **Mô tả**: Người dân đánh dấu thông báo là đã đọc
- **Luồng chính**:
  1. Click vào thông báo
  2. Tự động đánh dấu đã đọc
  3. Hoặc click "Đánh dấu tất cả đã đọc"
- **Kết quả**: Thông báo được đánh dấu đã đọc

#### UC-1.5.3: Nhận thông báo
- **Mô tả**: Người dân nhận thông báo về các sự kiện
- **Các loại thông báo**:
  - Nhắc lịch hẹn (trước 1 ngày)
  - Thay đổi trạng thái hồ sơ
  - Yêu cầu bổ sung hồ sơ
  - Hoàn tất xử lý hồ sơ
  - Hủy lịch hẹn
- **Kết quả**: Nhận thông báo realtime

### 1.6. Đánh giá dịch vụ

#### UC-1.6.1: Tạo đánh giá
- **Mô tả**: Người dân đánh giá dịch vụ sau khi hoàn tất
- **Tiền điều kiện**: Hồ sơ đã hoàn tất và chưa đánh giá
- **Luồng chính**:
  1. Vào chi tiết lịch hẹn đã hoàn tất
  2. Click "Đánh giá dịch vụ"
  3. Chọn điểm (1-5 sao)
  4. Viết bình luận (tùy chọn)
  5. Gửi đánh giá
- **Kết quả**: Đánh giá được lưu và gán cho cán bộ xử lý

#### UC-1.6.2: Chỉnh sửa đánh giá
- **Mô tả**: Người dân chỉnh sửa đánh giá đã tạo
- **Luồng chính**:
  1. Vào chi tiết lịch hẹn
  2. Click "Chỉnh sửa đánh giá"
  3. Sửa điểm và bình luận
  4. Lưu thay đổi
- **Kết quả**: Đánh giá được cập nhật

### 1.7. Chat và hỗ trợ

#### UC-1.7.1: Chat với AI chatbot
- **Mô tả**: Người dân chat với AI để được tư vấn tự động
- **Luồng chính**:
  1. Vào trang chat
  2. Gửi câu hỏi
  3. Nhận câu trả lời từ AI (Rasa chatbot)
  4. Tiếp tục hỏi hoặc chuyển sang chat với cán bộ
- **Kết quả**: Nhận được tư vấn từ AI

#### UC-1.7.2: Chat với cán bộ
- **Mô tả**: Người dân chat trực tiếp với cán bộ
- **Luồng chính**:
  1. Tạo phòng chat hoặc chuyển từ AI chat
  2. Gửi tin nhắn
  3. Nhận phản hồi từ cán bộ (realtime)
  4. Upload file trong chat (nếu cần)
- **Kết quả**: Giao tiếp với cán bộ thành công

### 1.8. Thanh toán

#### UC-1.8.1: Thanh toán phí dịch vụ
- **Mô tả**: Người dân thanh toán phí dịch vụ (nếu có)
- **Tiền điều kiện**: Dịch vụ có phí
- **Luồng chính**:
  1. Sau khi đặt lịch, vào trang thanh toán
  2. Xem thông tin thanh toán (số tiền, dịch vụ)
  3. Chọn phương thức thanh toán (VNPay, ZaloPay)
  4. Thực hiện thanh toán
  5. Nhận xác nhận thanh toán
- **Kết quả**: Thanh toán thành công

#### UC-1.8.2: Xem lịch sử thanh toán
- **Mô tả**: Người dân xem lịch sử các giao dịch thanh toán
- **Luồng chính**:
  1. Vào "Lịch sử thanh toán" (`/payment`)
  2. Xem danh sách các giao dịch
  3. Xem chi tiết từng giao dịch
- **Kết quả**: Hiển thị lịch sử thanh toán

### 1.9. Xem thông tin công khai

#### UC-1.9.1: Xem tin tức/bài viết
- **Mô tả**: Người dân xem các tin tức, bài viết trên website
- **Luồng chính**:
  1. Vào "Tin tức" (`/posts`)
  2. Xem danh sách bài viết
  3. Click vào bài viết để xem chi tiết
  4. Bình luận (nếu đã đăng nhập)
- **Kết quả**: Xem được thông tin

#### UC-1.9.2: Liên hệ
- **Mô tả**: Người dân gửi yêu cầu liên hệ
- **Luồng chính**:
  1. Vào "Liên hệ" (`/contact`)
  2. Điền form liên hệ
  3. Gửi yêu cầu
- **Kết quả**: Yêu cầu được gửi

---

## 2. CÁN BỘ (STAFF/OFFICER)

### 2.1. Quản lý tài khoản

#### UC-2.1.1: Đăng nhập
- **Mô tả**: Cán bộ đăng nhập vào hệ thống admin
- **Luồng chính**:
  1. Truy cập `/admin/login`
  2. Nhập tên đăng nhập và mật khẩu
  3. Xác thực thành công
  4. Chuyển đến dashboard
- **Kết quả**: Đăng nhập thành công

#### UC-2.1.2: Xem và cập nhật thông tin cá nhân
- **Mô tả**: Cán bộ xem và chỉnh sửa thông tin cá nhân
- **Luồng chính**:
  1. Vào "Tài khoản" → "Thông tin cá nhân"
  2. Xem thông tin hiện tại
  3. Chỉnh sửa (nếu có quyền)
  4. Lưu thay đổi
- **Kết quả**: Thông tin được cập nhật

#### UC-2.1.3: Đổi mật khẩu
- **Mô tả**: Cán bộ thay đổi mật khẩu
- **Luồng chính**:
  1. Vào "Tài khoản" → "Đổi mật khẩu"
  2. Nhập mật khẩu cũ
  3. Nhập mật khẩu mới
  4. Xác nhận
- **Kết quả**: Mật khẩu được thay đổi

### 2.2. Quản lý hồ sơ được phân công

#### UC-2.2.1: Xem danh sách hồ sơ được phân công
- **Mô tả**: Cán bộ xem tất cả hồ sơ được phân công cho mình
- **Luồng chính**:
  1. Vào "Hồ sơ" (`/admin/ho-so`)
  2. Xem danh sách hồ sơ (sắp xếp theo ngày hẹn và số thứ tự)
  3. Lọc theo trạng thái, ngày
  4. Tìm kiếm theo mã hồ sơ, tên người dân
  5. Xem thống kê nhanh
- **Kết quả**: Hiển thị danh sách hồ sơ

#### UC-2.2.2: Xem chi tiết hồ sơ
- **Mô tả**: Cán bộ xem thông tin chi tiết một hồ sơ
- **Luồng chính**:
  1. Click vào một hồ sơ
  2. Xem thông tin: Mã hồ sơ, người dân, dịch vụ, ngày giờ hẹn
  3. Xem file đính kèm (download nếu cần)
  4. Xem thông tin form động
  5. Xem lịch sử xử lý
  6. Xem ghi chú trước đó
- **Kết quả**: Hiển thị chi tiết hồ sơ

#### UC-2.2.3: Xem hồ sơ hôm nay
- **Mô tả**: Cán bộ xem danh sách hồ sơ cần xử lý trong ngày
- **Luồng chính**:
  1. Vào "File" (`/admin/file`)
  2. Xem danh sách hồ sơ có ngày hẹn = hôm nay
  3. Sắp xếp theo số thứ tự
- **Kết quả**: Hiển thị hồ sơ hôm nay

### 2.3. Xử lý hồ sơ

#### UC-2.3.1: Tiếp nhận hồ sơ
- **Mô tả**: Cán bộ tiếp nhận và bắt đầu xử lý hồ sơ
- **Tiền điều kiện**: Hồ sơ ở trạng thái "Đang chờ xử lý"
- **Luồng chính**:
  1. Vào chi tiết hồ sơ
  2. Click "Tiếp nhận"
  3. Cập nhật trạng thái thành "Đã tiếp nhận"
  4. Tự động gửi thông báo cho người dân
- **Kết quả**: Hồ sơ được tiếp nhận

#### UC-2.3.2: Cập nhật trạng thái xử lý
- **Mô tả**: Cán bộ cập nhật trạng thái trong quá trình xử lý
- **Các trạng thái**:
  - Đang xử lý
  - Cần bổ sung hồ sơ
  - Hoàn tất
- **Luồng chính**:
  1. Vào chi tiết hồ sơ
  2. Chọn trạng thái mới
  3. Nhập ghi chú (bắt buộc nếu "Cần bổ sung hồ sơ")
  4. Lưu thay đổi
  5. Tự động gửi thông báo cho người dân
- **Kết quả**: Trạng thái được cập nhật

#### UC-2.3.3: Yêu cầu bổ sung hồ sơ
- **Mô tả**: Cán bộ yêu cầu người dân bổ sung hồ sơ thiếu
- **Luồng chính**:
  1. Vào chi tiết hồ sơ
  2. Chọn trạng thái "Cần bổ sung hồ sơ"
  3. Nhập chi tiết yêu cầu bổ sung
  4. Lưu và gửi thông báo
- **Kết quả**: Người dân nhận yêu cầu bổ sung

#### UC-2.3.4: Hoàn tất xử lý hồ sơ
- **Mô tả**: Cán bộ đánh dấu hồ sơ đã hoàn tất
- **Luồng chính**:
  1. Vào chi tiết hồ sơ
  2. Chọn trạng thái "Hoàn tất"
  3. Nhập ghi chú kết quả (nếu cần)
  4. Lưu và gửi thông báo
- **Kết quả**: Hồ sơ được hoàn tất, người dân có thể đánh giá

#### UC-2.3.5: Hủy hồ sơ
- **Mô tả**: Cán bộ hủy hồ sơ (nếu có lý do chính đáng)
- **Luồng chính**:
  1. Vào chi tiết hồ sơ
  2. Click "Hủy hồ sơ"
  3. Nhập lý do hủy
  4. Xác nhận hủy
  5. Gửi thông báo cho người dân
- **Kết quả**: Hồ sơ được hủy

#### UC-2.3.6: Thêm ghi chú
- **Mô tả**: Cán bộ thêm ghi chú trong quá trình xử lý
- **Luồng chính**:
  1. Vào chi tiết hồ sơ
  2. Nhập ghi chú vào trường "Ghi chú"
  3. Lưu ghi chú
- **Kết quả**: Ghi chú được lưu

### 2.4. Chat với người dân

#### UC-2.4.1: Xem danh sách phòng chat
- **Mô tả**: Cán bộ xem danh sách các phòng chat với người dân
- **Luồng chính**:
  1. Vào "Chat" (`/admin/room-chats`)
  2. Xem danh sách phòng chat
  3. Lọc theo trạng thái (chưa nhận, đã nhận)
- **Kết quả**: Hiển thị danh sách phòng chat

#### UC-2.4.2: Nhận phòng chat
- **Mô tả**: Cán bộ nhận một phòng chat để trả lời
- **Luồng chính**:
  1. Xem danh sách phòng chat chưa được nhận
  2. Click "Nhận chat" hoặc "Nhận ngẫu nhiên"
  3. Phòng chat được gán cho cán bộ
- **Kết quả**: Cán bộ có thể trả lời chat

#### UC-2.4.3: Trả lời tin nhắn
- **Mô tả**: Cán bộ trả lời tin nhắn từ người dân
- **Luồng chính**:
  1. Vào phòng chat
  2. Xem lịch sử tin nhắn
  3. Gửi tin nhắn trả lời
  4. Upload file (nếu cần)
  5. Tin nhắn được gửi realtime
- **Kết quả**: Người dân nhận được phản hồi

### 2.5. Xem đánh giá

#### UC-2.5.1: Xem danh sách đánh giá
- **Mô tả**: Cán bộ xem các đánh giá về mình
- **Luồng chính**:
  1. Vào "Đánh giá" hoặc "Báo cáo"
  2. Xem danh sách đánh giá
  3. Xem điểm trung bình
  4. Xem chi tiết từng đánh giá
- **Kết quả**: Hiển thị đánh giá

#### UC-2.5.2: Xem thống kê đánh giá
- **Mô tả**: Cán bộ xem thống kê về đánh giá của mình
- **Luồng chính**:
  1. Vào trang thống kê
  2. Xem: Điểm trung bình, số lượt đánh giá, phân bố điểm
  3. Xem xu hướng theo thời gian
- **Kết quả**: Hiển thị thống kê

### 2.6. Báo cáo

#### UC-2.6.1: Xem báo cáo cá nhân
- **Mô tả**: Cán bộ xem báo cáo về hiệu suất làm việc của mình
- **Luồng chính**:
  1. Vào "Báo cáo"
  2. Xem thống kê: Số hồ sơ đã xử lý, thời gian trung bình, tỷ lệ hoàn tất
  3. Xem biểu đồ theo thời gian
- **Kết quả**: Hiển thị báo cáo

---

## 3. ADMIN PHƯỜNG (WARD ADMIN)

### 3.1. Quản lý tài khoản

#### UC-3.1.1: Đăng nhập
- **Mô tả**: Admin phường đăng nhập vào hệ thống
- **Luồng chính**:
  1. Truy cập `/admin/login`
  2. Nhập tên đăng nhập và mật khẩu
  3. Xác thực thành công
  4. Chuyển đến dashboard
- **Kết quả**: Đăng nhập thành công

#### UC-3.1.2: Xem và cập nhật thông tin cá nhân
- **Mô tả**: Admin phường quản lý thông tin cá nhân
- **Luồng chính**: Tương tự UC-2.1.2
- **Kết quả**: Thông tin được cập nhật

### 3.2. Quản lý cán bộ phường

#### UC-3.2.1: Xem danh sách cán bộ
- **Mô tả**: Admin phường xem danh sách cán bộ thuộc phường mình
- **Luồng chính**:
  1. Vào "Quản trị hệ thống" → "Quản lý tài khoản" (`/admin/quantri`)
  2. Xem danh sách cán bộ (chỉ cán bộ của phường mình)
  3. Tìm kiếm, lọc theo quyền
- **Kết quả**: Hiển thị danh sách cán bộ

#### UC-3.2.2: Tạo tài khoản cán bộ
- **Mô tả**: Admin phường tạo tài khoản mới cho cán bộ
- **Luồng chính**:
  1. Click "Thêm tài khoản"
  2. Nhập thông tin: Họ tên, tên đăng nhập, mật khẩu, email, SĐT
  3. Chọn quyền "Cán bộ phường"
  4. Chọn đơn vị (chỉ phường của mình)
  5. Tạo tài khoản
- **Kết quả**: Tài khoản cán bộ được tạo

#### UC-3.2.3: Chỉnh sửa thông tin cán bộ
- **Mô tả**: Admin phường chỉnh sửa thông tin cán bộ
- **Luồng chính**:
  1. Click vào một cán bộ
  2. Chỉnh sửa thông tin (không thể thay đổi quyền hoặc phường)
  3. Lưu thay đổi
- **Kết quả**: Thông tin được cập nhật

#### UC-3.2.4: Xóa cán bộ
- **Mô tả**: Admin phường xóa tài khoản cán bộ
- **Tiền điều kiện**: Cán bộ không còn hồ sơ đang xử lý
- **Luồng chính**:
  1. Click "Xóa" trên một cán bộ
  2. Xác nhận xóa
- **Kết quả**: Tài khoản được xóa

#### UC-3.2.5: Import cán bộ từ Excel
- **Mô tả**: Admin phường import nhiều cán bộ cùng lúc
- **Luồng chính**:
  1. Click "Import cán bộ"
  2. Chọn đơn vị/phường
  3. Upload file Excel (theo template)
  4. Xem preview
  5. Xác nhận import
- **Kết quả**: Nhiều cán bộ được tạo cùng lúc

### 3.3. Quản lý hồ sơ phường

#### UC-3.3.1: Xem tất cả hồ sơ của phường
- **Mô tả**: Admin phường xem tất cả hồ sơ trong phường mình
- **Luồng chính**:
  1. Vào "Hồ sơ" (`/admin/ho-so`)
  2. Xem danh sách hồ sơ (group theo cán bộ)
  3. Lọc theo trạng thái, ngày, cán bộ
  4. Tìm kiếm
  5. Xem thống kê
- **Kết quả**: Hiển thị danh sách hồ sơ

#### UC-3.3.2: Phân công hồ sơ cho cán bộ
- **Mô tả**: Admin phường phân công hồ sơ cho cán bộ xử lý
- **Tiền điều kiện**: Hồ sơ chưa được phân công hoặc cần phân công lại
- **Luồng chính**:
  1. Vào chi tiết hồ sơ
  2. Click "Phân công"
  3. Chọn cán bộ (chỉ cán bộ trong phường)
  4. Xác nhận phân công
  5. Cán bộ nhận được thông báo
- **Kết quả**: Hồ sơ được phân công

#### UC-3.3.3: Xem chi tiết hồ sơ
- **Mô tả**: Admin phường xem chi tiết bất kỳ hồ sơ nào trong phường
- **Luồng chính**: Tương tự UC-2.2.2
- **Kết quả**: Hiển thị chi tiết hồ sơ

#### UC-3.3.4: Hủy hồ sơ
- **Mô tả**: Admin phường hủy hồ sơ (nếu cần)
- **Luồng chính**: Tương tự UC-2.3.5
- **Kết quả**: Hồ sơ được hủy

### 3.4. Quản lý dịch vụ phường

#### UC-3.4.1: Xem danh sách dịch vụ của phường
- **Mô tả**: Admin phường xem các dịch vụ được kích hoạt cho phường
- **Luồng chính**:
  1. Vào "Dịch vụ phường" (`/admin/service-phuong`)
  2. Xem danh sách dịch vụ
  3. Xem trạng thái kích hoạt
- **Kết quả**: Hiển thị danh sách dịch vụ

#### UC-3.4.2: Tùy chỉnh dịch vụ cho phường
- **Mô tả**: Admin phường tùy chỉnh thông tin dịch vụ cho phường mình
- **Luồng chính**:
  1. Chọn một dịch vụ
  2. Tùy chỉnh: Thời gian xử lý, số lượng/ngày, phí dịch vụ
  3. Kích hoạt/tắt dịch vụ
  4. Lưu thay đổi
- **Kết quả**: Dịch vụ được tùy chỉnh (không ảnh hưởng phường khác)

#### UC-3.4.3: Quản lý lịch dịch vụ
- **Mô tả**: Admin phường quản lý lịch làm việc của dịch vụ
- **Luồng chính**:
  1. Vào "Lịch dịch vụ"
  2. Xem/chỉnh sửa lịch: Thứ, giờ, số lượng tối đa
  3. Lưu thay đổi
- **Kết quả**: Lịch dịch vụ được cập nhật

### 3.5. Thống kê và báo cáo

#### UC-3.5.1: Xem thống kê tổng quan
- **Mô tả**: Admin phường xem thống kê về hoạt động của phường
- **Luồng chính**:
  1. Vào "Báo cáo" hoặc Dashboard
  2. Xem thống kê:
     - Số lượng hồ sơ theo trạng thái
     - Số lượng hồ sơ theo dịch vụ
     - Thời gian xử lý trung bình
     - Khung giờ cao điểm
  3. Xem biểu đồ
- **Kết quả**: Hiển thị thống kê

#### UC-3.5.2: Xem thống kê đánh giá
- **Mô tả**: Admin phường xem thống kê đánh giá của phường
- **Luồng chính**:
  1. Vào "Báo cáo" → "Đánh giá nhân viên"
  2. Xem: Điểm trung bình phường, điểm từng cán bộ
  3. Xem phân bố điểm, xu hướng
- **Kết quả**: Hiển thị thống kê đánh giá

#### UC-3.5.3: Xem thống kê hiệu suất cán bộ
- **Mô tả**: Admin phường xem hiệu suất làm việc của từng cán bộ
- **Luồng chính**:
  1. Vào trang thống kê cán bộ
  2. Xem: Số hồ sơ đã xử lý, thời gian trung bình, tỷ lệ hoàn tất
  3. So sánh giữa các cán bộ
- **Kết quả**: Hiển thị thống kê hiệu suất

#### UC-3.5.4: Xuất báo cáo Excel/PDF
- **Mô tả**: Admin phường xuất báo cáo ra file
- **Luồng chính**:
  1. Vào trang báo cáo
  2. Chọn khoảng thời gian
  3. Click "Xuất Excel" hoặc "Xuất PDF"
  4. Download file
- **Kết quả**: File báo cáo được tạo

### 3.6. Quản lý người dân

#### UC-3.6.1: Xem danh sách người dân trong phường
- **Mô tả**: Admin phường xem danh sách người dân thuộc phường
- **Luồng chính**:
  1. Vào "Quản lý người dân" (`/admin/users`)
  2. Lọc theo phường (chỉ phường của mình)
  3. Xem danh sách
- **Kết quả**: Hiển thị danh sách người dân

#### UC-3.6.2: Xem lịch sử đặt lịch của người dân
- **Mô tả**: Admin phường xem lịch sử đặt lịch của một người dân
- **Luồng chính**:
  1. Click vào một người dân
  2. Xem lịch sử đặt lịch
  3. Xem lịch sử hồ sơ
- **Kết quả**: Hiển thị lịch sử

### 3.7. Chat và hỗ trợ

#### UC-3.7.1: Xem danh sách phòng chat
- **Mô tả**: Admin phường xem các phòng chat trong phường
- **Luồng chính**: Tương tự UC-2.4.1
- **Kết quả**: Hiển thị danh sách chat

#### UC-3.7.2: Trả lời chat (nếu cần)
- **Mô tả**: Admin phường có thể trả lời chat nếu cần
- **Luồng chính**: Tương tự UC-2.4.3
- **Kết quả**: Trả lời thành công

---

## 4. ADMIN TỔNG (SUPER ADMIN)

### 4.1. Quản lý tài khoản

#### UC-4.1.1: Đăng nhập
- **Mô tả**: Admin tổng đăng nhập vào hệ thống
- **Luồng chính**: Tương tự UC-2.1.1
- **Kết quả**: Đăng nhập thành công

#### UC-4.1.2: Quản lý thông tin cá nhân
- **Mô tả**: Admin tổng quản lý thông tin cá nhân
- **Luồng chính**: Tương tự UC-2.1.2
- **Kết quả**: Thông tin được cập nhật

### 4.2. Quản lý đơn vị/phường

#### UC-4.2.1: Xem danh sách tất cả phường
- **Mô tả**: Admin tổng xem danh sách tất cả phường trong hệ thống
- **Luồng chính**:
  1. Vào "Quản lý đơn vị/phường" (`/admin/don-vi`)
  2. Xem danh sách tất cả phường
  3. Tìm kiếm, lọc
- **Kết quả**: Hiển thị danh sách phường

#### UC-4.2.2: Tạo phường mới
- **Mô tả**: Admin tổng tạo đơn vị/phường mới
- **Luồng chính**:
  1. Click "Thêm đơn vị/phường"
  2. Nhập thông tin: Tên đơn vị, mô tả
  3. Tạo phường
- **Kết quả**: Phường mới được tạo

#### UC-4.2.3: Chỉnh sửa thông tin phường
- **Mô tả**: Admin tổng chỉnh sửa thông tin phường
- **Luồng chính**:
  1. Click vào một phường
  2. Chỉnh sửa thông tin
  3. Lưu thay đổi
- **Kết quả**: Thông tin được cập nhật

#### UC-4.2.4: Xóa phường
- **Mô tả**: Admin tổng xóa phường (nếu không còn cán bộ)
- **Tiền điều kiện**: Phường không còn cán bộ
- **Luồng chính**:
  1. Click "Xóa" trên một phường
  2. Xác nhận xóa
- **Kết quả**: Phường được xóa

### 4.3. Quản lý tài khoản toàn hệ thống

#### UC-4.3.1: Xem danh sách tất cả tài khoản
- **Mô tả**: Admin tổng xem tất cả tài khoản trong hệ thống
- **Luồng chính**:
  1. Vào "Quản trị hệ thống" → "Quản lý tài khoản" (`/admin/quantri`)
  2. Xem danh sách tất cả tài khoản (Admin tổng, Admin phường, Cán bộ)
  3. Lọc theo quyền, phường
  4. Tìm kiếm
- **Kết quả**: Hiển thị danh sách tài khoản

#### UC-4.3.2: Tạo tài khoản Admin phường
- **Mô tả**: Admin tổng tạo tài khoản cho Admin phường
- **Luồng chính**:
  1. Click "Thêm tài khoản"
  2. Nhập thông tin
  3. Chọn quyền "Admin phường"
  4. Chọn phường
  5. Tạo tài khoản
- **Kết quả**: Tài khoản Admin phường được tạo

#### UC-4.3.3: Tạo tài khoản Cán bộ
- **Mô tả**: Admin tổng tạo tài khoản cho Cán bộ
- **Luồng chính**: Tương tự UC-3.2.2 (nhưng có thể chọn bất kỳ phường nào)
- **Kết quả**: Tài khoản cán bộ được tạo

#### UC-4.3.4: Chỉnh sửa tài khoản
- **Mô tả**: Admin tổng chỉnh sửa bất kỳ tài khoản nào
- **Luồng chính**:
  1. Click vào một tài khoản
  2. Chỉnh sửa thông tin (có thể thay đổi quyền, phường)
  3. Lưu thay đổi
- **Kết quả**: Thông tin được cập nhật

#### UC-4.3.5: Xóa tài khoản
- **Mô tả**: Admin tổng xóa tài khoản (trừ chính mình)
- **Tiền điều kiện**: Không phải tài khoản của chính mình
- **Luồng chính**:
  1. Click "Xóa" trên một tài khoản
  2. Xác nhận xóa
- **Kết quả**: Tài khoản được xóa

#### UC-4.3.6: Import cán bộ
- **Mô tả**: Admin tổng import nhiều cán bộ từ Excel
- **Luồng chính**: Tương tự UC-3.2.5 (có thể chọn bất kỳ phường nào)
- **Kết quả**: Nhiều cán bộ được tạo

### 4.4. Quản lý dịch vụ toàn hệ thống

#### UC-4.4.1: Xem danh sách tất cả dịch vụ
- **Mô tả**: Admin tổng xem tất cả dịch vụ trong hệ thống
- **Luồng chính**:
  1. Vào "Dịch vụ" (`/admin/services`)
  2. Xem danh sách dịch vụ
  3. Tìm kiếm, lọc
- **Kết quả**: Hiển thị danh sách dịch vụ

#### UC-4.4.2: Tạo dịch vụ mới
- **Mô tả**: Admin tổng tạo dịch vụ mới cho toàn hệ thống
- **Luồng chính**:
  1. Click "Thêm dịch vụ"
  2. Nhập thông tin: Tên dịch vụ, mô tả, phí mặc định, thời gian xử lý mặc định
  3. Tạo form động (các trường yêu cầu)
  4. Lưu dịch vụ
- **Kết quả**: Dịch vụ mới được tạo

#### UC-4.4.3: Chỉnh sửa dịch vụ
- **Mô tả**: Admin tổng chỉnh sửa thông tin dịch vụ
- **Luồng chính**:
  1. Click vào một dịch vụ
  2. Chỉnh sửa thông tin
  3. Quản lý form động (thêm/sửa/xóa trường)
  4. Lưu thay đổi
- **Kết quả**: Dịch vụ được cập nhật

#### UC-4.4.4: Xóa dịch vụ
- **Mô tả**: Admin tổng xóa dịch vụ (nếu không còn hồ sơ)
- **Tiền điều kiện**: Dịch vụ không còn hồ sơ
- **Luồng chính**:
  1. Click "Xóa" trên một dịch vụ
  2. Xác nhận xóa
- **Kết quả**: Dịch vụ được xóa

#### UC-4.4.5: Quản lý form động của dịch vụ
- **Mô tả**: Admin tổng quản lý các trường form cho dịch vụ
- **Luồng chính**:
  1. Vào chi tiết dịch vụ
  2. Vào "Quản lý form"
  3. Thêm/sửa/xóa các trường (text, file, select, date...)
  4. Đánh dấu trường bắt buộc
  5. Lưu thay đổi
- **Kết quả**: Form được cập nhật

#### UC-4.4.6: Phân phối dịch vụ cho phường
- **Mô tả**: Admin tổng kích hoạt dịch vụ cho các phường
- **Luồng chính**:
  1. Vào "Dịch vụ phường"
  2. Chọn dịch vụ và phường
  3. Kích hoạt dịch vụ cho phường
  4. Thiết lập thông tin mặc định (có thể phường tự tùy chỉnh)
- **Kết quả**: Dịch vụ được phân phối

### 4.5. Quản lý hồ sơ toàn hệ thống

#### UC-4.5.1: Xem tất cả hồ sơ
- **Mô tả**: Admin tổng xem tất cả hồ sơ trong hệ thống
- **Luồng chính**:
  1. Vào "Hồ sơ" (`/admin/ho-so`)
  2. Chọn phường để xem (bắt buộc)
  3. Xem danh sách hồ sơ của phường đó
  4. Lọc, tìm kiếm
- **Kết quả**: Hiển thị danh sách hồ sơ

#### UC-4.5.2: Xem chi tiết hồ sơ
- **Mô tả**: Admin tổng xem chi tiết bất kỳ hồ sơ nào
- **Luồng chính**: Tương tự UC-2.2.2
- **Kết quả**: Hiển thị chi tiết hồ sơ

#### UC-4.5.3: Phân công hồ sơ
- **Mô tả**: Admin tổng phân công hồ sơ cho cán bộ
- **Luồng chính**: Tương tự UC-3.3.2 (có thể phân công bất kỳ hồ sơ nào)
- **Kết quả**: Hồ sơ được phân công

### 4.6. Báo cáo và thống kê

#### UC-4.6.1: Xem báo cáo tổng hợp
- **Mô tả**: Admin tổng xem báo cáo tổng hợp toàn hệ thống
- **Luồng chính**:
  1. Vào "Báo cáo" → "Báo cáo tổng hợp" (`/admin/reports`)
  2. Xem thống kê:
     - Tổng số hồ sơ toàn hệ thống
     - Phân bố theo phường
     - Phân bố theo dịch vụ
     - Xu hướng theo thời gian
     - Thời gian xử lý trung bình
  3. Xem biểu đồ, biểu đồ so sánh
- **Kết quả**: Hiển thị báo cáo tổng hợp

#### UC-4.6.2: Xem bảng xếp hạng phường
- **Mô tả**: Admin tổng xem bảng xếp hạng hiệu suất các phường
- **Luồng chính**:
  1. Vào trang báo cáo
  2. Xem bảng xếp hạng theo:
     - Số lượng hồ sơ đã xử lý
     - Thời gian xử lý trung bình
     - Điểm đánh giá trung bình
     - Tỷ lệ hoàn tất
- **Kết quả**: Hiển thị bảng xếp hạng

#### UC-4.6.3: Xem bảng xếp hạng cán bộ
- **Mô tả**: Admin tổng xem bảng xếp hạng hiệu suất cán bộ
- **Luồng chính**:
  1. Vào "Báo cáo" → "Đánh giá nhân viên"
  2. Xem bảng xếp hạng cán bộ toàn hệ thống
  3. Xem chi tiết từng cán bộ
- **Kết quả**: Hiển thị bảng xếp hạng

#### UC-4.6.4: Xem dashboard tổng quan
- **Mô tả**: Admin tổng xem dashboard với các chỉ số quan trọng
- **Luồng chính**:
  1. Vào Dashboard (`/admin`)
  2. Xem các chỉ số:
     - Tổng số hồ sơ hôm nay
     - Tổng số hồ sơ đang xử lý
     - Tổng số người dân
     - Tổng số cán bộ
  3. Xem biểu đồ xu hướng
  4. Xem thống kê theo phường
- **Kết quả**: Hiển thị dashboard

#### UC-4.6.5: Xuất báo cáo Excel/PDF
- **Mô tả**: Admin tổng xuất báo cáo ra file
- **Luồng chính**:
  1. Vào trang báo cáo
  2. Chọn loại báo cáo, khoảng thời gian, phường (nếu cần)
  3. Click "Xuất Excel" hoặc "Xuất PDF"
  4. Download file
- **Kết quả**: File báo cáo được tạo

### 4.7. Quản lý người dân

#### UC-4.7.1: Xem danh sách tất cả người dân
- **Mô tả**: Admin tổng xem danh sách tất cả người dân
- **Luồng chính**:
  1. Vào "Quản lý người dân" (`/admin/users`)
  2. Xem danh sách tất cả người dân
  3. Lọc theo phường
  4. Tìm kiếm
- **Kết quả**: Hiển thị danh sách người dân

#### UC-4.7.2: Xem chi tiết người dân
- **Mô tả**: Admin tổng xem thông tin chi tiết một người dân
- **Luồng chính**:
  1. Click vào một người dân
  2. Xem thông tin cá nhân
  3. Xem lịch sử đặt lịch
  4. Xem lịch sử hồ sơ
- **Kết quả**: Hiển thị chi tiết người dân

#### UC-4.7.3: Chỉnh sửa thông tin người dân
- **Mô tả**: Admin tổng chỉnh sửa thông tin người dân (nếu cần)
- **Luồng chính**:
  1. Vào chi tiết người dân
  2. Chỉnh sửa thông tin
  3. Lưu thay đổi
- **Kết quả**: Thông tin được cập nhật

### 4.8. Cấu hình hệ thống

#### UC-4.8.1: Cấu hình website
- **Mô tả**: Admin tổng cấu hình thông tin website
- **Luồng chính**:
  1. Vào "Cấu hình website" (`/admin/settings`)
  2. Cấu hình: Tên website, logo, thông tin liên hệ, email
  3. Lưu cấu hình
- **Kết quả**: Cấu hình được lưu

#### UC-4.8.2: Quản lý thông báo hệ thống
- **Mô tả**: Admin tổng quản lý các thông báo công khai
- **Luồng chính**:
  1. Vào "Thông báo" (`/admin/notifications`)
  2. Xem danh sách thông báo
  3. Tạo/sửa/xóa thông báo
  4. Đăng thông báo lên website
- **Kết quả**: Thông báo được quản lý

#### UC-4.8.3: Quản lý bài viết/tin tức
- **Mô tả**: Admin tổng quản lý các bài viết trên website
- **Luồng chính**:
  1. Vào "Bài viết" hoặc "Tin tức"
  2. Tạo/sửa/xóa bài viết
  3. Đăng bài
- **Kết quả**: Bài viết được quản lý

---

## 📊 TÓM TẮT SỐ LƯỢNG USE CASE

| Actor | Số lượng Use Case |
|-------|-------------------|
| Người dân | 30 |
| Cán bộ | 15 |
| Admin phường | 20 |
| Admin tổng | 25 |
| **TỔNG CỘNG** | **90** |

---

## 🔑 CÁC USE CASE QUAN TRỌNG NHẤT

### Người dân:
- UC-1.2.5: Xác nhận và hoàn tất đặt lịch
- UC-1.3.1: Xem danh sách lịch hẹn
- UC-1.4.2: Tra cứu theo mã hồ sơ

### Cán bộ:
- UC-2.2.1: Xem danh sách hồ sơ được phân công
- UC-2.3.2: Cập nhật trạng thái xử lý
- UC-2.4.3: Trả lời tin nhắn

### Admin phường:
- UC-3.2.2: Tạo tài khoản cán bộ
- UC-3.3.2: Phân công hồ sơ cho cán bộ
- UC-3.5.1: Xem thống kê tổng quan

### Admin tổng:
- UC-4.2.2: Tạo phường mới
- UC-4.4.2: Tạo dịch vụ mới
- UC-4.6.1: Xem báo cáo tổng hợp

---

*Tài liệu này được tạo dựa trên phân tích codebase của hệ thống đặt lịch dịch vụ hành chính công.*

