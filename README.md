// CREATE migration

// lệnh tạo table
php artisan migrate
// lệnh run
php artisan serve

//run rasa
cd rasa
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\venv\Scripts\activate
rasa run --enable-api --cors "\*" --port 5005

//vnpay thông tin
Ngân hàng NCB
Số thẻ 9704198526191432198
Tên chủ thẻ NGUYEN VAN A
Ngày phát hành 07/15
Mật khẩu OTP 123456
