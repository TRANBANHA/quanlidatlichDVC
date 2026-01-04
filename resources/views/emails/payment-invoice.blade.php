<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn thanh toán</title>
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
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #4CAF50;
            margin: 0;
            font-size: 24px;
        }
        .invoice-info {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #666;
        }
        .info-value {
            color: #333;
            text-align: right;
        }
        .amount-section {
            background-color: #e8f5e9;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .amount-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .amount-value {
            font-size: 32px;
            font-weight: bold;
            color: #4CAF50;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-success {
            background-color: #4CAF50;
            color: white;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .thank-you {
            text-align: center;
            margin: 20px 0;
            color: #4CAF50;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>HÓA ĐƠN THANH TOÁN</h1>
            <p style="margin: 5px 0; color: #666;">Hệ thống quản lý đặt lịch dịch vụ công</p>
        </div>

        <div class="thank-you">
            ✓ Thanh toán thành công!
        </div>

        <div class="invoice-info">
            <div class="info-row">
                <span class="info-label">Mã giao dịch:</span>
                <span class="info-value">{{ $payment->ma_giao_dich }}</span>
            </div>
            @if($payment->hoSo)
            <div class="info-row">
                <span class="info-label">Mã hồ sơ:</span>
                <span class="info-value">{{ $payment->hoSo->ma_ho_so ?? 'N/A' }}</span>
            </div>
            @endif
            @if($payment->hoSo && $payment->hoSo->dichVu)
            <div class="info-row">
                <span class="info-label">Dịch vụ:</span>
                <span class="info-value">{{ $payment->hoSo->dichVu->ten_dich_vu ?? 'N/A' }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Phương thức thanh toán:</span>
                <span class="info-value">
                    @if($payment->phuong_thuc_thanh_toan == 'vnpay')
                        VNPay
                    @elseif($payment->phuong_thuc_thanh_toan == 'tien_mat')
                        Tiền mặt
                    @elseif($payment->phuong_thuc_thanh_toan == 'chuyen_khoan')
                        Chuyển khoản
                    @else
                        {{ $payment->phuong_thuc_thanh_toan ?? 'N/A' }}
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Trạng thái:</span>
                <span class="info-value">
                    <span class="status-badge status-success">
                        @if($payment->trang_thai_thanh_toan == 'da_thanh_toan')
                            Đã thanh toán
                        @else
                            {{ $payment->trang_thai_thanh_toan ?? 'N/A' }}
                        @endif
                    </span>
                </span>
            </div>
            @if($payment->ngay_thanh_toan)
            <div class="info-row">
                <span class="info-label">Ngày thanh toán:</span>
                <span class="info-value">{{ $payment->ngay_thanh_toan->format('d/m/Y H:i:s') }}</span>
            </div>
            @endif
            @if($payment->du_lieu_vnpay && isset($payment->du_lieu_vnpay['bank_tran_no']))
            <div class="info-row">
                <span class="info-label">Mã giao dịch ngân hàng:</span>
                <span class="info-value">{{ $payment->du_lieu_vnpay['bank_tran_no'] }}</span>
            </div>
            @endif
        </div>

        <div class="amount-section">
            <div class="amount-label">Tổng tiền thanh toán</div>
            <div class="amount-value">{{ number_format($payment->so_tien, 0, ',', '.') }} đ</div>
        </div>

        @if($payment->hoSo && $payment->hoSo->donVi)
        <div class="invoice-info">
            <div class="info-row">
                <span class="info-label">Đơn vị:</span>
                <span class="info-value">{{ $payment->hoSo->donVi->ten_don_vi ?? 'N/A' }}</span>
            </div>
            @if($payment->hoSo->ngay_hen)
            <div class="info-row">
                <span class="info-label">Ngày hẹn:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($payment->hoSo->ngay_hen)->format('d/m/Y') }}</span>
            </div>
            @endif
            @if($payment->hoSo->gio_hen)
            <div class="info-row">
                <span class="info-label">Giờ hẹn:</span>
                <span class="info-value">{{ $payment->hoSo->gio_hen }}</span>
            </div>
            @endif
        </div>
        @endif

        <div class="footer">
            <p><strong>Cảm ơn bạn đã sử dụng dịch vụ!</strong></p>
            <p>Hóa đơn này được gửi tự động từ hệ thống.</p>
            <p>Nếu có thắc mắc, vui lòng liên hệ với chúng tôi.</p>
            <p style="margin-top: 15px; font-size: 11px; color: #999;">
                © {{ date('Y') }} Hệ thống quản lý đặt lịch dịch vụ công. Mọi quyền được bảo lưu.
            </p>
        </div>
    </div>
</body>
</html>
