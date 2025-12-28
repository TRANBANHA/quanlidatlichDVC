@extends('backend.components.layout')
@section('title')
    Chi tiết đơn vị/phường
@endsection
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Chi tiết đơn vị/phường: {{ $donVi->ten_don_vi }}</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="/admin">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li>
                        <a href="{{ route('don-vi.index') }}">Danh sách đơn vị</a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li>Chi tiết</li>
                </ul>
            </div>

            <div class="row mb-4">
                <div class="col">
                    <a href="{{ route('don-vi.edit', $donVi) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                    <a href="{{ route('quantri.index', ['don_vi_id' => $donVi->id]) }}" class="btn btn-info">
                        <i class="fas fa-users"></i> Xem cán bộ
                    </a>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5>Thông tin đơn vị</h5>
                    <table class="table">
                        <tr>
                            <th width="200">Tên đơn vị/phường:</th>
                            <td>{{ $donVi->ten_don_vi }}</td>
                        </tr>
                        <tr>
                            <th>Mô tả:</th>
                            <td>{{ $donVi->mo_ta ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Số cán bộ:</th>
                            <td><span class="badge bg-info">{{ $donVi->admins->count() }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Danh sách dịch vụ và lịch làm việc --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Danh sách dịch vụ và lịch làm việc</h5>
                </div>
                <div class="card-body">
                    @if ($servicePhuongs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="25%">Tên dịch vụ</th>
                                        <th width="20%">Thời gian xử lý</th>
                                        <th width="15%">Số lượng/ngày</th>
                                        <th width="15%">Phí dịch vụ</th>
                                        <th width="20%">Lịch làm việc (Thứ)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($servicePhuongs as $servicePhuong)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $servicePhuong->dichVu->ten_dich_vu ?? 'N/A' }}</strong>
                                                @if($servicePhuong->dichVu->mo_ta)
                                                    <br><small class="text-muted">{{ mb_substr($servicePhuong->dichVu->mo_ta, 0, 50) }}{{ mb_strlen($servicePhuong->dichVu->mo_ta) > 50 ? '...' : '' }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $servicePhuong->thoi_gian_xu_ly }} ngày
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    {{ $servicePhuong->so_luong_toi_da }} hồ sơ
                                                </span>
                                            </td>
                                            <td>
                                                @if($servicePhuong->phi_dich_vu > 0)
                                                    <span class="badge bg-warning text-dark">
                                                        {{ number_format($servicePhuong->phi_dich_vu, 0, ',', '.') }} đ
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">Miễn phí</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $dayNames = [
                                                        1 => 'Thứ 2',
                                                        2 => 'Thứ 3',
                                                        3 => 'Thứ 4',
                                                        4 => 'Thứ 5',
                                                        5 => 'Thứ 6',
                                                        6 => 'Thứ 7',
                                                        7 => 'Chủ nhật',
                                                    ];
                                                @endphp
                                                @if(isset($servicePhuong->schedules) && $servicePhuong->schedules->count() > 0)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($servicePhuong->schedules as $schedule)
                                                            <span class="badge bg-primary">
                                                                {{ $dayNames[$schedule->thu_trong_tuan] ?? 'N/A' }}
                                                                @if($schedule->gio_bat_dau && $schedule->gio_ket_thuc)
                                                                    <br><small>({{ date('H:i', strtotime($schedule->gio_bat_dau)) }} - {{ date('H:i', strtotime($schedule->gio_ket_thuc)) }})</small>
                                                                @endif
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">Chưa có lịch</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> Phường này chưa có dịch vụ nào được kích hoạt.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Danh sách cán bộ thuộc đơn vị</h5>
                </div>
                <div class="card-body">
                    @if ($donVi->admins->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Họ tên</th>
                                    <th>Tên đăng nhập</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Quyền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($donVi->admins as $admin)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $admin->ho_ten }}</td>
                                        <td>{{ $admin->ten_dang_nhap }}</td>
                                        <td>{{ $admin->email ?? '-' }}</td>
                                        <td>{{ $admin->so_dien_thoai ?? '-' }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($admin->quyen == 1) bg-danger
                                                @elseif($admin->quyen == 2) bg-warning text-dark
                                                @else bg-primary
                                                @endif">
                                                {{ \App\Models\Admin::getRoleName($admin->quyen) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">Chưa có cán bộ nào thuộc đơn vị này.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

