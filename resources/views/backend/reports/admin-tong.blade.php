@extends('backend.components.layout')
@section('title', 'Báo Cáo Tổng Hợp - Admin Tổng')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Báo Cáo Tổng Hợp - Admin Tổng</h5>
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
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-2"></i>Lọc
                            </button>
                            <a href="{{ route('reports.export-excel', request()->all()) }}" class="btn btn-success">
                                <i class="fas fa-file-excel me-2"></i>Xuất Excel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-gradient-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Tổng số hồ sơ</h6>
                            <h3 class="mb-0">{{ number_format($tongHoSo) }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-gradient-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Tổng số phường</h6>
                            <h3 class="mb-0">{{ $donVis->count() }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-gradient-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Tổng số dịch vụ</h6>
                            <h3 class="mb-0">{{ $hoSoTheoDichVu->count() }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-concierge-bell fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-gradient-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Tổng số cán bộ</h6>
                            <h3 class="mb-0">{{ $soNguoiLamTheoPhuong->sum('so_luong') }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-gradient-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Tổng đánh giá</h6>
                            <h3 class="mb-0">{{ number_format($thongKeDanhGia->so_luong ?? 0) }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-gradient-secondary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Điểm TB</h6>
                            <h3 class="mb-0">{{ number_format($thongKeDanhGia->diem_tb ?? 0, 1) }}/5</h3>
                        </div>
                        <div>
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng các phường -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Báo Cáo Theo Phường</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Phường/Đơn vị</th>
                                    <th>Số hồ sơ</th>
                                    <th>Tổng phí (VNĐ)</th>
                                    <th>Số cán bộ</th>
                                    <th>Số dịch vụ</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hoSoTheoPhuong as $index => $item)
                                @php
                                    $tongTienItem = $tongTienTheoPhuong->where('don_vi_id', $item->don_vi_id)->first();
                                    $tongTien = $tongTienItem ? $tongTienItem->tong_tien : 0;
                                    $soCanBoItem = $soNguoiLamTheoPhuong->where('don_vi_id', $item->don_vi_id)->first();
                                    $soCanBo = $soCanBoItem ? $soCanBoItem->so_luong : 0;
                                    $soDichVu = $chiTietDichVuTheoPhuong->where('don_vi_id', $item->don_vi_id)->count();
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->donVi->ten_don_vi ?? 'N/A' }}</strong>
                                    </td>
                                    <td><span class="badge bg-primary">{{ number_format($item->so_luong) }}</span></td>
                                    <td><span class="badge bg-success">{{ number_format($tongTien) }}</span></td>
                                    <td><span class="badge bg-info">{{ $soCanBo }}</span></td>
                                    <td><span class="badge bg-warning">{{ $soDichVu }}</span></td>
                                    <td>
                                        <a href="{{ route('reports.phuong.detail', ['donViId' => $item->don_vi_id, 'tu_ngay' => $tuNgay, 'den_ngay' => $denNgay]) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye me-1"></i>Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chi tiết dịch vụ theo phường -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Chi Tiết Dịch Vụ Theo Phường</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Phường</th>
                                    <th>Dịch vụ</th>
                                    <th>Số lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($chiTietDichVuTheoPhuong as $item)
                                <tr>
                                    <td>{{ $item->donVi->ten_don_vi ?? 'N/A' }}</td>
                                    <td>{{ $item->dichVu->ten_dich_vu ?? 'N/A' }}</td>
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

    <!-- Tổng phí theo phường -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Tổng Phí Theo Phường</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Phường/Đơn vị</th>
                                    <th>Tổng phí (VNĐ)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tongTienTheoPhuong as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $item->donVi->ten_don_vi ?? 'N/A' }}</strong></td>
                                    <td><span class="badge bg-success fs-6">{{ number_format($item->tong_tien ?? 0) }}</span></td>
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

    <!-- Bảng xếp hạng cán bộ với đánh giá chi tiết -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Bảng Xếp Hạng Cán Bộ (Top 10)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Cán bộ</th>
                                    <th class="text-center">Số hồ sơ</th>
                                    <th class="text-center">Điểm TB</th>
                                    <th class="text-center">Tổng đánh giá</th>
                                    <th class="text-center">★★★★★</th>
                                    <th class="text-center">★★★★</th>
                                    <th class="text-center">★★★</th>
                                    <th class="text-center">★★</th>
                                    <th class="text-center">★</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bangXepHangCanBo as $index => $item)
                                <tr>
                                    <td>
                                        @if($index < 3)
                                            <span class="badge bg-warning text-dark">#{{ $index + 1 }}</span>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </td>
                                    <td><strong>{{ $item->quanTriVien->ho_ten ?? 'N/A' }}</strong></td>
                                    <td class="text-center"><span class="badge bg-primary">{{ number_format($item->so_luong) }}</span></td>
                                    <td class="text-center">
                                        @php
                                            $avg = $item->average_rating ?? 0;
                                            $badgeColor = $avg >= 4.5 ? 'success' : ($avg >= 4 ? 'info' : ($avg >= 3 ? 'warning' : 'danger'));
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }} fs-6">
                                            <i class="fas fa-star me-1"></i>{{ number_format($avg, 1) }}/5.0
                                        </span>
                                    </td>
                                    <td class="text-center"><span class="badge bg-info">{{ $item->total_ratings ?? 0 }}</span></td>
                                    <td class="text-center">
                                        @if(($item->five_star ?? 0) > 0)
                                            <span class="badge bg-success">{{ $item->five_star }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(($item->four_star ?? 0) > 0)
                                            <span class="badge bg-info">{{ $item->four_star }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(($item->three_star ?? 0) > 0)
                                            <span class="badge bg-warning">{{ $item->three_star }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(($item->two_star ?? 0) > 0)
                                            <span class="badge bg-orange">{{ $item->two_star }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(($item->one_star ?? 0) > 0)
                                            <span class="badge bg-danger">{{ $item->one_star }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê đánh giá theo nhân viên -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-star me-2 text-warning"></i>Thống Kê Đánh Giá Theo Nhân Viên
                    </h5>
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
                                @forelse($danhGiaNhanVien as $index => $rating)
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
                                            <p class="mb-0">Không có dữ liệu đánh giá trong khoảng thời gian từ {{ \Carbon\Carbon::parse($tuNgay)->format('d/m/Y') }} đến {{ \Carbon\Carbon::parse($denNgay)->format('d/m/Y') }}</p>
                                            <small class="text-muted">Vui lòng chọn khoảng thời gian khác hoặc kiểm tra lại dữ liệu đánh giá</small>
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
    
    /* Gradient backgrounds for cards */
    .bg-gradient-primary {
        background: linear-gradient(45deg, #4e73df, #224abe) !important;
    }
    
    .bg-gradient-success {
        background: linear-gradient(45deg, #1cc88a, #13855c) !important;
    }
    
    .bg-gradient-info {
        background: linear-gradient(45deg, #36b9cc, #258391) !important;
    }
    
    .bg-gradient-warning {
        background: linear-gradient(45deg, #f6c23e, #dda20a) !important;
    }
    
    .bg-gradient-danger {
        background: linear-gradient(45deg, #e74a3b, #be2617) !important;
    }
    
    .bg-gradient-secondary {
        background: linear-gradient(45deg, #858796, #5a5c69) !important;
    }
    
    /* Card hover effect */
    .card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
</style>
@endsection

