@extends('backend.components.layout')
@section('title', 'Quản lý hồ sơ')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="fw-bold mb-2 text-primary">Quản lý hồ sơ</h2>
                    <p class="text-muted mb-0">Quản lý và theo dõi tất cả hồ sơ đăng ký dịch vụ</p>
                </div>
            </div>
            <ul class="breadcrumbs mt-3">
                <li class="nav-home">
                    <a href="/admin"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li>Danh sách hồ sơ</li>
            </ul>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Thống kê nhanh -->
        <div class="row mb-4 g-3">
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stats-card stats-card-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon">
                                <i class="fas fa-folder"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <p class="stats-label mb-1">Tổng hồ sơ</p>
                                <h3 class="stats-value mb-0">{{ $stats['tong'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stats-card stats-card-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <p class="stats-label mb-1">Đã tiếp nhận</p>
                                <h3 class="stats-value mb-0">{{ $stats['da_tiep_nhan'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stats-card stats-card-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon">
                                <i class="fas fa-spinner"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <p class="stats-label mb-1">Đang xử lý</p>
                                <h3 class="stats-value mb-0">{{ $stats['dang_xu_ly'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stats-card stats-card-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <p class="stats-label mb-1">Cần bổ sung</p>
                                <h3 class="stats-value mb-0">{{ $stats['can_bo_sung'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stats-card stats-card-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <p class="stats-label mb-1">Hoàn tất</p>
                                <h3 class="stats-value mb-0">{{ $stats['hoan_tat'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bộ lọc -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Bộ lọc tìm kiếm</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.ho-so.index') }}" method="GET" class="row g-3">
                    @if($currentUser->isAdmin())
                        <div class="col-md-3">
                            <label class="form-label fw-semibold"><i class="fas fa-building me-1"></i>Chọn phường <span class="text-danger">*</span></label>
                            <select name="don_vi_id" class="form-select form-select-modern" required>
                                <option value="">-- Chọn phường --</option>
                                @foreach($donVis as $donVi)
                                    <option value="{{ $donVi->id }}" {{ request('don_vi_id') == $donVi->id ? 'selected' : '' }}>
                                        {{ $donVi->ten_don_vi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="fas fa-search me-1"></i>Tìm kiếm</label>
                        <input type="text" name="search" class="form-control form-control-modern" placeholder="Mã hồ sơ, tên, CCCD..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold"><i class="fas fa-tag me-1"></i>Trạng thái</label>
                        <select name="trang_thai" class="form-select form-select-modern">
                            <option value="">Tất cả trạng thái</option>
                            <option value="Đã tiếp nhận" {{ request('trang_thai') == 'Đã tiếp nhận' ? 'selected' : '' }}>Đã tiếp nhận</option>
                            <option value="Đang xử lý" {{ request('trang_thai') == 'Đang xử lý' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="Cần bổ sung hồ sơ" {{ request('trang_thai') == 'Cần bổ sung hồ sơ' ? 'selected' : '' }}>Cần bổ sung</option>
                            <option value="Hoàn tất" {{ request('trang_thai') == 'Hoàn tất' ? 'selected' : '' }}>Hoàn tất</option>
                            <option value="Đã hủy" {{ request('trang_thai') == 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold"><i class="fas fa-calendar-alt me-1"></i>Từ ngày</label>
                        <input type="date" name="tu_ngay" class="form-control form-control-modern" value="{{ request('tu_ngay') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold"><i class="fas fa-calendar-check me-1"></i>Đến ngày</label>
                        <input type="date" name="den_ngay" class="form-control form-control-modern" value="{{ request('den_ngay') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="w-100">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-search me-2"></i>Tìm kiếm
                            </button>
                            <a href="{{ route('admin.ho-so.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách hồ sơ -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Danh sách hồ sơ</h5>
                    <span class="badge bg-primary">{{ $hoSos->count() }} hồ sơ</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($currentUser->isAdmin() && !request('don_vi_id'))
                    <!-- Admin tổng chưa chọn phường -->
                    <div class="text-center py-5">
                        <div class="empty-state-table">
                            <i class="fas fa-building fa-3x mb-3 text-muted"></i>
                            <p class="text-muted mb-0">Vui lòng chọn phường để xem hồ sơ</p>
                        </div>
                    </div>
                @elseif($currentUser->isAdmin() || $currentUser->isAdminPhuong())
                    <!-- Hiển thị theo group (Admin tổng: đã chọn phường, Admin phường: theo nhân viên) -->
                    @forelse($groupedHoSos as $groupKey => $groupHoSos)
                        <div class="mb-4">
                            <!-- Header của group -->
                            <div class="bg-light p-3 border-bottom">
                                <h6 class="mb-0 fw-bold text-primary">
                                    @if($currentUser->isAdmin())
                                        <i class="fas fa-building me-2"></i>
                                        Phường: {{ $groupHoSos->first()->donVi->ten_don_vi ?? 'N/A' }}
                                        <span class="badge bg-primary ms-2">{{ $groupHoSos->count() }} hồ sơ</span>
                                    @elseif($currentUser->isAdminPhuong())
                                        <i class="fas fa-user-tie me-2"></i>
                                        @if($groupKey && $groupHoSos->first()->quanTriVien)
                                            Cán bộ: {{ $groupHoSos->first()->quanTriVien->ho_ten }}
                                        @else
                                            Chưa phân công
                                        @endif
                                        <span class="badge bg-primary ms-2">{{ $groupHoSos->count() }} hồ sơ</span>
                                    @endif
                                </h6>
                            </div>
                            
                            <!-- Bảng hồ sơ trong group -->
                            <div class="table-responsive">
                                <table class="table table-hover table-modern mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4"><i class="fas fa-hashtag me-2"></i>Mã hồ sơ</th>
                                            <th><i class="fas fa-sort-numeric-up me-2"></i>Số thứ tự</th>
                                            <th><i class="fas fa-user me-2"></i>Người dân</th>
                                            <th><i class="fas fa-concierge-bell me-2"></i>Dịch vụ</th>
                                            @if($currentUser->isAdmin() || $currentUser->isAdminPhuong())
                                                <th><i class="fas fa-user-tie me-2"></i>Cán bộ xử lý</th>
                                            @endif
                                            <th><i class="fas fa-calendar-alt me-2"></i>Ngày hẹn</th>
                                            <th><i class="fas fa-tag me-2"></i>Trạng thái</th>
                                            <th class="text-center"><i class="fas fa-cog me-2"></i>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupHoSos as $hoSo)
                                            <tr class="table-row-hover">
                                                <td class="ps-4">
                                                    <strong class="text-primary">{{ $hoSo->ma_ho_so }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $hoSo->so_thu_tu ?? '-' }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold">{{ $hoSo->nguoiDung->ten ?? 'N/A' }}</span>
                                                        <small class="text-muted">{{ $hoSo->nguoiDung->cccd ?? '' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">{{ $hoSo->dichVu->ten_dich_vu ?? 'N/A' }}</span>
                                                </td>
                                                @if($currentUser->isAdmin() || $currentUser->isAdminPhuong())
                                                    <td>
                                                        @if($currentUser->isAdminPhuong() || ($currentUser->isAdmin() && request('don_vi_id')))
                                                            @php
                                                                // Lấy thứ trong tuần của ngày hẹn
                                                                $ngayHen = \Carbon\Carbon::parse($hoSo->ngay_hen);
                                                                $thuTrongTuan = $ngayHen->dayOfWeek; // 0 = Chủ nhật, 1 = Thứ 2, ..., 6 = Thứ 7
                                                                $thuTrongTuan = $thuTrongTuan == 0 ? 7 : $thuTrongTuan;
                                                                
                                                                // Tìm schedule của dịch vụ vào thứ đó
                                                                $schedule = \App\Models\ServiceSchedule::where('dich_vu_id', $hoSo->dich_vu_id)
                                                                    ->where('thu_trong_tuan', $thuTrongTuan)
                                                                    ->where('trang_thai', true)
                                                                    ->first();
                                                                
                                                                $canBosForHoSo = collect();
                                                                $donViId = $currentUser->isAdmin() ? $hoSo->don_vi_id : $currentUser->don_vi_id;
                                                                
                                                                if ($schedule) {
                                                                    // Lấy các cán bộ đã được phân công vào schedule này
                                                                    $canBoIds = \App\Models\ServiceScheduleStaff::where('schedule_id', $schedule->id)
                                                                        ->pluck('can_bo_id')
                                                                        ->toArray();
                                                                    
                                                                    if (!empty($canBoIds)) {
                                                                        $canBosForHoSo = \App\Models\Admin::where('don_vi_id', $donViId)
                                                                            ->where('quyen', 0)
                                                                            ->whereIn('id', $canBoIds)
                                                                            ->orderBy('ho_ten')
                                                                            ->get();
                                                                    }
                                                                }
                                                                
                                                                // CHỈ thêm cán bộ đã được phân công vào danh sách NẾU cán bộ đó có trong schedule
                                                                // Không thêm cán bộ đã được phân công trước đó nếu không có trong schedule
                                                            @endphp
                                                            <form action="{{ route('admin.ho-so.assign', $hoSo->id) }}" method="POST" class="assign-form" data-ho-so-id="{{ $hoSo->id }}">
                                                                @csrf
                                                                <select name="quan_tri_vien_id" class="form-select form-select-sm assign-select" style="min-width: 150px;" {{ $canBosForHoSo->isEmpty() ? 'disabled' : '' }}>
                                                                    @if($canBosForHoSo->isEmpty())
                                                                        <option value="" disabled selected>Chưa phân công cán bộ</option>
                                                                    @else
                                                                        <option value="">-- Chọn cán bộ --</option>
                                                                        @foreach($canBosForHoSo as $canBo)
                                                                            <option value="{{ $canBo->id }}" {{ $hoSo->quan_tri_vien_id == $canBo->id ? 'selected' : '' }}>
                                                                                {{ $canBo->ho_ten }}
                                                                            </option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </form>
                                                        @else
                                                            @if($hoSo->quanTriVien)
                                                                <span class="badge bg-info">{{ $hoSo->quanTriVien->ho_ten }}</span>
                                                            @else
                                                                <span class="badge bg-secondary">Chưa phân công</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                @endif
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span>{{ \Carbon\Carbon::parse($hoSo->ngay_hen)->format('d/m/Y') }}</span>
                                                        <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $hoSo->gio_hen }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'Đã tiếp nhận' => 'info',
                                                            'Đang xử lý' => 'warning',
                                                            'Cần bổ sung hồ sơ' => 'danger',
                                                            'Hoàn tất' => 'success',
                                                            'Đã hủy' => 'secondary',
                                                        ];
                                                        $color = $statusColors[$hoSo->trang_thai] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge bg-{{ $color }} badge-modern">{{ $hoSo->trang_thai }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.ho-so.show', $hoSo->id) }}" class="btn btn-sm btn-primary btn-action" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="empty-state-table">
                                <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                                <p class="text-muted mb-0">Chưa có hồ sơ nào</p>
                            </div>
                        </div>
                    @endforelse
                @else
                    <!-- Cán bộ: Hiển thị trực tiếp, sắp xếp theo số thứ tự -->
                    <div class="table-responsive">
                        <table class="table table-hover table-modern mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4"><i class="fas fa-sort-numeric-up me-2"></i>Số thứ tự</th>
                                    <th><i class="fas fa-hashtag me-2"></i>Mã hồ sơ</th>
                                    <th><i class="fas fa-user me-2"></i>Người dân</th>
                                    <th><i class="fas fa-concierge-bell me-2"></i>Dịch vụ</th>
                                    <th><i class="fas fa-calendar-alt me-2"></i>Ngày hẹn</th>
                                    <th><i class="fas fa-tag me-2"></i>Trạng thái</th>
                                    <th class="text-center"><i class="fas fa-cog me-2"></i>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hoSos as $hoSo)
                                    <tr class="table-row-hover">
                                        <td class="ps-4">
                                            <span class="badge bg-info fs-6">{{ $hoSo->so_thu_tu ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <strong class="text-primary">{{ $hoSo->ma_ho_so }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold">{{ $hoSo->nguoiDung->ten ?? 'N/A' }}</span>
                                                <small class="text-muted">{{ $hoSo->nguoiDung->cccd ?? '' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $hoSo->dichVu->ten_dich_vu ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ \Carbon\Carbon::parse($hoSo->ngay_hen)->format('d/m/Y') }}</span>
                                                <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $hoSo->gio_hen }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'Đã tiếp nhận' => 'info',
                                                    'Đang xử lý' => 'warning',
                                                    'Cần bổ sung hồ sơ' => 'danger',
                                                    'Hoàn tất' => 'success',
                                                    'Đã hủy' => 'secondary',
                                                ];
                                                $color = $statusColors[$hoSo->trang_thai] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }} badge-modern">{{ $hoSo->trang_thai }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.ho-so.show', $hoSo->id) }}" class="btn btn-sm btn-primary btn-action" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="empty-state-table">
                                                <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                                                <p class="text-muted mb-0">Chưa có hồ sơ nào</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .stats-card {
        border: none;
        border-radius: 12px;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    }

    .stats-card-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .stats-card-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
    }

    .stats-card-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: white;
    }

    .stats-card-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }

    .stats-card-success {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stats-label {
        font-size: 0.875rem;
        opacity: 0.9;
        margin: 0;
    }

    .stats-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
    }

    .form-control-modern,
    .form-select-modern {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }

    .form-control-modern:focus,
    .form-select-modern:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .table-modern {
        font-size: 0.95rem;
    }

    .table-modern thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 15px;
        border-bottom: 2px solid #dee2e6;
    }

    .table-modern tbody td {
        padding: 15px;
        vertical-align: middle;
    }

    .table-row-hover {
        transition: all 0.2s ease;
    }

    .table-row-hover:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
    }

    .badge-modern {
        padding: 6px 12px;
        font-weight: 500;
        font-size: 0.85rem;
        border-radius: 6px;
    }

    .btn-action {
        border-radius: 8px;
        padding: 8px 12px;
        transition: all 0.3s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
    }

    .empty-state-table {
        padding: 40px 20px;
    }

    @media (max-width: 768px) {
        .stats-card {
            margin-bottom: 15px;
        }

        .table-modern {
            font-size: 0.85rem;
        }

        .table-modern thead th,
        .table-modern tbody td {
            padding: 10px 8px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tự động submit form khi Admin tổng chọn phường
        @if($currentUser->isAdmin())
        const donViSelect = document.querySelector('select[name="don_vi_id"]');
        if (donViSelect) {
            donViSelect.addEventListener('change', function() {
                if (this.value) {
                    // Giữ lại các filter khác khi submit
                    const form = this.closest('form');
                    form.submit();
                }
            });
        }
        @endif

        // Xử lý phân công cán bộ qua AJAX
        const assignSelects = document.querySelectorAll('.assign-select');
        assignSelects.forEach(function(select) {
            select.addEventListener('change', function() {
                const form = this.closest('form');
                const hoSoId = form.dataset.hoSoId;
                const canBoId = this.value;
                const originalValue = this.dataset.originalValue || '';

                // Disable select trong khi xử lý
                this.disabled = true;
                const originalText = this.options[this.selectedIndex].text;

                // Gửi request AJAX
                const formData = new FormData(form);
                formData.set('quan_tri_vien_id', canBoId || '');
                
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || form.querySelector('input[name="_token"]')?.value,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hiển thị thông báo thành công
                        showNotification('success', data.message);
                        // Cập nhật giá trị original
                        this.dataset.originalValue = canBoId;
                    } else {
                        // Hiển thị lỗi và khôi phục giá trị cũ
                        showNotification('error', data.message || 'Có lỗi xảy ra khi phân công.');
                        this.value = originalValue;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('error', 'Có lỗi xảy ra khi phân công hồ sơ.');
                    this.value = originalValue;
                })
                .finally(() => {
                    // Enable lại select
                    this.disabled = false;
                });
            });

            // Lưu giá trị ban đầu
            select.dataset.originalValue = select.value;
        });

        // Hàm hiển thị thông báo
        function showNotification(type, message) {
            // Tạo alert element
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999; min-width: 300px;" role="alert">
                    <i class="fas ${icon} me-2"></i>
                    <div class="d-inline">${message}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Thêm vào body
            const alertDiv = document.createElement('div');
            alertDiv.innerHTML = alertHtml;
            document.body.appendChild(alertDiv.firstElementChild);

            // Tự động ẩn sau 3 giây
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 150);
                }
            }, 3000);
        }
    });
</script>
@if($currentUser->isAdmin())
@endif
@endsection
