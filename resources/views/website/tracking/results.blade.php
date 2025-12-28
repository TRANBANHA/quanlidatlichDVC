@extends('website.components.layout')

@section('title', 'Kết quả tra cứu')

@section('content')
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="mb-4">
            <a href="{{ route('tracking.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Tra cứu lại
            </a>
        </div>

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-search me-2"></i>Kết quả tra cứu
                    @if($type == 'cccd')
                        <small class="text-white-50">(CCCD: {{ $keyword }})</small>
                    @else
                        <small class="text-white-50">(Mã: {{ $keyword }})</small>
                    @endif
                </h4>
            </div>
            <div class="card-body">
                @if($hoSos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã hồ sơ</th>
                                    <th>Dịch vụ</th>
                                    <th>Phường</th>
                                    <th>Ngày hẹn</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hoSos as $hoSo)
                                    <tr>
                                        <td><strong class="text-primary">{{ $hoSo->ma_ho_so }}</strong></td>
                                        <td>{{ $hoSo->dichVu->ten_dich_vu }}</td>
                                        <td>{{ $hoSo->donVi->ten_don_vi }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($hoSo->ngay_hen)->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">{{ $hoSo->gio_hen }}</small>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($hoSo->trang_thai == 'Hoàn tất') bg-success
                                                @elseif($hoSo->trang_thai == 'Đang xử lý') bg-warning
                                                @elseif($hoSo->trang_thai == 'Cần bổ sung hồ sơ') bg-danger
                                                @elseif($hoSo->trang_thai == 'Đã hủy') bg-secondary
                                                @else bg-info
                                                @endif">
                                                {{ $hoSo->trang_thai }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('tracking.show', $hoSo->ma_ho_so) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i>Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h4>Không tìm thấy hồ sơ</h4>
                        <p>Không tìm thấy hồ sơ nào với thông tin bạn đã nhập.</p>
                        <p class="mb-0">Vui lòng kiểm tra lại mã hồ sơ hoặc số CCCD.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

