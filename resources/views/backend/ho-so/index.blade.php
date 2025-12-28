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

        <!-- Thống kê nhân viên (chỉ Admin phường) -->
        @if($currentUser->isAdminPhuong() && isset($canBoList) && $canBoList->count() > 0)
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="fas fa-users me-2 text-primary"></i>Thống kê nhân viên</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($canBoList as $canBo)
                        @php
                            $soHoSoDaXuLy = $canBoStats[$canBo->id] ?? 0;
                            $tongHoSoCuaCanBo = $hoSos->where('quan_tri_vien_id', $canBo->id)->count();
                            $tyLe = $tongHoSoCuaCanBo > 0 ? round(($soHoSoDaXuLy / $tongHoSoCuaCanBo) * 100, 1) : 0;
                        @endphp
                        <div class="col-xl-3 col-md-4 col-sm-6">
                            <div class="card border h-100 staff-stats-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                        <div class="staff-avatar me-3">
                                            <div class="avatar-circle bg-primary">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold">{{ $canBo->ho_ten }}</h6>
                                            <div class="mb-2">
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-folder-open me-1"></i>
                                                    Tổng hồ sơ: <strong>{{ $tongHoSoCuaCanBo }}</strong>
                                                </small>
                                                <small class="text-success d-block mt-1">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Đã xử lý: <strong>{{ $soHoSoDaXuLy }}</strong>
                                                </small>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ $tyLe }}%" 
                                                     aria-valuenow="{{ $tyLe }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1">Tỷ lệ: {{ $tyLe }}%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <!-- Thống kê hồ sơ chưa chỉ định -->
                    @php
                        $hoSoChuaPhanCong = $hoSos->whereNull('quan_tri_vien_id')->count();
                    @endphp
                    @if($hoSoChuaPhanCong > 0)
                        <div class="col-xl-3 col-md-4 col-sm-6">
                            <div class="card border h-100 staff-stats-card border-warning">
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                        <div class="staff-avatar me-3">
                                            <div class="avatar-circle bg-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-warning">Chưa chỉ định</h6>
                                            <div class="mb-2">
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-folder-open me-1"></i>
                                                    Hồ sơ: <strong>{{ $hoSoChuaPhanCong }}</strong>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Tab dịch vụ -->
        @if(($currentUser->isAdminPhuong() || $currentUser->isCanBo()) && $services->count() > 0)
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="fas fa-briefcase me-2 text-primary"></i>Chọn dịch vụ</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @php
                        $currentDichVuId = request('dich_vu_id');
                    @endphp
                    <div class="col-md-12">
                        <a href="{{ route('admin.ho-so.index', array_merge(request()->except('dich_vu_id'), ['dich_vu_id' => ''])) }}" 
                           class="btn btn-{{ !$currentDichVuId ? 'primary' : 'outline-primary' }} mb-2 me-2">
                            <i class="fas fa-list me-1"></i>Tất cả dịch vụ
                            <span class="badge bg-{{ !$currentDichVuId ? 'light text-dark' : 'primary' }} ms-2">{{ $serviceCounts['all'] ?? $hoSos->count() }}</span>
                        </a>
                        @foreach($services as $service)
                            @php
                                $count = $serviceCounts[$service->id] ?? 0;
                                $isActive = $currentDichVuId == $service->id;
                            @endphp
                            <a href="{{ route('admin.ho-so.index', array_merge(request()->except('dich_vu_id'), ['dich_vu_id' => $service->id])) }}" 
                               class="btn btn-{{ $isActive ? 'primary' : 'outline-primary' }} mb-2 me-2">
                                <i class="fas fa-briefcase me-1"></i>{{ $service->ten_dich_vu }}
                                <span class="badge bg-{{ $isActive ? 'light text-dark' : 'primary' }} ms-2">{{ $count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

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
                    <!-- Giữ lại dich_vu_id trong form nếu đã chọn -->
                    @if(request('dich_vu_id'))
                        <input type="hidden" name="dich_vu_id" value="{{ request('dich_vu_id') }}">
                    @endif
                </form>
            </div>
        </div>

        <!-- Khung Hôm nay xử lý -->
        @if(($currentUser->isAdmin() || $currentUser->isAdminPhuong() || $currentUser->isCanBo()) && isset($groupedHoSosHomNay) && count($groupedHoSosHomNay) > 0)
        <div class="card shadow-sm border-0 mb-4 border-warning border-2">
            <div class="card-header bg-warning text-white border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-calendar-day me-2"></i>HÔM NAY XỬ LÝ
                        <span class="badge bg-light text-dark ms-2">{{ $hoSoHomNay->count() }} hồ sơ</span>
                    </h5>
                    <span class="badge bg-danger animate-pulse">
                        <i class="fas fa-exclamation-circle me-1"></i>Ưu tiên
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                @include('backend.ho-so.partials.hoso-list', [
                    'groupedHoSos' => $groupedHoSosHomNay,
                    'currentUser' => $currentUser,
                    'canBoMap' => $canBoMap ?? [],
                    'canBoList' => $canBoList ?? collect(),
                    'isHomNay' => true
                ])
            </div>
        </div>
        @endif

        <!-- Khung Thông báo quan trọng (nếu có) -->
        @php
            $hoSoQuanTrong = $hoSos->filter(function($hoSo) {
                return in_array($hoSo->trang_thai, ['Đang xử lý', 'Cần bổ sung hồ sơ']) && 
                       $hoSo->ngay_hen && 
                       \Carbon\Carbon::parse($hoSo->ngay_hen)->lte(\Carbon\Carbon::now()->addDays(2));
            })->count();
        @endphp
        @if($hoSoQuanTrong > 0)
        <div class="card shadow-sm border-0 mb-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i>CẦN CHÚ Ý
                    <span class="badge bg-light text-danger ms-2">{{ $hoSoQuanTrong }} hồ sơ cần xử lý gấp</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Có <strong>{{ $hoSoQuanTrong }}</strong> hồ sơ đang xử lý hoặc cần bổ sung trong 2 ngày tới. Vui lòng kiểm tra và xử lý kịp thời.
                </div>
            </div>
        </div>
        @endif

        <!-- Danh sách hồ sơ các ngày khác -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Danh sách hồ sơ theo ngày</h5>
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
                @elseif($currentUser->isAdmin() || $currentUser->isAdminPhuong() || $currentUser->isCanBo())
                    <!-- Hiển thị theo ngày, trong mỗi ngày group theo cán bộ hoặc dịch vụ -->
                    @forelse($groupedHoSos as $ngayKey => $ngayGroup)
                        @php
                            // Xác định ngày hiển thị
                            if ($ngayKey === 'khong_co_ngay') {
                                $ngayHienThi = 'Chưa có ngày hẹn';
                                $isToday = false;
                            } else {
                                try {
                                    $ngayCarbon = \Carbon\Carbon::parse($ngayKey);
                                    $ngayHienThi = $ngayCarbon->format('d/m/Y');
                                    $isToday = $ngayCarbon->isToday();
                                    $isTomorrow = $ngayCarbon->isTomorrow();
                                } catch (\Exception $e) {
                                    $ngayHienThi = $ngayKey;
                                    $isToday = false;
                                    $isTomorrow = false;
                                }
                            }
                        @endphp
                        
                        <!-- Header ngày -->
                        <div class="bg-info text-white p-3 border-bottom">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $ngayHienThi }}
                                @if($isToday)
                                    <span class="badge bg-warning text-dark ms-2">Hôm nay</span>
                                @elseif($isTomorrow)
                                    <span class="badge bg-secondary ms-2">Ngày mai</span>
                                @endif
                                @php
                                    $tongHoSoTrongNgay = is_a($ngayGroup, 'Illuminate\Support\Collection') 
                                        ? $ngayGroup->flatten()->count() 
                                        : (is_array($ngayGroup) ? collect($ngayGroup)->flatten()->count() : $ngayGroup->count());
                                @endphp
                                <span class="badge bg-light text-dark ms-2">{{ $tongHoSoTrongNgay }} hồ sơ</span>
                            </h6>
                        </div>
                        
                        <!-- Nếu là Admin phường hoặc Cán bộ, group tiếp theo cán bộ/dịch vụ -->
                        @if($currentUser->isAdminPhuong() || $currentUser->isCanBo())
                            @foreach($ngayGroup as $subGroupKey => $subGroupHoSos)
                                @include('backend.ho-so.partials.hoso-group', [
                                    'groupHoSos' => $subGroupHoSos,
                                    'groupKey' => $subGroupKey,
                                    'currentUser' => $currentUser,
                                    'canBoMap' => $canBoMap ?? [],
                                    'canBoList' => $canBoList ?? collect()
                                ])
                            @endforeach
                        @else
                            <!-- Admin tổng: hiển thị trực tiếp -->
                            @include('backend.ho-so.partials.hoso-group', [
                                'groupHoSos' => $ngayGroup,
                                'groupKey' => null,
                                'currentUser' => $currentUser,
                                'canBoMap' => [],
                                'canBoList' => collect()
                            ])
                        @endif
                    @empty
                        <div class="text-center py-5">
                            <div class="empty-state-table">
                                <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                                <p class="text-muted mb-0">Chưa có hồ sơ nào</p>
                            </div>
                        </div>
                    @endforelse
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

    .staff-stats-card {
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .staff-stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
    }

    .staff-avatar {
        flex-shrink: 0;
    }

    .avatar-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .avatar-circle.bg-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .avatar-circle.bg-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
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

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }

    .border-warning.border-2 {
        border-width: 3px !important;
    }

    .table-sm th,
    .table-sm td {
        padding: 0.5rem;
        font-size: 0.875rem;
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

        // Xử lý cập nhật cán bộ qua AJAX
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
                        showNotification('error', data.message || 'Có lỗi xảy ra khi cập nhật.');
                        this.value = originalValue;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('error', 'Có lỗi xảy ra khi cập nhật hồ sơ.');
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
