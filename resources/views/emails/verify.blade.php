<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Tài Khoản</title>
</head>
<body>
    <h1>Xác Nhận Tài Khoản</h1>
    <p>Chào {{ $user->name }},</p>
    <p>Vui lòng nhấp vào liên kết dưới đây để xác nhận tài khoản của bạn:</p>
    <a href="{{ $verificationLink }}">Xác Nhận Tài Khoản</a>
    <p>Cảm ơn bạn đã đăng ký!</p>
</body>
</html>