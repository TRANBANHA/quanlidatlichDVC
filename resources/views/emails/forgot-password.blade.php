<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>

<body>
    <h1>Quên mật khẩu?</h1>
    <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu của bạn. Vui lòng nhấp vào liên kết dưới đây để tạo mật khẩu mới:
    </p>
    <a href="{{ url('/forgot-password?token=' . $token) }}">Đặt lại mật khẩu của bạn</a>
    <p>Chúc bạn một ngày tốt lành!</p>
</body>

</html>
