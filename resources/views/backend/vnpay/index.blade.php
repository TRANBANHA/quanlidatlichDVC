@extends('backend.components.layout')

@section('title', 'Cấu Hình VNPay')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>Cấu Hình VNPay
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Cấu hình VNPay được quản lý qua file cấu hình và biến môi trường (.env). 
                        Để thay đổi cấu hình, vui lòng chỉnh sửa file <code>config/vnpay.php</code> hoặc file <code>.env</code>.
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="mb-3"><i class="fas fa-cog me-2"></i>Thông Tin Cấu Hình Hiện Tại</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Terminal ID (TmnCode):</th>
                                    <td>
                                        <strong class="font-monospace">{{ $config['tmn_code'] ?? 'N/A' }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Hash Secret:</th>
                                    <td>
                                        <strong class="font-monospace">{{ $config['hash_secret'] ?? 'N/A' }}</strong>
                                        <small class="text-muted d-block mt-1">(Chỉ hiển thị 10 ký tự đầu để bảo mật)</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>VNPay URL:</th>
                                    <td>
                                        <code>{{ $config['url'] ?? 'N/A' }}</code>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Return URL:</th>
                                    <td>
                                        <code>{{ $config['return_url'] ?? 'N/A' }}</code>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="mb-3"><i class="fas fa-book me-2"></i>Hướng Dẫn Cấu Hình</h6>
                                    <p class="small mb-2"><strong>1. Cấu hình qua .env:</strong></p>
                                    <pre class="bg-dark text-light p-2 rounded small"><code>VNPAY_TMN_CODE=HFTERFKR
VNPAY_HASH_SECRET=VNSPNEC6Y4KOYQFAMER56MPC11AGLN62
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL=http://your-domain.com/payment/vnpay/return</code></pre>
                                    
                                    <p class="small mb-2 mt-3"><strong>2. Hoặc chỉnh sửa:</strong></p>
                                    <p class="small mb-0"><code>config/vnpay.php</code></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="mb-3"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Lưu Ý Quan Trọng</h6>
                            <div class="alert alert-warning border">
                                <h6 class="mb-2"><strong>Nếu gặp lỗi "Website này chưa được phê duyệt":</strong></h6>
                                <ol class="mb-0">
                                    <li><strong>Liên hệ VNPay:</strong>
                                        <ul>
                                            <li>Email: <a href="mailto:support@vnpay.vn">support@vnpay.vn</a></li>
                                            <li>Hotline: 1900 5454 26</li>
                                            <li>Website: <a href="https://vnpay.vn" target="_blank">https://vnpay.vn</a></li>
                                        </ul>
                                    </li>
                                    <li><strong>Yêu cầu kích hoạt tài khoản:</strong> Gửi email với thông tin Terminal ID và yêu cầu kích hoạt tài khoản sandbox/production</li>
                                    <li><strong>Kiểm tra môi trường:</strong>
                                        <ul>
                                            <li>Sandbox (test): <code>https://sandbox.vnpayment.vn/paymentv2/vpcpay.html</code></li>
                                            <li>Production: <code>https://www.vnpayment.vn/paymentv2/vpcpay.html</code></li>
                                        </ul>
                                    </li>
                                    <li><strong>Thông tin cần cung cấp:</strong>
                                        <ul>
                                            <li>Terminal ID: <strong>{{ $config['tmn_code'] }}</strong></li>
                                            <li>Email đăng ký: <strong>tranbanha430116@gmail.com</strong></li>
                                            <li>URL website của bạn</li>
                                        </ul>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="mb-3"><i class="fas fa-question-circle me-2"></i>Thông Tin Về VNPay</h6>
                            <div class="alert alert-light border">
                                <ul class="mb-0">
                                    <li><strong>VNPay</strong> là cổng thanh toán điện tử phổ biến tại Việt Nam</li>
                                    <li>Hỗ trợ thanh toán qua thẻ ATM nội địa, thẻ tín dụng/quốc tế</li>
                                    <li>Thanh toán được xử lý tự động và xác nhận ngay lập tức</li>
                                    <li>Hỗ trợ nhiều ngân hàng tại Việt Nam</li>
                                    <li>Bảo mật cao với mã hóa SSL/TLS</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

