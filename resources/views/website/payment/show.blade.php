@extends('website.components.layout')

@section('title', 'Chi Tiết Thanh Toán')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Chi Tiết Thanh Toán</h5>
                </div>
                <div class="card-body">
                    <!-- Thông tin thanh toán -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Thông tin thanh toán</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Mã giao dịch:</th>
                                <td><strong>{{ $payment->ma_giao_dich }}</strong></td>
                            </tr>
                            <tr>
                                <th>Số tiền:</th>
                                <td><strong class="text-primary">{{ number_format($payment->so_tien) }} VNĐ</strong></td>
                            </tr>
                            <tr>
                                <th>Trạng thái:</th>
                                <td>
                                    @if($payment->trang_thai_thanh_toan == 'da_thanh_toan')
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    @elseif($payment->trang_thai_thanh_toan == 'cho_thanh_toan')
                                        <span class="badge bg-warning">Chờ thanh toán</span>
                                    @elseif($payment->trang_thai_thanh_toan == 'that_bai')
                                        <span class="badge bg-danger">Thất bại</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $payment->trang_thai_thanh_toan }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Phương thức:</th>
                                <td>
                                    @if($payment->phuong_thuc_thanh_toan == 'qr_code')
                                        <span class="badge bg-success"><i class="fas fa-qrcode me-1"></i>QR Code</span>
                                    @elseif($payment->phuong_thuc_thanh_toan == 'tien_mat')
                                        <span class="badge bg-secondary">Tiền mặt</span>
                                    @elseif($payment->phuong_thuc_thanh_toan == 'chuyen_khoan')
                                        <span class="badge bg-info">Chuyển khoản</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $payment->phuong_thuc_thanh_toan }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if($payment->ngay_thanh_toan)
                            <tr>
                                <th>Ngày thanh toán:</th>
                                <td>{{ $payment->ngay_thanh_toan->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Ngày tạo:</th>
                                <td>{{ $payment->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Thông tin hồ sơ -->
                    @if($payment->hoSo)
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Thông tin hồ sơ</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Mã hồ sơ:</th>
                                <td><strong>{{ $payment->hoSo->ma_ho_so }}</strong></td>
                            </tr>
                            <tr>
                                <th>Dịch vụ:</th>
                                <td>{{ $payment->hoSo->dichVu->ten_dich_vu ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Đơn vị:</th>
                                <td>{{ $payment->hoSo->donVi->ten_don_vi ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="{{ route('payment.index') }}" class="btn btn-primary">
                            <i class="fas fa-list me-2"></i>Danh sách thanh toán
                        </a>
                        <a href="{{ route('info.index', ['action' => 'tab2']) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

