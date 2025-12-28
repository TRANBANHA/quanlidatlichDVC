@extends('backend.components.layout')
@section('title', 'Thống Kê - Admin Phường')

@section('content')
<div class=" py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Thống Kê - Admin Phường</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('statistics.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="tu_ngay" class="form-control" value="{{ $tuNgay }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="den_ngay" class="form-control" value="{{ $denNgay }}" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-info text-white">
                                <i class="fas fa-search me-2"></i>Lọc
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Thời gian chờ TB</h6>
                            <h3 class="mb-0">{{ $thoiGianChoTB ? number_format($thoiGianChoTB->tb_ngay, 1) : '0' }} ngày</h3>
                        </div>
                        <div class="icon-bg">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Đánh giá TB</h6>
                            <h3 class="mb-0">{{ $thongKeDanhGia ? number_format($thongKeDanhGia->diem_tb, 1) : '0' }}/5</h3>
                        </div>
                        <div class="icon-bg">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Số lượt đánh giá</h6>
                            <h3 class="mb-0">{{ $thongKeDanhGia ? number_format($thongKeDanhGia->so_luong) : '0' }}</h3>
                        </div>
                        <div class="icon-bg">
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Khung giờ cao điểm</h6>
                            <h3 class="mb-0">{{ $khungGioCaoDiem->first() ? $khungGioCaoDiem->first()->gio . 'h' : 'N/A' }}</h3>
                        </div>
                        <div class="icon-bg">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Số lượt theo dịch vụ -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Số lượt theo dịch vụ</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="dichVuChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Khung giờ cao điểm</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="khungGioChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Phân bố điểm đánh giá -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Phân bố điểm đánh giá</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="phanBoDiemChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Thống kê theo trạng thái</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="trangThaiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Xu hướng -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Xu hướng theo thời gian</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="xuHuongChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-gradient-info {
        background: linear-gradient(45deg, #36b9cc, #258391);
    }
    .icon-bg {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background-color: rgba(255, 255, 255, 0.2);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Chart số lượt theo dịch vụ
    const dichVuCtx = document.getElementById('dichVuChart').getContext('2d');
    new Chart(dichVuCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($thongKeTheoDichVu->pluck('dichVu.ten_dich_vu')->toArray()) !!},
            datasets: [{
                data: {!! json_encode($thongKeTheoDichVu->pluck('so_luong')->toArray()) !!},
                backgroundColor: [
                    'rgba(78, 115, 223, 0.8)',
                    'rgba(28, 200, 138, 0.8)',
                    'rgba(246, 194, 62, 0.8)',
                    'rgba(231, 74, 59, 0.8)',
                    'rgba(54, 185, 204, 0.8)',
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
                }
            }
        }
    });

    // Chart khung giờ cao điểm
    const khungGioCtx = document.getElementById('khungGioChart').getContext('2d');
    new Chart(khungGioCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($khungGioCaoDiem->pluck('gio')->map(function($gio) { return $gio . 'h'; })->toArray()) !!},
            datasets: [{
                label: 'Số lượng',
                data: {!! json_encode($khungGioCaoDiem->pluck('so_luong')->toArray()) !!},
                backgroundColor: 'rgba(246, 194, 62, 0.8)',
                borderColor: 'rgba(246, 194, 62, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Chart phân bố điểm đánh giá
    const phanBoDiemCtx = document.getElementById('phanBoDiemChart').getContext('2d');
    new Chart(phanBoDiemCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($phanBoDiem->pluck('diem')->map(function($diem) { return $diem . ' sao'; })->toArray()) !!},
            datasets: [{
                label: 'Số lượng',
                data: {!! json_encode($phanBoDiem->pluck('so_luong')->toArray()) !!},
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
                    beginAtZero: true
                }
            }
        }
    });

    // Chart thống kê theo trạng thái
    const trangThaiCtx = document.getElementById('trangThaiChart').getContext('2d');
    new Chart(trangThaiCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($thongKeTrangThai->pluck('trang_thai')->toArray()) !!},
            datasets: [{
                data: {!! json_encode($thongKeTrangThai->pluck('so_luong')->toArray()) !!},
                backgroundColor: [
                    'rgba(78, 115, 223, 0.8)',
                    'rgba(28, 200, 138, 0.8)',
                    'rgba(246, 194, 62, 0.8)',
                    'rgba(231, 74, 59, 0.8)',
                    'rgba(133, 135, 150, 0.8)',
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
                }
            }
        }
    });

    // Chart xu hướng
    const xuHuongCtx = document.getElementById('xuHuongChart').getContext('2d');
    new Chart(xuHuongCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($xuHuong->pluck('ngay')->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('d/m/Y');
            })->toArray()) !!},
            datasets: [{
                label: 'Số lượng hồ sơ',
                data: {!! json_encode($xuHuong->pluck('so_luong')->toArray()) !!},
                borderColor: 'rgba(78, 115, 223, 1)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
@endsection

