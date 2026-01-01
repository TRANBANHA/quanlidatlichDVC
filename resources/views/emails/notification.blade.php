<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo mới</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 30px;
            margin: 20px 0;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background-color: white;
            padding: 25px;
            border-radius: 0 0 8px 8px;
        }
        .message-box {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-item {
            margin: 10px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #777;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🔔 Thông báo mới</h2>
        </div>
        
        <div class="content">
            <p>Xin chào <strong>{{ $user->ten ?? 'Quý khách' }}</strong>,</p>
            
            <div class="message-box">
                <p style="margin: 0; font-size: 16px;">{{ $thongBao->message }}</p>
            </div>

            @if($thongBao->hoSo)
            <div class="info-item">
                <span class="info-label">📋 Mã hồ sơ:</span> 
                <strong>{{ $thongBao->hoSo->ma_ho_so ?? 'N/A' }}</strong>
            </div>
            @endif

            @if($thongBao->dichVu)
            <div class="info-item">
                <span class="info-label">🛎️ Dịch vụ:</span> 
                {{ $thongBao->dichVu->ten_dich_vu ?? 'N/A' }}
            </div>
            @endif

            @if($thongBao->ngay_hen)
            <div class="info-item">
                <span class="info-label">📅 Ngày hẹn:</span> 
                {{ \Carbon\Carbon::parse($thongBao->ngay_hen)->format('d/m/Y') }}
            </div>
            @endif

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('notifications.index') }}" class="btn">Xem chi tiết thông báo</a>
            </div>
        </div>

        <div class="footer">
            <p>Đây là email tự động từ hệ thống quản lý đặt lịch dịch vụ công.</p>
            <p>Vui lòng không trả lời email này.</p>
            <p>&copy; {{ date('Y') }} - Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>
