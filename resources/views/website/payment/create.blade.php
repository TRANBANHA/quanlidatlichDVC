@extends('website.components.layout')

@section('title', 'Thanh Toán Hồ Sơ')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Thanh Toán Hồ Sơ</h5>
                </div>
                <div class="card-body">
                    @if($existingPayment && $existingPayment->trang_thai_thanh_toan == 'da_thanh_toan')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>Hồ sơ này đã được thanh toán thành công.
                        </div>
                        <a href="{{ route('payment.show', $existingPayment->id) }}" class="btn btn-primary">
                            <i class="fas fa-eye me-2"></i>Xem chi tiết thanh toán
                        </a>
                    @else
                        <!-- Thông tin hồ sơ -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Thông tin hồ sơ</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Mã hồ sơ:</th>
                                    <td><strong>{{ $hoSo->ma_ho_so }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Dịch vụ:</th>
                                    <td>{{ $hoSo->dichVu->ten_dich_vu ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Đơn vị:</th>
                                    <td>{{ $hoSo->donVi->ten_don_vi ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày hẹn:</th>
                                    <td>{{ $hoSo->ngay_hen ? $hoSo->ngay_hen->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Giờ hẹn:</th>
                                    <td>{{ $hoSo->gio_hen ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Thông tin thanh toán -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Thông tin thanh toán</h6>
                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><strong>Phí dịch vụ:</strong></span>
                                    <span class="h4 text-primary mb-0">{{ number_format($phiDichVu) }} VNĐ</span>
                                </div>
                            </div>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Phương thức thanh toán</h6>
                            
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h5 class="mb-3"><i class="fas fa-credit-card me-2 text-primary"></i>Thanh toán qua VNPay</h5>
                                    
                                    <p class="text-muted mb-4">Thanh toán an toàn và nhanh chóng qua cổng thanh toán VNPay. Bạn có thể thanh toán bằng thẻ ATM, thẻ tín dụng, hoặc ví điện tử.</p>
                                    
                                    <!-- Form thanh toán VNPay -->
                                    <form method="POST" action="{{ route('payment.vnpay.create', $hoSo->id) }}" id="vnpayForm">
                                        @csrf
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Chọn ngân hàng (Tùy chọn)</label>
                                            <select name="bank_code" class="form-select">
                                                <option value="">Chọn ngân hàng</option>
                                                <option value="NCB">Ngân hàng Quốc Dân (NCB)</option>
                                                <option value="VIETCOMBANK">Ngân hàng Ngoại Thương (Vietcombank)</option>
                                                <option value="VIETINBANK">Ngân hàng Công Thương (Vietinbank)</option>
                                                <option value="BIDV">Ngân hàng Đầu tư và Phát triển (BIDV)</option>
                                                <option value="AGRIBANK">Ngân hàng Nông nghiệp (Agribank)</option>
                                                <option value="SACOMBANK">Ngân hàng Sài Gòn Thương Tín (Sacombank)</option>
                                                <option value="TECHCOMBANK">Ngân hàng Kỹ Thương (Techcombank)</option>
                                                <option value="ACB">Ngân hàng Á Châu (ACB)</option>
                                                <option value="VPBANK">Ngân hàng Việt Nam Thịnh Vượng (VPBank)</option>
                                                <option value="TPBANK">Ngân hàng Tiên Phong (TPBank)</option>
                                                <option value="MBBANK">Ngân hàng Quân đội (MB Bank)</option>
                                                <option value="VIB">Ngân hàng Quốc tế (VIB)</option>
                                                <option value="SHB">Ngân hàng Sài Gòn - Hà Nội (SHB)</option>
                                                <option value="OCB">Ngân hàng Phương Đông (OCB)</option>
                                                <option value="DONGABANK">Ngân hàng Đông Á (DongA Bank)</option>
                                            </select>
                                            <small class="text-muted">Để trống nếu muốn chọn ngân hàng trên trang VNPay</small>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fas fa-credit-card me-2"></i>Thanh toán qua VNPay
                                            </button>
                                            <a href="{{ route('info.index', ['action' => 'tab2']) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                                            </a>
                                        </div>
                                    </form>
                                    
                                    <!-- Thông tin về VNPay -->
                                    <div class="alert alert-info mt-4 text-start">
                                        <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Thông tin về thanh toán VNPay:</h6>
                                        <ul class="mb-0 small">
                                            <li>Thanh toán được xử lý an toàn qua cổng VNPay</li>
                                            <li>Hỗ trợ thanh toán bằng thẻ ATM nội địa, thẻ tín dụng/quốc tế</li>
                                            <li>Thanh toán được xác nhận tự động sau khi hoàn tất</li>
                                            <li>Bạn sẽ được chuyển hướng đến trang thanh toán của VNPay</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($existingPayment && $existingPayment->trang_thai_thanh_toan == 'that_bai')
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Lần thanh toán trước đã thất bại. Vui lòng thử lại.
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
