@extends('backend.components.layout')

@section('title', 'Chi Tiết Đánh Giá Nhân Viên')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="page-header mb-4">
        <div class="d-flex align-items-center justify-content-between w-100">
            <div class="flex-grow-1">
                <h2 class="fw-bold mb-2 text-primary">
                    <i class="fas fa-user-circle me-2"></i>Chi Tiết Đánh Giá: {{ $staff->ho_ten }}
                </h2>
                <p class="text-muted mb-0">Xem tất cả đánh giá chi tiết của nhân viên này</p>
            </div>
            <div class="flex-shrink-0 ms-3" style="margin-right: 50px;">
            <a href="{{ route('admin.reports.staff-rating') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
            </div>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-star text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Tổng Đánh Giá</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_ratings'] }}</h3>
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
                            <h6 class="text-muted mb-1">Điểm TB Tổng</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['average_rating'], 1) }}/5.0</h3>
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
                                <i class="fas fa-smile text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Thái Độ TB</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['avg_thai_do'], 1) }}/5.0</h3>
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
                                <i class="fas fa-clock text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Thời Gian TB</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['avg_thoi_gian'], 1) }}/5.0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phân bố sao -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-chart-pie me-2 text-primary"></i>Phân Bố Đánh Giá Theo Sao</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 col-4 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <div class="text-warning mb-2 fs-4">★★★★★</div>
                                <h4 class="mb-0 text-success">{{ $stats['five_star'] }}</h4>
                                <small class="text-muted">5 sao</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-4 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <div class="text-warning mb-2 fs-4">★★★★</div>
                                <h4 class="mb-0 text-info">{{ $stats['four_star'] }}</h4>
                                <small class="text-muted">4 sao</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-4 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <div class="text-warning mb-2 fs-4">★★★</div>
                                <h4 class="mb-0 text-warning">{{ $stats['three_star'] }}</h4>
                                <small class="text-muted">3 sao</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-4 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <div class="text-warning mb-2 fs-4">★★</div>
                                <h4 class="mb-0 text-orange">{{ $stats['two_star'] }}</h4>
                                <small class="text-muted">2 sao</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-4 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <div class="text-warning mb-2 fs-4">★</div>
                                <h4 class="mb-0 text-danger">{{ $stats['one_star'] }}</h4>
                                <small class="text-muted">1 sao</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách đánh giá chi tiết -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i>Danh Sách Đánh Giá Chi Tiết</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 60px;">STT</th>
                                    <th>Người đánh giá</th>
                                    <th>Dịch vụ</th>
                                    <th>Phường</th>
                                    <th class="text-center">Điểm tổng</th>
                                    <th class="text-center">Thái độ</th>
                                    <th class="text-center">Thời gian</th>
                                    <th class="text-center">Chất lượng</th>
                                    <th class="text-center">Cơ sở VC</th>
                                    <th>Bình luận</th>
                                    <th>Ngày đánh giá</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ratings as $index => $rating)
                                <tr class="align-middle">
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">{{ ($ratings->currentPage() - 1) * $ratings->perPage() + $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-info"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $rating->nguoiDung->ten ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ $rating->nguoiDung->so_dien_thoai ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $rating->hoSo->dichVu->ten_dich_vu ?? 'N/A' }}</td>
                                    <td>{{ $rating->hoSo->donVi->ten_don_vi ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        @php
                                            $diem = $rating->diem ?? 0;
                                            $badgeColor = $diem >= 4.5 ? 'success' : ($diem >= 4 ? 'info' : ($diem >= 3 ? 'warning' : 'danger'));
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }} rounded-pill px-3 py-2">
                                            <i class="fas fa-star me-1"></i>{{ number_format($diem, 1) }}/5
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill px-2 py-1">{{ number_format($rating->diem_thai_do ?? 0, 1) }}/5</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill px-2 py-1">{{ number_format($rating->diem_thoi_gian ?? 0, 1) }}/5</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill px-2 py-1">{{ number_format($rating->diem_chat_luong ?? 0, 1) }}/5</span>
                                    </td>
                                    <td class="text-center">
                                        @if($rating->diem_co_so_vat_chat)
                                            <span class="badge bg-secondary rounded-pill px-2 py-1">{{ number_format($rating->diem_co_so_vat_chat, 1) }}/5</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rating->binh_luan)
                                            <button type="button" class="btn btn-sm btn-link p-0 text-start" data-bs-toggle="modal" data-bs-target="#commentModal{{ $rating->id }}">
                                                <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $rating->binh_luan }}">
                                                    {{ Str::limit($rating->binh_luan, 30) }}
                                                </span>
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $rating->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                </tr>

                                <!-- Modal xem bình luận -->
                                @if($rating->binh_luan)
                                <div class="modal fade" id="commentModal{{ $rating->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Bình luận đánh giá</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mb-2"><strong>Người đánh giá:</strong> {{ $rating->nguoiDung->ten ?? 'N/A' }}</p>
                                                <p class="mb-2"><strong>Dịch vụ:</strong> {{ $rating->hoSo->dichVu->ten_dich_vu ?? 'N/A' }}</p>
                                                <p class="mb-2"><strong>Ngày:</strong> {{ $rating->created_at->format('d/m/Y H:i') }}</p>
                                                <hr>
                                                <p class="mb-0"><strong>Bình luận:</strong></p>
                                                <p class="text-muted">{{ $rating->binh_luan }}</p>
                                                @if($rating->y_kien_khac)
                                                <hr>
                                                <p class="mb-0"><strong>Ý kiến khác:</strong></p>
                                                <p class="text-muted">{{ $rating->y_kien_khac }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                            <p class="mb-0">Chưa có đánh giá nào</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($ratings->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-center">
                        {{ $ratings->links() }}
                    </div>
                </div>
                @endif
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
    .text-orange {
        color: #fd7e14 !important;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }
</style>
@endsection

