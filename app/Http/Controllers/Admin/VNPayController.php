<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VNPayController extends Controller
{
    /**
     * Hiển thị trang cấu hình VNPay
     */
    public function index()
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Chỉ Admin tổng mới có quyền xem cấu hình VNPay
        if (!$currentUser->isAdmin()) {
            abort(403, 'Chỉ Admin tổng mới có quyền xem cấu hình VNPay.');
        }

        $config = [
            'tmn_code' => config('vnpay.tmn_code'),
            'hash_secret' => config('vnpay.hash_secret') ? substr(config('vnpay.hash_secret'), 0, 10) . '...' : '',
            'url' => config('vnpay.url'),
            'return_url' => config('vnpay.return_url'),
            'email' => 'tranbanha430116@gmail.com', // Email đăng ký VNPay
        ];

        return view('backend.vnpay.index', compact('config'));
    }
}

