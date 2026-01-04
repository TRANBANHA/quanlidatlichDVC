<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VNPay Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình thông tin kết nối VNPay
    |
    */

    'tmn_code' => env('VNPAY_TMN_CODE', 'HFTERFKR'),
    'hash_secret' => env('VNPAY_HASH_SECRET', 'VNSPNEC6Y4KOYQFAMER56MPC11AGLN62'),
    
    // URL VNPay
    // Sandbox (test): https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
    // Production: https://www.vnpayment.vn/paymentv2/vpcpay.html
    'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    
    // Return URL sau khi thanh toán
    'return_url' => env('VNPAY_RETURN_URL', '/payment/vnpay/return'),
];

