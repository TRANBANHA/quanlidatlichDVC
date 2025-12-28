@extends('backend.components.layout')
@section('title', 'Báo Cáo Đánh Giá Nhân Viên')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="page-header mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="fw-bold mb-2 text-primary"><i class="fas fa-star me-2"></i>Báo Cáo Đánh Giá Nhân Viên</h2>
                <p class="text-muted mb-0">Thống kê và phân tích đánh giá của người dùng về chất lượng phục vụ</p>
            </div>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    @php
        $totalStaff = $staffRatings->count();
        $totalRatings = $staffRatings->sum('total_ratings');
        $avgRating = $staffRatings->avg('average_rating');
        $excellentCount = $staffRatings->where('average_rating', '>=', 4.5)->count();
    @endphp
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-users text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Tổng Nhân Viên</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalStaff }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-star text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Tổng Đánh Giá</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalRatings }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-chart-line text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Điểm TB Chung</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($avgRating ?? 0, 1) }}/5.0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-trophy text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Xuất Sắc (≥4.5)</h6>
                            <h3 class="mb-0 fw-bold">{{ $excellentCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng thống kê -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-table me-2 text-primary"></i>Thống Kê Đánh Giá Theo Nhân Viên</h5>
                    </div>
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
                                    <th class="text-center"><i class="fas fa-cog me-2 text-secondary"></i>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffRatings as $index => $rating)
                                <tr class="align-middle">
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-primary"></i>
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
                                    <td class="text-center">
                                        <a href="{{ route('admin.reports.staff-rating.show', $rating->id) }}" class="btn btn-sm btn-primary" title="Xem chi tiết đánh giá">
                                            <i class="fas fa-eye me-1"></i>Chi tiết
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                            <p class="mb-0">Không có dữ liệu đánh giá</p>
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

    <!-- Biểu đồ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-chart-bar me-2 text-primary"></i>Biểu Đồ Đánh Giá</h5>
                </div>
                <div class="card-body">
                    <canvas id="ratingChart" style="max-height: 400px;"></canvas>
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
    .bg-orange {
        background-color: #fd7e14 !important;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Lấy dữ liệu cho biểu đồ
    fetch('{{ route("admin.reports.staff-rating.chart") }}')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('ratingChart').getContext('2d');
            
            // Tạo gradient cho mỗi bar
            const gradients = data.map((item, index) => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                const rating = parseFloat(item.average_rating || 0);
                if (rating >= 4.5) {
                    gradient.addColorStop(0, 'rgba(40, 167, 69, 0.8)');
                    gradient.addColorStop(1, 'rgba(40, 167, 69, 0.4)');
                } else if (rating >= 4) {
                    gradient.addColorStop(0, 'rgba(23, 162, 184, 0.8)');
                    gradient.addColorStop(1, 'rgba(23, 162, 184, 0.4)');
                } else if (rating >= 3) {
                    gradient.addColorStop(0, 'rgba(255, 193, 7, 0.8)');
                    gradient.addColorStop(1, 'rgba(255, 193, 7, 0.4)');
                } else {
                    gradient.addColorStop(0, 'rgba(220, 53, 69, 0.8)');
                    gradient.addColorStop(1, 'rgba(220, 53, 69, 0.4)');
                }
                return gradient;
            });
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.ten),
                    datasets: [{
                        label: 'Điểm Trung Bình',
                        data: data.map(item => parseFloat(item.average_rating || 0)),
                        backgroundColor: gradients,
                        borderColor: data.map(item => {
                            const rating = parseFloat(item.average_rating || 0);
                            if (rating >= 4.5) return 'rgba(40, 167, 69, 1)';
                            if (rating >= 4) return 'rgba(23, 162, 184, 1)';
                            if (rating >= 3) return 'rgba(255, 193, 7, 1)';
                            return 'rgba(220, 53, 69, 1)';
                        }),
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
                                    return 'Điểm: ' + context.parsed.y.toFixed(1) + '/5.0';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: 12
                                },
                                callback: function(value) {
                                    return value + '.0';
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
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
            document.getElementById('ratingChart').parentElement.innerHTML = 
                '<div class="alert alert-danger text-center"><i class="fas fa-exclamation-triangle me-2"></i>Không thể tải dữ liệu biểu đồ</div>';
        });
</script>
@endpush
@endsection

