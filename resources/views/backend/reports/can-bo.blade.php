@extends('backend.components.layout')
@section('title', 'Báo Cáo Cá Nhân - Cán Bộ')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user-chart me-2"></i>Báo Cáo Cá Nhân</h5>
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
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-search me-2"></i>Lọc
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hiệu quả làm việc -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Hiệu Quả Làm Việc</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6>Tổng hồ sơ</h6>
                                    <h3>{{ number_format($thongKeHieuQua['tong_ho_so']) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6>Đã hoàn tất</h6>
                                    <h3>{{ number_format($thongKeHieuQua['da_hoan_tat']) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h6>Đang xử lý</h6>
                                    <h3>{{ number_format($thongKeHieuQua['dang_xu_ly']) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h6>Cần bổ sung</h6>
                                    <h3>{{ number_format($thongKeHieuQua['can_bo_sung']) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Đánh giá tổng quan -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Đánh Giá Từ Người Dùng</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded bg-light">
                                <h6 class="text-muted mb-2">Tổng đánh giá</h6>
                                <h3 class="text-primary mb-0">{{ number_format($thongKeDanhGia['tong_danh_gia']) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded bg-light">
                                <h6 class="text-muted mb-2">Điểm trung bình</h6>
                                <h3 class="text-warning mb-0">
                                    {{ number_format($thongKeDanhGia['diem_trung_binh'], 1) }}/5
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded bg-light">
                                <h6 class="text-muted mb-2">Thái độ phục vụ</h6>
                                <h3 class="text-success mb-0">
                                    {{ number_format($thongKeDanhGia['diem_thai_do_tb'], 1) }}/5
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded bg-light">
                                <h6 class="text-muted mb-2">Thời gian xử lý</h6>
                                <h3 class="text-info mb-0">
                                    {{ number_format($thongKeDanhGia['diem_thoi_gian_tb'], 1) }}/5
                                </h3>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Phân bố sao -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="mb-3"><i class="fas fa-chart-pie me-2"></i>Phân Bố Đánh Giá Theo Sao</h6>
                            <div class="row text-center">
                                <div class="col-md-2 col-4 mb-2">
                                    <div class="p-2 border rounded">
                                        <div class="text-warning mb-1">★★★★★</div>
                                        <h5 class="mb-0 text-success">{{ $thongKeDanhGia['five_star'] ?? 0 }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-2 col-4 mb-2">
                                    <div class="p-2 border rounded">
                                        <div class="text-warning mb-1">★★★★</div>
                                        <h5 class="mb-0 text-info">{{ $thongKeDanhGia['four_star'] ?? 0 }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-2 col-4 mb-2">
                                    <div class="p-2 border rounded">
                                        <div class="text-warning mb-1">★★★</div>
                                        <h5 class="mb-0 text-warning">{{ $thongKeDanhGia['three_star'] ?? 0 }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-2 col-4 mb-2">
                                    <div class="p-2 border rounded">
                                        <div class="text-warning mb-1">★★</div>
                                        <h5 class="mb-0 text-orange">{{ $thongKeDanhGia['two_star'] ?? 0 }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-2 col-4 mb-2">
                                    <div class="p-2 border rounded">
                                        <div class="text-warning mb-1">★</div>
                                        <h5 class="mb-0 text-danger">{{ $thongKeDanhGia['one_star'] ?? 0 }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách đánh giá chi tiết -->
    @if(isset($thongKeDanhGia['chi_tiet']) && $thongKeDanhGia['chi_tiet']->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Danh Sách Đánh Giá Chi Tiết</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Người đánh giá</th>
                                    <th>Dịch vụ</th>
                                    <th class="text-center">Điểm tổng</th>
                                    <th class="text-center">Thái độ</th>
                                    <th class="text-center">Thời gian</th>
                                    <th class="text-center">Chất lượng</th>
                                    <th>Bình luận</th>
                                    <th>Ngày đánh giá</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($thongKeDanhGia['chi_tiet'] as $index => $rating)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $rating->nguoiDung->ten ?? 'N/A' }}</td>
                                    <td>{{ $rating->hoSo->dichVu->ten_dich_vu ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        @php
                                            $diem = $rating->diem ?? 0;
                                            $badgeColor = $diem >= 4.5 ? 'success' : ($diem >= 4 ? 'info' : ($diem >= 3 ? 'warning' : 'danger'));
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }}">{{ number_format($diem, 1) }}/5</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ number_format($rating->diem_thai_do ?? 0, 1) }}/5</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ number_format($rating->diem_thoi_gian ?? 0, 1) }}/5</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ number_format($rating->diem_chat_luong ?? 0, 1) }}/5</span>
                                    </td>
                                    <td>
                                        @if($rating->binh_luan)
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $rating->binh_luan }}">
                                                {{ $rating->binh_luan }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $rating->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Lịch làm việc -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Lịch Làm Việc</h5>
                </div>
                <div class="card-body">
                    @php
                        $thuNames = [1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7', 7 => 'Chủ nhật'];
                    @endphp
                    @if($lichLamViec->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Thứ</th>
                                        <th>Dịch vụ</th>
                                        <th>Giờ làm việc</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lichLamViec as $thu => $schedules)
                                        @foreach($schedules as $scheduleStaff)
                                            @php
                                                $schedule = $scheduleStaff->schedule;
                                                $dichVu = $schedule->service ?? null;
                                            @endphp
                                            <tr>
                                                <td><strong>{{ $thuNames[$thu] ?? "Thứ $thu" }}</strong></td>
                                                <td>{{ $dichVu->ten_dich_vu ?? 'N/A' }}</td>
                                                <td>
                                                    @if($schedule)
                                                        {{ $schedule->gio_bat_dau ?? 'N/A' }} - {{ $schedule->gio_ket_thuc ?? 'N/A' }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Bạn chưa có lịch làm việc được phân công.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Hồ sơ theo ngày -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Hồ Sơ Theo Ngày</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Số lượng hồ sơ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hoSoTheoNgay as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item->ngay)->format('d/m/Y') }}</td>
                                    <td><span class="badge bg-primary">{{ number_format($item->so_luong) }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">Không có dữ liệu</td>
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
    .text-orange {
        color: #fd7e14 !important;
    }
    .bg-orange {
        background-color: #fd7e14 !important;
    }
</style>
@endsection

