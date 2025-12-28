@extends('backend.components.layout')
@section('title', 'Đánh Giá Của Tôi')

@section('content')
<div class=" py-4">
    <!-- Thống kê -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Tổng đánh giá</h6>
                            <h3 class="mb-0">{{ $stats['tong'] }}</h3>
                        </div>
                        <div class="icon-bg">
                            <i class="fas fa-star fa-2x"></i>
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
                            <h6 class="text-white-50 mb-1">Điểm trung bình</h6>
                            <h3 class="mb-0">{{ number_format($stats['diem_tb'], 1) }}/5</h3>
                        </div>
                        <div class="icon-bg">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h6 class="text-white-50 mb-1">5 sao</h6>
                            <h3 class="mb-0">{{ $stats['5_sao'] }}</h3>
                        </div>
                        <div class="icon-bg">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">1-2 sao</h6>
                            <h3 class="mb-0">{{ $stats['1_sao'] + $stats['2_sao'] }}</h3>
                        </div>
                        <div class="icon-bg">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('staff-ratings.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Lọc theo điểm</label>
                            <select name="diem" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="5" {{ request('diem') == '5' ? 'selected' : '' }}>5 sao</option>
                                <option value="4" {{ request('diem') == '4' ? 'selected' : '' }}>4 sao</option>
                                <option value="3" {{ request('diem') == '3' ? 'selected' : '' }}>3 sao</option>
                                <option value="2" {{ request('diem') == '2' ? 'selected' : '' }}>2 sao</option>
                                <option value="1" {{ request('diem') == '1' ? 'selected' : '' }}>1 sao</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" class="form-control" placeholder="Mã hồ sơ, tên người dùng..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Lọc
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách đánh giá -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Danh sách đánh giá</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã hồ sơ</th>
                                    <th>Người đánh giá</th>
                                    <th>Dịch vụ</th>
                                    <th>Điểm</th>
                                    <th>Bình luận</th>
                                    <th>Ngày đánh giá</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ratings as $rating)
                                <tr>
                                    <td><strong>{{ $rating->hoSo->ma_ho_so ?? 'N/A' }}</strong></td>
                                    <td>{{ $rating->nguoiDung->ten ?? 'N/A' }}</td>
                                    <td>{{ $rating->hoSo->dichVu->ten_dich_vu ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $rating->diem >= 4 ? 'success' : ($rating->diem >= 3 ? 'warning' : 'danger') }}">
                                            {{ $rating->diem }} sao
                                        </span>
                                        <div class="mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $rating->diem ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($rating->binh_luan, 50) ?? 'Không có' }}</td>
                                    <td>{{ $rating->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('staff-ratings.show', $rating->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Chưa có đánh giá nào</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $ratings->links() }}
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
@endsection

