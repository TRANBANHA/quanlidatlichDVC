@extends('website.components.layout')

@section('title', 'Chi tiết hồ sơ')

@section('content')
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i>Chi tiết hồ sơ: {{ $hoSo->ma_ho_so }}
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2 mb-3">Thông tin cơ bản</h5>
                        <p><strong>Mã hồ sơ:</strong> <span class="text-primary">{{ $hoSo->ma_ho_so }}</span></p>
                        <p><strong>Dịch vụ:</strong> {{ $hoSo->dichVu->ten_dich_vu }}</p>
                        <p><strong>Phường/Đơn vị:</strong> {{ $hoSo->donVi->ten_don_vi }}</p>
                        <p><strong>Ngày hẹn:</strong> {{ \Carbon\Carbon::parse($hoSo->ngay_hen)->format('d/m/Y') }}</p>
                        <p><strong>Giờ hẹn:</strong> {{ $hoSo->gio_hen }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2 mb-3">Trạng thái</h5>
                        <p>
                            <strong>Trạng thái:</strong> 
                            <span class="badge 
                                @if($hoSo->trang_thai == 'Hoàn tất') bg-success
                                @elseif($hoSo->trang_thai == 'Đang xử lý') bg-warning
                                @elseif($hoSo->trang_thai == 'Cần bổ sung hồ sơ') bg-danger
                                @elseif($hoSo->trang_thai == 'Đã hủy') bg-secondary
                                @else bg-info
                                @endif">
                                {{ $hoSo->trang_thai }}
                            </span>
                        </p>
                        @if($hoSo->quanTriVien)
                            <p><strong>Cán bộ xử lý:</strong> {{ $hoSo->quanTriVien->ho_ten }}</p>
                        @endif
                        <p><strong>Ngày tạo:</strong> {{ $hoSo->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                @if($hoSo->ghi_chu)
                    <div class="mt-4">
                        <h5 class="border-bottom pb-2 mb-3">Ghi chú</h5>
                        <div class="alert alert-info">
                            {!! nl2br(e($hoSo->ghi_chu)) !!}
                        </div>
                    </div>
                @endif

                @if($hoSo->trang_thai == 'Đã hủy' && $hoSo->ly_do_huy)
                    <div class="mt-4">
                        <h5 class="border-bottom pb-2 mb-3">Lý do hủy</h5>
                        <div class="alert alert-warning">
                            {{ $hoSo->ly_do_huy }}
                        </div>
                    </div>
                @endif

                <div class="mt-4 text-center">
                    <a href="{{ route('tracking.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Tra cứu lại
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

