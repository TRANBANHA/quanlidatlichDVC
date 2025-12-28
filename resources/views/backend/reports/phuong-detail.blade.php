@extends('backend.components.layout')
@section('title', 'Chi Tiết Báo Cáo - ' . $donVi->ten_don_vi)

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="page-header mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-lg bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="fas fa-building text-primary fs-2"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1 text-primary">
                            Chi Tiết Báo Cáo - {{ $donVi->ten_don_vi }}
                        </h2>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar-alt me-2 text-info"></i>
                            <span class="badge bg-info rounded-pill px-3 py-2">
                                Từ {{ \Carbon\Carbon::parse($tuNgay)->format('d/m/Y') }} đến {{ \Carbon\Carbon::parse($denNgay)->format('d/m/Y') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('reports.index', ['tu_ngay' => $tuNgay, 'den_ngay' => $denNgay]) }}" class="btn btn-outline-primary btn-lg fw-bold">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('reports.phuong.detail', $donVi->id) }}" class="row g-3 align-items-end">
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
                                <i class="fas fa-file-alt text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Tổng số hồ sơ</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($tongHoSo) }}</h3>
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
                            <h3 class="mb-0 fw-bold">{{ number_format($tongTien) }}</h3>
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
                            <h6 class="text-muted mb-1">Số cán bộ</h6>
                            <h3 class="mb-0 fw-bold">{{ $danhSachCanBo->count() }}</h3>
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
                                <i class="fas fa-star text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Đánh giá TB</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($thongKeDanhGia->diem_tb, 1) }}/5.0</h3>
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

        <!-- Chart: Hồ sơ theo trạng thái -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-chart-bar me-2 text-primary"></i>Hồ Sơ Theo Trạng Thái</h5>
                </div>
                <div class="card-body">
                    <canvas id="trangThaiChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Xu hướng theo ngày -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2 text-primary"></i>Xu Hướng Theo Ngày</h5>
                </div>
                <div class="card-body">
                    <canvas id="xuHuongChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng: Hồ sơ theo dịch vụ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-table me-2 text-primary"></i>Chi Tiết Hồ Sơ Theo Dịch Vụ</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 60px;">STT</th>
                                    <th><i class="fas fa-list me-2 text-primary"></i>Dịch vụ</th>
                                    <th class="text-center"><i class="fas fa-file-alt me-2 text-info"></i>Số lượng hồ sơ</th>
                                    <th class="text-end"><i class="fas fa-money-bill-wave me-2 text-success"></i>Tổng phí (VNĐ)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hoSoTheoDichVu as $index => $item)
                                @php
                                    $tongTienItem = $tongTienTheoDichVu->where('dich_vu_id', $item->dich_vu_id)->first();
                                    $tongTienValue = $tongTienItem ? $tongTienItem->tong_tien : 0;
                                @endphp
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
                                        <span class="badge bg-success rounded-pill px-3 py-2">{{ number_format($tongTienValue) }}</span>
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

    <!-- Bảng: Hồ sơ theo cán bộ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-tie me-2 text-primary"></i>Top 10 Cán Bộ Xử Lý Nhiều Nhất</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 60px;">STT</th>
                                    <th><i class="fas fa-user me-2 text-primary"></i>Cán bộ</th>
                                    <th class="text-center"><i class="fas fa-file-alt me-2 text-info"></i>Số lượng hồ sơ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hoSoTheoCanBo as $index => $item)
                                <tr class="align-middle">
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-info"></i>
                                            </div>
                                            <strong>{{ $item->quanTriVien->ho_ten ?? $item->quanTriVien->ten_dang_nhap ?? 'N/A' }}</strong>
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

    <!-- Bảng: Danh sách cán bộ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-users me-2 text-primary"></i>Danh Sách Cán Bộ</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 60px;">STT</th>
                                    <th><i class="fas fa-user me-2 text-primary"></i>Tên cán bộ</th>
                                    <th><i class="fas fa-user-circle me-2 text-info"></i>Tên đăng nhập</th>
                                    <th class="text-center"><i class="fas fa-file-alt me-2 text-success"></i>Số hồ sơ đã xử lý</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($danhSachCanBo as $index => $canBo)
                                <tr class="align-middle">
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <strong>{{ $canBo->ho_ten ?? 'N/A' }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $canBo->ten_dang_nhap }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success rounded-pill px-3 py-2">{{ $canBo->ho_so_count ?? 0 }}</span>
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

    <!-- Bảng: Đánh giá nhân viên -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-star me-2 text-warning"></i>Đánh Giá Nhân Viên</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 60px;">STT</th>
                                    <th><i class="fas fa-user me-2 text-primary"></i>Tên Nhân Viên</th>
                                    <th class="text-center"><i class="fas fa-list me-2 text-info"></i>Tổng Đánh Giá</th>
                                    <th class="text-center"><i class="fas fa-star me-2 text-warning"></i>Điểm TB</th>
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
                                @forelse($danhGiaNhanVien as $index => $rating)
                                <tr class="align-middle">
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-warning"></i>
                                            </div>
                                            <strong>{{ $rating->ho_ten ?? 'N/A' }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info rounded-pill px-3 py-2">{{ $rating->total_ratings ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $avg = $rating->average_rating ?? 0;
                                            $badgeColor = $avg >= 4.5 ? 'success' : ($avg >= 4 ? 'info' : ($avg >= 3 ? 'warning' : 'danger'));
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }} rounded-pill px-3 py-2 fs-6">
                                            <i class="fas fa-star me-1"></i>{{ number_format($avg, 1) }}/5.0
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if(($rating->five_star ?? 0) > 0)
                                            <span class="badge bg-success rounded-pill px-3 py-2">{{ $rating->five_star }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(($rating->four_star ?? 0) > 0)
                                            <span class="badge bg-info rounded-pill px-3 py-2">{{ $rating->four_star }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(($rating->three_star ?? 0) > 0)
                                            <span class="badge bg-warning rounded-pill px-3 py-2">{{ $rating->three_star }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(($rating->two_star ?? 0) > 0)
                                            <span class="badge bg-orange rounded-pill px-3 py-2">{{ $rating->two_star }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(($rating->one_star ?? 0) > 0)
                                            <span class="badge bg-danger rounded-pill px-3 py-2">{{ $rating->one_star }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-star fa-3x mb-3 d-block text-warning"></i>
                                            <p class="mb-0">Chưa có đánh giá nào</p>
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
        border-left-color: #1cc88a;
    }
    .stats-card:nth-child(3) {
        border-left-color: #36b9cc;
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
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .page-header {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
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
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Chart: Hồ sơ theo dịch vụ (Pie Chart)
    const dichVuCtx = document.getElementById('dichVuChart').getContext('2d');
    const dichVuData = {!! json_encode($hoSoTheoDichVu->pluck('so_luong')->toArray()) !!};
    const dichVuLabels = {!! json_encode($hoSoTheoDichVu->pluck('dichVu.ten_dich_vu')->toArray()) !!};
    
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
    
    // Màu sắc theo trạng thái
    const trangThaiColors = {
        'cho_xu_ly': 'rgba(108, 117, 125, 0.8)',
        'dang_xu_ly': 'rgba(23, 162, 184, 0.8)',
        'da_hoan_tat': 'rgba(40, 167, 69, 0.8)',
        'can_bo_sung': 'rgba(255, 193, 7, 0.8)',
        'da_huy': 'rgba(220, 53, 69, 0.8)'
    };
    
    const trangThaiBgColors = Object.keys(trangThaiData).map(key => trangThaiColors[key] || 'rgba(78, 115, 223, 0.8)');

    const trangThaiCtx = document.getElementById('trangThaiChart').getContext('2d');
    new Chart(trangThaiCtx, {
        type: 'bar',
        data: {
            labels: trangThaiLabelsArray,
            datasets: [{
                label: 'Số lượng hồ sơ',
                data: trangThaiValuesArray,
                backgroundColor: trangThaiBgColors,
                borderColor: trangThaiBgColors.map(c => c.replace('0.8', '1')),
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
                        stepSize: 1,
                        font: {
                            size: 12
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
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });

    // Chart: Xu hướng theo ngày (Line Chart)
    const xuHuongLabels = {!! json_encode($xuHuong->pluck('ngay')->map(function($date) {
        return \Carbon\Carbon::parse($date)->format('d/m/Y');
    })->toArray()) !!};
    const xuHuongData = {!! json_encode($xuHuong->pluck('so_luong')->toArray()) !!};

    const xuHuongCtx = document.getElementById('xuHuongChart').getContext('2d');
    
    // Tạo gradient
    const gradient = xuHuongCtx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(78, 115, 223, 0.3)');
    gradient.addColorStop(1, 'rgba(78, 115, 223, 0.05)');
    
    new Chart(xuHuongCtx, {
        type: 'line',
        data: {
            labels: xuHuongLabels,
            datasets: [{
                label: 'Số lượng hồ sơ',
                data: xuHuongData,
                borderColor: 'rgba(78, 115, 223, 1)',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 8,
                pointBackgroundColor: '#fff',
                pointBorderColor: 'rgba(78, 115, 223, 1)',
                pointBorderWidth: 2,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12
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
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 13,
                            weight: 'bold'
                        },
                        padding: 15
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
                            return 'Số lượng: ' + context.parsed.y + ' hồ sơ';
                        }
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });
</script>
@endpush
