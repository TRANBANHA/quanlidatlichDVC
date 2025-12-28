@extends('website.components.layout')

@section('title', 'Đặt lịch thành công')

@section('content')
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-success">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center" 
                                 style="width: 100px; height: 100px; font-size: 3rem;">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        
                        <h2 class="text-success mb-3">Đặt lịch thành công!</h2>
                        <p class="lead text-muted mb-4">Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi</p>

                        <div class="alert alert-info text-start mb-4">
                            <h5><i class="fas fa-info-circle me-2"></i>Thông tin đặt lịch</h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Mã hồ sơ:</strong><br>
                                    <span class="text-primary fs-5">{{ $hoSo->ma_ho_so }}</span>
                                </div>
                                @if($hoSo->so_thu_tu)
                                <div class="col-md-6 mb-2">
                                    <strong>Số thứ tự:</strong><br>
                                    <span class="text-success fs-5 fw-bold">{{ $hoSo->so_thu_tu }}</span>
                                </div>
                                @endif
                                <div class="col-md-6 mb-2">
                                    <strong>Dịch vụ:</strong><br>
                                    {{ $hoSo->dichVu->ten_dich_vu }}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Phường/Đơn vị:</strong><br>
                                    {{ $hoSo->donVi->ten_don_vi }}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Ngày hẹn:</strong><br>
                                    {{ \Carbon\Carbon::parse($hoSo->ngay_hen)->format('d/m/Y') }} 
                                    lúc {{ $hoSo->gio_hen }}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Trạng thái:</strong><br>
                                    <span class="badge bg-info">{{ $hoSo->trang_thai }}</span>
                                </div>
                            </div>
                        </div>

                        @if($hoSo->ghi_chu)
                            <div class="alert alert-warning text-start mb-4">
                                <strong>Ghi chú:</strong><br>
                                {{ $hoSo->ghi_chu }}
                            </div>
                        @endif

                        <div class="alert alert-warning text-start">
                            <h6><i class="fas fa-bell me-2"></i>Lưu ý quan trọng:</h6>
                            <ul class="mb-0">
                                @if($hoSo->so_thu_tu)
                                <li><strong>Số thứ tự của bạn là: {{ $hoSo->so_thu_tu }}</strong> - Vui lòng đến đúng số thứ tự này</li>
                                @endif
                                <li>Vui lòng đến đúng giờ hẹn tại phường/đơn vị đã chọn</li>
                                <li>Mang theo đầy đủ giấy tờ đã upload và CCCD/CMND gốc</li>
                                <li>Bạn sẽ nhận được thông báo nhắc lịch trước 1 ngày</li>
                                <li>Bạn có thể tra cứu trạng thái hồ sơ bằng mã hồ sơ hoặc CCCD</li>
                                <li>Lưu mã hồ sơ để tra cứu sau này</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="{{ route('info.index') }}?action=tab2" class="btn btn-primary btn-lg">
                                <i class="fas fa-list me-2"></i>Xem lịch hẹn của tôi
                            </a>
                            <a href="{{ route('booking.select-phuong') }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Đặt lịch mới
                            </a>
                            <a href="{{ route('index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-home me-2"></i>Về trang chủ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

