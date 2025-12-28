@extends('backend.components.layout')
@section('title', 'Báo Cáo Phường - Admin Phường')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Báo Cáo Phường</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="tu_ngay" class="form-control" value="{{ $tuNgay }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="den_ngay" class="form-control" value="{{ $denNgay }}" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-success">
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
                            <h6 class=" mb-1">Tổng số dịch vụ</h6>
                            <h3 class="mb-0">{{ $dichVuTheoPhuong->count() }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-concierge-bell fa-2x"></i>
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
                            <h6 class=" mb-1">Tổng số nhân viên</h6>
                            <h3 class="mb-0">{{ $danhSachNhanVien->count() }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x"></i>
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
                            <h6 class=" mb-1">Tổng phí</h6>
                            <h3 class="mb-0">{{ number_format($tongPhi ?? 0) }} VNĐ</h3>
                        </div>
                        <div>
                            <i class="fas fa-money-bill-wave fa-2x"></i>
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
                            <h6 class=" mb-1">Tổng hồ sơ</h6>
                            <h3 class="mb-0">{{ number_format($dichVuTheoPhuong->sum('so_luong')) }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-2x"></i>
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

        <!-- Chart: Phí theo dịch vụ -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Phí Theo Dịch Vụ</h5>
                </div>
                <div class="card-body">
                    <canvas id="phiChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Báo cáo dịch vụ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Báo Cáo Dịch Vụ</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Dịch vụ</th>
                                    <th>Số lượng hồ sơ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dichVuTheoPhuong as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $item->dichVu->ten_dich_vu ?? 'N/A' }}</strong></td>
                                    <td><span class="badge bg-primary">{{ number_format($item->so_luong) }}</span></td>
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

    <!-- Báo cáo nhân viên -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Báo Cáo Nhân Viên</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Nhân viên</th>
                                    <th>Số hồ sơ đã xử lý</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($nhanVienTheoPhuong as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $item->quanTriVien->ho_ten ?? 'N/A' }}</strong></td>
                                    <td><span class="badge bg-success">{{ number_format($item->so_luong) }}</span></td>
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

    <!-- Phí theo dịch vụ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Phí Theo Dịch Vụ</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Dịch vụ</th>
                                    <th>Số lượng</th>
                                    <th>Tổng phí (VNĐ)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($phiTheoDichVu as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $item->dichVu->ten_dich_vu ?? 'N/A' }}</strong></td>
                                    <td><span class="badge bg-primary">{{ number_format($item->so_luong) }}</span></td>
                                    <td><span class="badge bg-success fs-6">{{ number_format($item->tong_tien ?? 0) }}</span></td>
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
            labels: {!! json_encode($dichVuTheoPhuong->pluck('dichVu.ten_dich_vu')->toArray()) !!},
            datasets: [{
                data: {!! json_encode($dichVuTheoPhuong->pluck('so_luong')->toArray()) !!},
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

    // Chart: Phí theo dịch vụ (Bar Chart)
    const phiCtx = document.getElementById('phiChart').getContext('2d');
    new Chart(phiCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($phiTheoDichVu->pluck('dichVu.ten_dich_vu')->toArray()) !!},
            datasets: [{
                label: 'Tổng phí (VNĐ)',
                data: {!! json_encode($phiTheoDichVu->pluck('tong_tien')->toArray()) !!},
                backgroundColor: 'rgba(28, 200, 138, 0.8)',
                borderColor: 'rgba(28, 200, 138, 1)',
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
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' VNĐ';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush

