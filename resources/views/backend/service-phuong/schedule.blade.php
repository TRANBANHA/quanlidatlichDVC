@extends('backend.components.layout')
@section('title')
    Quản lý lịch dịch vụ và phân công cán bộ
@endsection
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Quản lý lịch dịch vụ và phân công cán bộ</h3>
            <a href="{{ route('service-phuong.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
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

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($servicePhuongs->isEmpty())
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Bạn chưa có dịch vụ nào được kích hoạt. Vui lòng kích hoạt dịch vụ trước khi quản lý lịch.
            </div>
        @else
            <!-- Form thêm lịch mới -->
            @if(Auth::guard('admin')->user()->isAdminPhuong() || Auth::guard('admin')->user()->isCanBo())
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Thêm lịch dịch vụ và phân công cán bộ</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Mỗi dịch vụ chỉ có thể có tối đa <strong>2 thứ</strong> trong tuần. Thường thì mỗi dịch vụ chỉ có <strong>1 thứ</strong>.
                    </div>
                    <form action="{{ route('service-phuong.schedule.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="dich_vu_id" class="form-label">Dịch vụ <span class="text-danger">*</span></label>
                                <select name="dich_vu_id" id="dich_vu_id" class="form-select" required>
                                    <option value="">-- Chọn dịch vụ --</option>
                                    @foreach($servicePhuongs as $servicePhuong)
                                        @php
                                            $scheduleCount = isset($schedulesByService[$servicePhuong->dich_vu_id]) ? $schedulesByService[$servicePhuong->dich_vu_id]->count() : 0;
                                        @endphp
                                        <option value="{{ $servicePhuong->dich_vu_id }}" {{ $scheduleCount >= 2 ? 'disabled' : '' }}>
                                            {{ $servicePhuong->dichVu->ten_dich_vu ?? 'N/A' }}
                                            @if($scheduleCount >= 2)
                                                (Đã đủ 2 thứ)
                                            @elseif($scheduleCount > 0)
                                                (Đã có {{ $scheduleCount }} thứ)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Dịch vụ đã có đủ 2 thứ sẽ không thể chọn</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="thu_trong_tuan" class="form-label">Thứ trong tuần <span class="text-danger">*</span></label>
                                <select name="thu_trong_tuan" id="thu_trong_tuan" class="form-select" required>
                                    <option value="">-- Chọn thứ --</option>
                                    <option value="1">Thứ 2</option>
                                    <option value="2">Thứ 3</option>
                                    <option value="3">Thứ 4</option>
                                    <option value="4">Thứ 5</option>
                                    <option value="5">Thứ 6</option>
                                    <option value="6">Thứ 7</option>
                                    <option value="7">Chủ nhật</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="gio_bat_dau" class="form-label">Giờ bắt đầu <span class="text-danger">*</span></label>
                                <input type="time" name="gio_bat_dau" id="gio_bat_dau" class="form-control" value="08:00" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="gio_ket_thuc" class="form-label">Giờ kết thúc <span class="text-danger">*</span></label>
                                <input type="time" name="gio_ket_thuc" id="gio_ket_thuc" class="form-control" value="17:00" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="so_luong_toi_da" class="form-label">Số lượng tối đa <span class="text-danger">*</span></label>
                                <input type="number" name="so_luong_toi_da" id="so_luong_toi_da" class="form-control" value="10" min="1" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ma_can_bo" class="form-label">Phân công cán bộ</label>
                            @if($canBoList->isEmpty())
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Chưa có cán bộ nào trong phường.
                                </div>
                            @else
                                <select name="ma_can_bo[]" id="ma_can_bo" class="form-select" multiple size="5">
                                    @foreach($canBoList as $canBo)
                                        <option value="{{ $canBo->id }}">{{ $canBo->ho_ten }} ({{ $canBo->ten_dang_nhap }})</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Giữ Ctrl (hoặc Cmd trên Mac) để chọn nhiều cán bộ</small>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="ghi_chu" class="form-label">Ghi chú</label>
                            <textarea name="ghi_chu" id="ghi_chu" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Lưu lịch và phân công
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Danh sách lịch hiện có -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Danh sách lịch dịch vụ</h5>
                </div>
                <div class="card-body">
                    @foreach($servicePhuongs as $servicePhuong)
                        @php
                            $schedules = $schedulesByService[$servicePhuong->dich_vu_id] ?? collect();
                        @endphp
                        @if($schedules->isNotEmpty())
                            <div class="mb-4">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-briefcase me-2"></i>{{ $servicePhuong->dichVu->ten_dich_vu ?? 'N/A' }}
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Thứ</th>
                                                <th>Giờ làm việc</th>
                                                <th>Số lượng tối đa</th>
                                                <th>Cán bộ được phân công</th>
                                                <th>Ghi chú</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($schedules as $schedule)
                                                @php
                                                    $assignedStaffIds = $assignmentsBySchedule[$schedule->id] ?? [];
                                                    $assignedStaff = $canBoList->whereIn('id', $assignedStaffIds);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        @php
                                                            $dayNames = [1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7', 7 => 'Chủ nhật'];
                                                        @endphp
                                                        <span class="badge bg-primary">{{ $dayNames[$schedule->thu_trong_tuan] ?? 'N/A' }}</span>
                                                    </td>
                                                    <td>{{ $schedule->gio_bat_dau }} - {{ $schedule->gio_ket_thuc }}</td>
                                                    <td><span class="badge bg-success">{{ $schedule->so_luong_toi_da }} hồ sơ</span></td>
                                                    <td>
                                                        @if($assignedStaff->isNotEmpty())
                                                            @foreach($assignedStaff as $staff)
                                                                <span class="badge bg-info me-1">{{ $staff->ho_ten }}</span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">Chưa phân công</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $schedule->ghi_chu ?? '-' }}</td>
                                                    <td>
                                                        @if(Auth::guard('admin')->user()->isAdminPhuong() || Auth::guard('admin')->user()->isCanBo())
                                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $schedule->id }}">
                                                                <i class="fas fa-edit"></i> Sửa
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal sửa lịch -->
@if(Auth::guard('admin')->user()->isAdminPhuong() || Auth::guard('admin')->user()->isCanBo())
@foreach($servicePhuongs as $servicePhuong)
    @php
        $schedules = $schedulesByService[$servicePhuong->dich_vu_id] ?? collect();
    @endphp
    @foreach($schedules as $schedule)
        @php
            $assignedStaffIds = $assignmentsBySchedule[$schedule->id] ?? [];
        @endphp
        <div class="modal fade" id="editModal{{ $schedule->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">Sửa lịch dịch vụ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('service-phuong.schedule.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="dich_vu_id" value="{{ $schedule->dich_vu_id }}">
                            <input type="hidden" name="thu_trong_tuan" value="{{ $schedule->thu_trong_tuan }}">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Giờ bắt đầu <span class="text-danger">*</span></label>
                                    <input type="time" name="gio_bat_dau" class="form-control" value="{{ $schedule->gio_bat_dau }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Giờ kết thúc <span class="text-danger">*</span></label>
                                    <input type="time" name="gio_ket_thuc" class="form-control" value="{{ $schedule->gio_ket_thuc }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Số lượng tối đa <span class="text-danger">*</span></label>
                                <input type="number" name="so_luong_toi_da" class="form-control" value="{{ $schedule->so_luong_toi_da }}" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phân công cán bộ</label>
                                <select name="ma_can_bo[]" class="form-select" multiple size="5">
                                    @foreach($canBoList as $canBo)
                                        <option value="{{ $canBo->id }}" {{ in_array($canBo->id, $assignedStaffIds) ? 'selected' : '' }}>
                                            {{ $canBo->ho_ten }} ({{ $canBo->ten_dang_nhap }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Giữ Ctrl (hoặc Cmd trên Mac) để chọn nhiều cán bộ</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ghi chú</label>
                                <textarea name="ghi_chu" class="form-control" rows="2">{{ $schedule->ghi_chu }}</textarea>
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
@endforeach
@endif

@endsection

