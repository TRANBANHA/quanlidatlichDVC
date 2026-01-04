<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class VNPayService
{
    private $vnp_TmnCode;
    private $vnp_HashSecret;
    private $vnp_Url;
    private $vnp_Returnurl;

    public function __construct()
    {
        $this->vnp_TmnCode = config('vnpay.tmn_code', 'HFTERFKR');
        $this->vnp_HashSecret = config('vnpay.hash_secret', 'VNSPNEC6Y4KOYQFAMER56MPC11AGLN62');
        $this->vnp_Url = config('vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $returnUrl = config('vnpay.return_url', '/payment/vnpay/return');
        // Chuyển đổi relative URL thành absolute URL
        $this->vnp_Returnurl = str_starts_with($returnUrl, 'http') ? $returnUrl : url($returnUrl);
    }

    /**
     * Tạo URL thanh toán VNPay
     */
    public function createPaymentUrl($amount, $orderId, $orderDescription, $orderType = 'other', $bankCode = '', $language = 'vn')
    {
        $vnp_TxnRef = $orderId; // Mã đơn hàng
        $vnp_OrderInfo = $orderDescription;
        $vnp_OrderType = $orderType;
        $vnp_Amount = $amount * 100; // VNPay yêu cầu số tiền tính bằng xu
        $vnp_Locale = $language;
        $vnp_IpAddr = request()->ip();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $this->vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (!empty($bankCode)) {
            $inputData['vnp_BankCode'] = $bankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $this->vnp_Url . "?" . $query;
        if (isset($this->vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return $vnp_Url;
    }

    /**
     * Xác thực chữ ký từ VNPay callback
     */
    public function validateSignature($inputData)
    {
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);

        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->vnp_HashSecret);
        
        return $secureHash === $vnp_SecureHash;
    }

    /**
     * Xử lý kết quả thanh toán từ VNPay
     */
    public function processPaymentResult($inputData)
    {
        $result = [
            'success' => false,
            'message' => '',
            'data' => []
        ];

        // Kiểm tra chữ ký
        if (!$this->validateSignature($inputData)) {
            $result['message'] = 'Chữ ký không hợp lệ';
            return $result;
        }

        $vnp_ResponseCode = $inputData['vnp_ResponseCode'] ?? '';
        $vnp_TransactionStatus = $inputData['vnp_TransactionStatus'] ?? '';
        $vnp_TxnRef = $inputData['vnp_TxnRef'] ?? '';
        $vnp_Amount = $inputData['vnp_Amount'] ?? 0;
        $vnp_BankCode = $inputData['vnp_BankCode'] ?? '';
        $vnp_BankTranNo = $inputData['vnp_BankTranNo'] ?? '';
        $vnp_CardType = $inputData['vnp_CardType'] ?? '';
        $vnp_PayDate = $inputData['vnp_PayDate'] ?? '';

        // Mã phản hồi 00 = thành công
        if ($vnp_ResponseCode == '00' && $vnp_TransactionStatus == '00') {
            $result['success'] = true;
            $result['message'] = 'Thanh toán thành công';
            $result['data'] = [
                'order_id' => $vnp_TxnRef,
                'amount' => $vnp_Amount / 100, // Chuyển từ xu về VNĐ
                'bank_code' => $vnp_BankCode,
                'bank_tran_no' => $vnp_BankTranNo,
                'card_type' => $vnp_CardType,
                'pay_date' => $vnp_PayDate,
                'response_code' => $vnp_ResponseCode,
                'transaction_status' => $vnp_TransactionStatus,
            ];
        } else {
            $result['message'] = $this->getResponseMessage($vnp_ResponseCode);
            $result['data'] = [
                'order_id' => $vnp_TxnRef,
                'response_code' => $vnp_ResponseCode,
                'transaction_status' => $vnp_TransactionStatus,
            ];
        }

        return $result;
    }

    /**
     * Lấy thông báo lỗi từ mã phản hồi
     */
    private function getResponseMessage($responseCode)
    {
        $messages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Thẻ/Tài khoản chưa đăng ký dịch vụ InternetBanking',
            '10' => 'Xác thực thông tin thẻ/tài khoản không đúng. Quá 3 lần',
            '11' => 'Đã hết hạn chờ thanh toán. Xin vui lòng thực hiện lại giao dịch.',
            '12' => 'Thẻ/Tài khoản bị khóa.',
            '13' => 'Nhập sai mật khẩu xác thực giao dịch (OTP). Quá 3 lần.',
            '51' => 'Tài khoản không đủ số dư để thực hiện giao dịch.',
            '65' => 'Tài khoản đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Nhập sai mật khẩu thanh toán quá số lần quy định.',
            '99' => 'Lỗi không xác định',
        ];

        return $messages[$responseCode] ?? 'Lỗi không xác định';
    }
}

