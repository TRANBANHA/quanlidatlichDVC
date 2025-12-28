@extends('backend.components.layout')
@section('title', 'Chi Tiết Báo Cáo - ' . $donVi->ten_don_vi)

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>Chi Tiết Báo Cáo - {{ $donVi->ten_don_vi }}
                    </h5>
                    <a href="{{ route('reports.index', ['tu_ngay' => $tuNgay, 'den_ngay' => $denNgay]) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Quay lại
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.phuong.detail', $donVi->id) }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="tu_ngay" class="form-control" value="{{ $tuNgay }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="den_ngay" class="form-control" value="{{ $denNgay }}" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Lọc
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class=" mb-1">Tổng số hồ sơ</h6>
                            <h3 class="mb-0">{{ number_format($tongHoSo) }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class=" mb-1">Tổng phí (VNĐ)</h6>
                            <h3 class="mb-0">{{ number_format($tongTien) }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class=" mb-1">Số cán bộ</h6>
                            <h3 class="mb-0">{{ $danhSachCanBo->count() }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class=" mb-1">Đánh giá TB</h6>
                            <h3 class="mb-0">{{ number_format($thongKeDanhGia->diem_tb, 1) }}/5</h3>
                        </div>
                        <div>
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Chart: Hồ sơ theo dịch vụ -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Hồ Sơ Theo Dịch Vụ</h5>
                </div>
                <div class="card-body">
                    <canvas id="dichVuChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart: Hồ sơ theo trạng thái -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Hồ Sơ Theo Trạng Thái</h5>
                </div>
                <div class="card-body">
                    <canvas id="trangThaiChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Xu hướng theo ngày -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Xu Hướng Theo Ngày</h5>
                </div>
                <div class="card-body">
                    <canvas id="xuHuongChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng: Hồ sơ theo dịch vụ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Chi Tiết Hồ Sơ Theo Dịch Vụ</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Dịch vụ</th>
                                    <th>Số lượng hồ sơ</th>
                                    <th>Tổng phí (VNĐ)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hoSoTheoDichVu as $index => $item)
                                @php
                                    $tongTienItem = $tongTienTheoDichVu->where('dich_vu_id', $item->dich_vu_id)->first();
                                    $tongTien = $tongTienItem ? $tongTienItem->tong_tien : 0;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $item->dichVu->ten_dich_vu ?? 'N/A' }}</strong></td>
                                    <td><span class="badge bg-primary">{{ number_format($item->so_luong) }}</span></td>
                                    <td><span class="badge bg-success">{{ number_format($tongTien) }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng: Hồ sơ theo cán bộ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Hồ Sơ Theo Cán Bộ</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Cán bộ</th>
                                    <th>Số lượng hồ sơ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hoSoTheoCanBo as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $item->quanTriVien->ho_ten ?? $item->quanTriVien->ten_dang_nhap ?? 'N/A' }}</strong></td>
                                    <td><span class="badge bg-info">{{ number_format($item->so_luong) }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng: Danh sách cán bộ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Danh Sách Cán Bộ</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên cán bộ</th>
                                    <th>Tên đăng nhập</th>
                                    <th>Số hồ sơ đã xử lý</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($danhSachCanBo as $index => $canBo)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $canBo->ho_ten ?? 'N/A' }}</strong></td>
                                    <td>{{ $canBo->ten_dang_nhap }}</td>
                                    <td><span class="badge bg-primary">{{ $canBo->ho_so_count ?? 0 }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Chart: Hồ sơ theo dịch vụ (Pie Chart)
    const dichVuCtx = document.getElementById('dichVuChart').getContext('2d');
    new Chart(dichVuCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($hoSoTheoDichVu->pluck('dichVu.ten_dich_vu')->toArray()) !!},
            datasets: [{
                data: {!! json_encode($hoSoTheoDichVu->pluck('so_luong')->toArray()) !!},
                backgroundColor: [
                    'rgba(78, 115, 223, 0.8)',
                    'rgba(28, 200, 138, 0.8)',
                    'rgba(246, 194, 62, 0.8)',
                    'rgba(231, 74, 59, 0.8)',
                    'rgba(54, 185, 204, 0.8)',
                    'rgba(133, 135, 150, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed + ' hồ sơ';
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Chart: Hồ sơ theo trạng thái (Bar Chart)
    const trangThaiLabels = {
        'cho_xu_ly': 'Chờ xử lý',
        'dang_xu_ly': 'Đang xử lý',
        'da_hoan_tat': 'Đã hoàn tất',
        'can_bo_sung': 'Cần bổ sung',
        'da_huy': 'Đã hủy'
    };
    
    const trangThaiData = {!! json_encode($hoSoTheoTrangThai) !!};
    const trangThaiLabelsArray = Object.keys(trangThaiData).map(key => trangThaiLabels[key] || key);
    const trangThaiValuesArray = Object.values(trangThaiData);

    const trangThaiCtx = document.getElementById('trangThaiChart').getContext('2d');
    new Chart(trangThaiCtx, {
        type: 'bar',
        data: {
            labels: trangThaiLabelsArray,
            datasets: [{
                label: 'Số lượng hồ sơ',
                data: trangThaiValuesArray,
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Chart: Xu hướng theo ngày (Line Chart)
    const xuHuongLabels = {!! json_encode($xuHuong->pluck('ngay')->map(function($date) {
        return \Carbon\Carbon::parse($date)->format('d/m/Y');
    })->toArray()) !!};
    const xuHuongData = {!! json_encode($xuHuong->pluck('so_luong')->toArray()) !!};

    const xuHuongCtx = document.getElementById('xuHuongChart').getContext('2d');
    new Chart(xuHuongCtx, {
        type: 'line',
        data: {
            labels: xuHuongLabels,
            datasets: [{
                label: 'Số lượng hồ sơ',
                data: xuHuongData,
                borderColor: 'rgba(78, 115, 223, 1)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
</script>
@endpush

