@extends('website.components.layout')

@section('title', 'Lịch hẹn của tôi')

@section('content')
<div class="container-fluid py-5 bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5 animate-fade-in-up">
            <div>
                <h2 class="mb-2 fw-bold text-gradient" style="font-size: 2.5rem;">
                    <i class="fas fa-calendar-check me-2"></i>Lịch hẹn của tôi
                </h2>
                <p class="text-muted">Quản lý và theo dõi các lịch hẹn dịch vụ hành chính</p>
            </div>
            <a href="{{ route('booking.select-phuong') }}" class="btn btn-primary btn-lg shadow-beautiful">
                <i class="fas fa-plus me-2"></i>Đặt lịch mới
            </a>
        </div>

        <!-- Thống kê -->
        <div class="row mb-5 g-4">
            <div class="col-md-3 col-sm-6 animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="stat-card bg-gradient-primary text-white shadow-beautiful">
                    <i class="fas fa-list-alt"></i>
                    <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
                    <p class="mb-0 fs-5">Tổng số</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="stat-card bg-gradient-info text-white shadow-beautiful">
                    <i class="fas fa-check-circle"></i>
                    <h3 class="fw-bold mb-0">{{ $stats['received'] }}</h3>
                    <p class="mb-0 fs-5">Đã tiếp nhận</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 animate-fade-in-up" style="animation-delay: 0.3s;">
                <div class="stat-card bg-gradient-warning text-white shadow-beautiful">
                    <i class="fas fa-spinner"></i>
                    <h3 class="fw-bold mb-0">{{ $stats['processing'] }}</h3>
                    <p class="mb-0 fs-5">Đang xử lý</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 animate-fade-in-up" style="animation-delay: 0.4s;">
                <div class="stat-card bg-gradient-success text-white shadow-beautiful">
                    <i class="fas fa-check-double"></i>
                    <h3 class="fw-bold mb-0">{{ $stats['completed'] }}</h3>
                    <p class="mb-0 fs-5">Hoàn tất</p>
                </div>
            </div>
        </div>

        <!-- Bộ lọc -->
        <div class="card shadow-beautiful mb-5 animate-fade-in-up" style="animation-delay: 0.5s;">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-filter text-primary me-2"></i>Bộ lọc tìm kiếm</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('my-bookings.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="trang_thai" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="Đã tiếp nhận" {{ request('trang_thai') == 'Đã tiếp nhận' ? 'selected' : '' }}>Đã tiếp nhận</option>
                            <option value="Đang xử lý" {{ request('trang_thai') == 'Đang xử lý' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="Cần bổ sung hồ sơ" {{ request('trang_thai') == 'Cần bổ sung hồ sơ' ? 'selected' : '' }}>Cần bổ sung</option>
                            <option value="Hoàn tất" {{ request('trang_thai') == 'Hoàn tất' ? 'selected' : '' }}>Hoàn tất</option>
                            <option value="Đã hủy" {{ request('trang_thai') == 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" name="ngay_tu" class="form-control" value="{{ request('ngay_tu') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" name="ngay_den" class="form-control" value="{{ request('ngay_den') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100 shadow-beautiful">
                            <i class="fas fa-search me-2"></i>Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách lịch hẹn -->
        @if($hoSos->count() > 0)
            <div class="row g-4">
                @foreach($hoSos as $index => $hoSo)
                    <div class="col-md-6 animate-fade-in-up" style="animation-delay: {{ ($index % 2) * 0.1 }}s;">
                        <div class="card h-100 shadow-beautiful hover-lift">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-2">
                                            <span class="badge bg-gradient-primary rounded-beautiful px-3 py-2" style="font-size: 0.9rem;">{{ $hoSo->ma_ho_so }}</span>
                                        </h5>
                                        <p class="text-muted mb-0 fw-semibold">{{ $hoSo->dichVu->ten_dich_vu }}</p>
                                    </div>
                                    <span class="badge rounded-beautiful px-3 py-2
                                        @if($hoSo->trang_thai == 'Hoàn tất') bg-gradient-success text-white
                                        @elseif($hoSo->trang_thai == 'Đang xử lý') bg-gradient-warning text-white
                                        @elseif($hoSo->trang_thai == 'Cần bổ sung hồ sơ') text-white
                                        @elseif($hoSo->trang_thai == 'Đã hủy') bg-secondary text-white
                                        @else bg-gradient-info text-white
                                        @endif" style="font-size: 0.85rem; font-weight: 600; @if($hoSo->trang_thai == 'Cần bổ sung hồ sơ') background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); @endif">
                                        {{ $hoSo->trang_thai }}
                                    </span>
                                </div>

                                <div class="mb-4">
                                    <p class="mb-3">
                                        <i class="fas fa-building text-primary me-2"></i>
                                        <strong class="text-dark">Phường:</strong> 
                                        <span class="text-muted">{{ $hoSo->donVi->ten_don_vi }}</span>
                                    </p>
                                    <p class="mb-3">
                                        <i class="fas fa-calendar text-primary me-2"></i>
                                        <strong class="text-dark">Ngày hẹn:</strong> 
                                        <span class="text-muted">{{ \Carbon\Carbon::parse($hoSo->ngay_hen)->format('d/m/Y') }} lúc {{ $hoSo->gio_hen }}</span>
                                    </p>
                                    @if($hoSo->quanTriVien)
                                        <p class="mb-0">
                                            <i class="fas fa-user text-primary me-2"></i>
                                            <strong class="text-dark">Cán bộ:</strong> 
                                            <span class="text-muted">{{ $hoSo->quanTriVien->ho_ten }}</span>
                                        </p>
                                    @endif
                                </div>

                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ route('my-bookings.show', $hoSo->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye me-1"></i>Chi tiết
                                    </a>
                                    @if($hoSo->canBeCancelled())
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $hoSo->id }}">
                                            <i class="fas fa-times me-1"></i>Hủy
                                        </button>
                                    @endif
                                    @if($hoSo->isCompleted() && !$hoSo->rating)
                                        <a href="{{ route('rating.create', $hoSo->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-star me-1"></i>Đánh giá
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal hủy -->
                    <div class="modal fade" id="cancelModal{{ $hoSo->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('my-bookings.cancel', $hoSo->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Hủy lịch hẹn</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn hủy lịch hẹn <strong>{{ $hoSo->ma_ho_so }}</strong>?</p>
                                        <div class="mb-3">
                                            <label class="form-label">Lý do hủy <span class="text-danger">*</span></label>
                                            <textarea name="ly_do_huy" class="form-control" rows="3" required placeholder="Nhập lý do hủy..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                        <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Phân trang -->
            <div class="mt-4">
                {{ $hoSos->links() }}
            </div>
        @else
            <div class="alert alert-info text-center rounded-beautiful shadow-beautiful py-5 animate-fade-in-up">
                <div class="mb-4">
                    <i class="fas fa-info-circle fa-4x text-info mb-3" style="opacity: 0.7;"></i>
                </div>
                <h4 class="fw-bold mb-3">Chưa có lịch hẹn nào</h4>
                <p class="mb-4 fs-5">Bạn chưa có lịch hẹn nào. Hãy đặt lịch để sử dụng dịch vụ!</p>
                <a href="{{ route('booking.select-phuong') }}" class="btn btn-primary btn-lg shadow-beautiful">
                    <i class="fas fa-calendar-plus me-2"></i>Đặt lịch ngay
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

