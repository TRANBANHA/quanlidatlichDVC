<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin tài khoản</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-item {
            margin: 15px 0;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 150px;
        }
        .info-value {
            color: #007bff;
            font-size: 18px;
            font-weight: bold;
        }
        .password-box {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
        }
        .password-value {
            font-size: 24px;
            font-weight: bold;
            color: #856404;
            letter-spacing: 3px;
            font-family: 'Courier New', monospace;
        }
        .warning {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #721c24;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thông tin tài khoản cán bộ phường</h1>
        </div>

        <p>Xin chào <strong>{{ $hoTen }}</strong>,</p>
        
        <p>Tài khoản của bạn đã được tạo thành công. Dưới đây là thông tin đăng nhập:</p>

        <div class="info-box">
            <div class="info-item">
                <span class="info-label">Họ và tên:</span>
                <span class="info-value">{{ $hoTen }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Tên đăng nhập:</span>
                <span class="info-value">{{ $tenDangNhap }}</span>
            </div>
            @if($donVi)
            <div class="info-item">
                <span class="info-label">Đơn vị/Phường:</span>
                <span class="info-value">{{ $donVi }}</span>
            </div>
            @endif
        </div>

        <div class="password-box">
            <p style="margin: 0 0 10px 0; font-weight: bold; color: #856404;">Mật khẩu của bạn:</p>
            <div class="password-value">{{ $matKhau }}</div>
        </div>

        <div class="warning">
            <strong>⚠️ Lưu ý quan trọng:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                <li>Vui lòng lưu lại thông tin đăng nhập này</li>
                <li>Đăng nhập và đổi mật khẩu ngay sau lần đăng nhập đầu tiên</li>
                <li>Không chia sẻ thông tin đăng nhập với người khác</li>
            </ul>
        </div>

        <p>Bạn có thể đăng nhập vào hệ thống bằng thông tin trên.</p>

        <div class="footer">
            <p>Trân trọng,<br><strong>Hệ thống quản lý dịch vụ hành chính</strong></p>
            <p style="font-size: 12px; color: #999;">Email này được gửi tự động, vui lòng không trả lời.</p>
        </div>
    </div>
</body>
</html>

