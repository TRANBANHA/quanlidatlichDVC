@extends('backend.components.layout')
@section('title')
    Quản lý dịch vụ phường
@endsection
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Quản lý dịch vụ phường</h3>
            <div class="d-flex gap-2">
                @if(Auth::guard('admin')->user()->isAdminPhuong() || Auth::guard('admin')->user()->isCanBo())
                    <a href="{{ route('service-phuong.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Tạo dịch vụ mới
                    </a>
                    <a href="{{ route('service-phuong.schedule') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-alt me-2"></i>Quản lý lịch và phân công cán bộ
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Danh sách dịch vụ đã sao chép -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Dịch vụ đã kích hoạt cho phường</h5>
            </div>
            <div class="card-body">
                @if($servicePhuongs->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Chưa có dịch vụ nào được kích hoạt. Vui lòng sao chép dịch vụ từ danh sách bên dưới.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Tên dịch vụ</th>
                                    <th>Thời gian xử lý</th>
                                    <th>Số lượng tối đa</th>
                                    <th>Phí dịch vụ</th>
                                    <th>Lịch làm việc</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($servicePhuongs as $servicePhuong)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $servicePhuong->dichVu->ten_dich_vu ?? 'N/A' }}</strong>
                                            @if($servicePhuong->dichVu->mo_ta)
                                                <br><small class="text-muted">{{ mb_substr($servicePhuong->dichVu->mo_ta, 0, 50) }}{{ mb_strlen($servicePhuong->dichVu->mo_ta) > 50 ? '...' : '' }}</small>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-info">{{ $servicePhuong->thoi_gian_xu_ly }} ngày</span></td>
                                        <td><span class="badge bg-success">{{ $servicePhuong->so_luong_toi_da }} hồ sơ</span></td>
                                        <td><span class="badge bg-warning">{{ number_format($servicePhuong->phi_dich_vu, 0, ',', '.') }} VNĐ</span></td>
                                        <td>
                                            @php
                                                $schedules = $schedulesByService[$servicePhuong->dich_vu_id] ?? collect();
                                                $dayNames = [1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7', 7 => 'Chủ nhật'];
                                            @endphp
                                            @if($schedules->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($schedules as $schedule)
                                                        <span class="badge bg-primary" title="Giờ làm việc: {{ $schedule->gio_bat_dau }} - {{ $schedule->gio_ket_thuc }}">
                                                            {{ $dayNames[$schedule->thu_trong_tuan] ?? 'N/A' }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                                @if($schedules->count() >= 2)
                                                    <small class="text-muted d-block mt-1">(Đã đủ 2 thứ)</small>
                                                @endif
                                            @else
                                                <span class="text-muted">Chưa có lịch</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($servicePhuong->kich_hoat)
                                                <span class="badge bg-success">Đang hoạt động</span>
                                            @else
                                                <span class="badge bg-secondary">Tạm dừng</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(Auth::guard('admin')->user()->isAdminPhuong() || Auth::guard('admin')->user()->isCanBo())
                                                <a href="{{ route('service-phuong.edit', $servicePhuong->dich_vu_id) }}" class="btn btn-sm btn-info" title="Cấu hình dịch vụ và form">
                                                    <i class="fas fa-cog"></i> Cấu hình
                                                </a>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $servicePhuong->id }}">
                                                    <i class="fas fa-edit"></i> Sửa
                                                </button>
                                                <form action="{{ route('service-phuong.destroy', $servicePhuong->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa dịch vụ này khỏi phường?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Danh sách dịch vụ tổng để sao chép -->
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-copy me-2"></i>Danh sách dịch vụ tổng (Sao chép về phường)</h5>
            </div>
            <div class="card-body">
                @if($allServices->isEmpty())
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Chưa có dịch vụ nào trong hệ thống.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Tên dịch vụ</th>
                                    <th>Mô tả</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allServices as $service)
                                    @php
                                        $isCopied = isset($servicePhuongs[$service->id]);
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $service->ten_dich_vu }}</strong></td>
                                        <td>{{ mb_substr($service->mo_ta ?? '', 0, 100) }}{{ mb_strlen($service->mo_ta ?? '') > 100 ? '...' : '' }}</td>
                                        <td>
                                            @if($isCopied)
                                                <span class="badge bg-success">Đã sao chép</span>
                                            @else
                                                <span class="badge bg-secondary">Chưa sao chép</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isCopied)
                                                <span class="text-muted">Đã có trong phường</span>
                                            @else
                                                @if(Auth::guard('admin')->user()->isAdminPhuong() || Auth::guard('admin')->user()->isCanBo())
                                                    <form action="{{ route('service-phuong.copy', $service->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-copy me-1"></i>Sao chép
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal sửa dịch vụ phường -->
@foreach($servicePhuongs as $servicePhuong)
    <div class="modal fade" id="editModal{{ $servicePhuong->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Sửa cấu hình dịch vụ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('service-phuong.update', $servicePhuong->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Thời gian xử lý (ngày) <span class="text-danger">*</span></label>
                            <input type="number" name="thoi_gian_xu_ly" class="form-control" value="{{ $servicePhuong->thoi_gian_xu_ly }}" min="1" max="365" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số lượng tối đa (hồ sơ/ngày) <span class="text-danger">*</span></label>
                            <input type="number" name="so_luong_toi_da" class="form-control" value="{{ $servicePhuong->so_luong_toi_da }}" min="1" max="1000" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phí dịch vụ (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="phi_dich_vu" class="form-control" value="{{ $servicePhuong->phi_dich_vu }}" min="0" step="1000" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="kich_hoat" class="form-check-input" id="kich_hoat{{ $servicePhuong->id }}" {{ $servicePhuong->kich_hoat ? 'checked' : '' }}>
                                <label class="form-check-label" for="kich_hoat{{ $servicePhuong->id }}">Kích hoạt dịch vụ</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="ghi_chu" class="form-control" rows="3">{{ $servicePhuong->ghi_chu }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection

