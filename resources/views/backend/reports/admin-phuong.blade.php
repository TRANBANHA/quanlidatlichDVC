@extends('backend.components.layout')
@section('title', 'Báo Cáo Phường - Admin Phường')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="page-header mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-lg bg-success bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="fas fa-chart-bar text-success fs-2"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1 text-success">Báo Cáo Phường</h2>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar-alt me-2 text-info"></i>
                            <span class="badge bg-info rounded-pill px-3 py-2">
                                Từ {{ \Carbon\Carbon::parse($tuNgay)->format('d/m/Y') }} đến {{ \Carbon\Carbon::parse($denNgay)->format('d/m/Y') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-success">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-white mb-2">
                                <i class="fas fa-calendar-alt me-2"></i>Từ ngày
                            </label>
                            <input type="date" name="tu_ngay" class="form-control form-control-lg" value="{{ $tuNgay }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-white mb-2">
                                <i class="fas fa-calendar-check me-2"></i>Đến ngày
                            </label>
                            <input type="date" name="den_ngay" class="form-control form-control-lg" value="{{ $denNgay }}" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-light btn-lg w-100 fw-bold">
                                <i class="fas fa-filter me-2"></i>Lọc dữ liệu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-briefcase text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Tổng số dịch vụ</h6>
                            <h3 class="mb-0 fw-bold">{{ $dichVuTheoPhuong->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-users text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Tổng số nhân viên</h6>
                            <h3 class="mb-0 fw-bold">{{ $danhSachNhanVien->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-money-bill-wave text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Tổng phí (VNĐ)</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($tongPhi ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-file-alt text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Tổng hồ sơ</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($dichVuTheoPhuong->sum('so_luong')) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Chart: Hồ sơ theo dịch vụ -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-chart-pie me-2 text-primary"></i>Hồ Sơ Theo Dịch Vụ</h5>
                </div>
                <div class="card-body">
                    <canvas id="dichVuChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart: Phí theo dịch vụ -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-chart-bar me-2 text-primary"></i>Phí Theo Dịch Vụ</h5>
                </div>
                <div class="card-body">
                    <canvas id="phiChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Báo cáo dịch vụ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i>Báo Cáo Dịch Vụ</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 60px;">STT</th>
                                    <th><i class="fas fa-briefcase me-2 text-primary"></i>Dịch vụ</th>
                                    <th class="text-center"><i class="fas fa-file-alt me-2 text-info"></i>Số lượng hồ sơ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dichVuTheoPhuong as $index => $item)
                                <tr class="align-middle">
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-briefcase text-primary"></i>
                                            </div>
                                            <strong>{{ $item->dichVu->ten_dich_vu ?? 'N/A' }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info rounded-pill px-3 py-2">{{ number_format($item->so_luong) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                            <p class="mb-0">Không có dữ liệu</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Báo cáo nhân viên với đánh giá chi tiết -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-tie me-2 text-primary"></i>Báo Cáo Nhân Viên</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 60px;">STT</th>
                                    <th><i class="fas fa-user me-2 text-primary"></i>Nhân viên</th>
                                    <th class="text-center"><i class="fas fa-file-alt me-2 text-success"></i>Số hồ sơ</th>
                                    <th class="text-center"><i class="fas fa-star me-2 text-warning"></i>Điểm TB</th>
                                    <th class="text-center"><i class="fas fa-list me-2 text-info"></i>Tổng đánh giá</th>
                                    <th class="text-center">
                                        <span class="text-warning">★★★★★</span>
                                    </th>
                                    <th class="text-center">
                                        <span class="text-warning">★★★★</span>
                                    </th>
                                    <th class="text-center">
                                        <span class="text-warning">★★★</span>
                                    </th>
                                    <th class="text-center">
                                        <span class="text-warning">★★</span>
                                    </th>
                                    <th class="text-center">
                                        <span class="text-warning">★</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($nhanVienTheoPhuong as $index => $item)
                                <tr class="align-middle">
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-info"></i>
                                            </div>
                                            <strong>{{ $item->quanTriVien->ho_ten ?? 'N/A' }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success rounded-pill px-3 py-2">{{ number_format($item->so_luong) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $avg = $item->average_rating ?? 0;
                                            $badgeColor = $avg >= 4.5 ? 'success' : ($avg >= 4 ? 'info' : ($avg >= 3 ? 'warning' : 'danger'));
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }} rounded-pill px-3 py-2">
                                            <i class="fas fa-star me-1"></i>{{ number_format($avg, 1) }}/5.0
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info rounded-pill px-3 py-2">{{ $item->total_ratings ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if(($item->five_star ?? 0) > 0)
                                            <span class="badge bg-success rounded-pill px-2 py-1">{{ $item->five_star }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(($item->four_star ?? 0) > 0)
                                            <span class="badge bg-info rounded-pill px-2 py-1">{{ $item->four_star }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(($item->three_star ?? 0) > 0)
                                            <span class="badge bg-warning rounded-pill px-2 py-1">{{ $item->three_star }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(($item->two_star ?? 0) > 0)
                                            <span class="badge bg-orange rounded-pill px-2 py-1">{{ $item->two_star }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(($item->one_star ?? 0) > 0)
                                            <span class="badge bg-danger rounded-pill px-2 py-1">{{ $item->one_star }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                            <p class="mb-0">Không có dữ liệu</p>
                                        </div>
                                    </td>
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
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-money-bill-wave me-2 text-primary"></i>Phí Theo Dịch Vụ</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 60px;">STT</th>
                                    <th><i class="fas fa-briefcase me-2 text-primary"></i>Dịch vụ</th>
                                    <th class="text-center"><i class="fas fa-list me-2 text-info"></i>Số lượng</th>
                                    <th class="text-end"><i class="fas fa-money-bill-wave me-2 text-success"></i>Tổng phí (VNĐ)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($phiTheoDichVu as $index => $item)
                                <tr class="align-middle">
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-briefcase text-primary"></i>
                                            </div>
                                            <strong>{{ $item->dichVu->ten_dich_vu ?? 'N/A' }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info rounded-pill px-3 py-2">{{ number_format($item->so_luong) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-success rounded-pill px-3 py-2 fs-6">{{ number_format($item->tong_tien ?? 0) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                            <p class="mb-0">Không có dữ liệu</p>
                                        </div>
                                    </td>
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

<style>
    .avatar {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-sm {
        width: 40px;
        height: 40px;
    }
    .avatar-lg {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .stats-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-left: 4px solid transparent;
    }
    .stats-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15) !important;
    }
    .stats-card:nth-child(1) {
        border-left-color: #4e73df;
    }
    .stats-card:nth-child(2) {
        border-left-color: #36b9cc;
    }
    .stats-card:nth-child(3) {
        border-left-color: #1cc88a;
    }
    .stats-card:nth-child(4) {
        border-left-color: #f6c23e;
    }
    .table tbody tr {
        transition: all 0.2s ease;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .card {
        transition: box-shadow 0.3s ease, transform 0.2s ease;
    }
    .card:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
        transform: translateY(-2px);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
    }
    .page-header {
        background: linear-gradient(135deg, rgba(28, 200, 138, 0.1) 0%, rgba(23, 166, 115, 0.1) 100%);
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
    }
    .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        font-weight: 600;
    }
    .badge {
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    .bg-orange {
        background-color: #fd7e14 !important;
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Chart: Hồ sơ theo dịch vụ (Pie Chart)
    const dichVuCtx = document.getElementById('dichVuChart').getContext('2d');
    const dichVuData = {!! json_encode($dichVuTheoPhuong->pluck('so_luong')->toArray()) !!};
    const dichVuLabels = {!! json_encode($dichVuTheoPhuong->pluck('dichVu.ten_dich_vu')->toArray()) !!};
    
    new Chart(dichVuCtx, {
        type: 'pie',
        data: {
            labels: dichVuLabels,
            datasets: [{
                data: dichVuData,
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
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            label += context.parsed + ' hồ sơ (' + percentage + '%)';
                            return label;
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1500
            }
        }
    });

    // Chart: Phí theo dịch vụ (Bar Chart)
    const phiCtx = document.getElementById('phiChart').getContext('2d');
    const phiLabels = {!! json_encode($phiTheoDichVu->pluck('dichVu.ten_dich_vu')->toArray()) !!};
    const phiData = {!! json_encode($phiTheoDichVu->pluck('tong_tien')->toArray()) !!};
    
    // Tạo gradient cho mỗi bar
    const phiGradients = phiData.map((value, index) => {
        const gradient = phiCtx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(28, 200, 138, 0.8)');
        gradient.addColorStop(1, 'rgba(28, 200, 138, 0.4)');
        return gradient;
    });
    
    new Chart(phiCtx, {
        type: 'bar',
        data: {
            labels: phiLabels,
            datasets: [{
                label: 'Tổng phí (VNĐ)',
                data: phiData,
                backgroundColor: phiGradients,
                borderColor: 'rgba(28, 200, 138, 1)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 12
                        },
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return 'Tổng phí: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' VNĐ';
                        }
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });
</script>
@endpush

