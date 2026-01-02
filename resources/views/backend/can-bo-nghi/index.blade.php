@extends('backend.components.layout')
@section('title', 'Quản lý cán bộ báo nghỉ')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header mb-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="fw-bold mb-2 text-primary">Quản lý cán bộ báo nghỉ</h2>
                    <p class="text-muted mb-0">Danh sách cán bộ đã báo nghỉ</p>
                </div>
                @if(auth('admin')->user()->isCanBo())
                    <a href="{{ route('admin.can-bo-nghi.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Báo nghỉ
                    </a>
                @endif
            </div>
            <ul class="breadcrumbs mt-3">
                <li class="nav-home">
                    <a href="/admin"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li>Cán bộ báo nghỉ</li>
            </ul>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.can-bo-nghi.index') }}" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" name="tu_ngay" class="form-control" value="{{ request('tu_ngay') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" name="den_ngay" class="form-control" value="{{ request('den_ngay') }}">
                    </div>
                    @if(auth('admin')->user()->isAdmin() || auth('admin')->user()->isAdminPhuong())
                    <div class="col-md-2">
                        <label class="form-label">Cán bộ</label>
                        <select name="can_bo_id" class="form-select">
                            <option value="">-- Tất cả --</option>
                            @foreach($canBoList as $canBo)
                                <option value="{{ $canBo->id }}" {{ request('can_bo_id') == $canBo->id ? 'selected' : '' }}>
                                    {{ $canBo->ho_ten }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-2">
                        <label class="form-label">Trạng thái</label>
                        <select name="trang_thai" class="form-select">
                            <option value="">-- Tất cả --</option>
                            <option value="cho_duyet" {{ request('trang_thai') == 'cho_duyet' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="da_duyet" {{ request('trang_thai') == 'da_duyet' ? 'selected' : '' }}>Đã duyệt</option>
                            <option value="tu_choi" {{ request('trang_thai') == 'tu_choi' ? 'selected' : '' }}>Từ chối</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Tìm kiếm
                        </button>
                        <a href="{{ route('admin.can-bo-nghi.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                @if($danhSachNghi->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Cán bộ</th>
                                    <th>Ngày nghỉ</th>
                                    <th>Lý do</th>
                                    <th>Trạng thái</th>
                                    <th>Đã chuyển hồ sơ</th>
                                    <th>Người duyệt</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($danhSachNghi as $index => $nghi)
                                    <tr>
                                        <td>{{ ($danhSachNghi->currentPage() - 1) * $danhSachNghi->perPage() + $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $nghi->canBo->ho_ten ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $nghi->canBo->donVi->ten_don_vi ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">
                                                {{ \Carbon\Carbon::parse($nghi->ngay_nghi)->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>{{ $nghi->ly_do ?? 'Không có' }}</td>
                                        <td>
                                            @if($nghi->trang_thai == 'cho_duyet')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>Chờ duyệt
                                                </span>
                                            @elseif($nghi->trang_thai == 'da_duyet')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Đã duyệt
                                                </span>
                                            @elseif($nghi->trang_thai == 'tu_choi')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i>Từ chối
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($nghi->da_chuyen_ho_so)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Đã chuyển
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times me-1"></i>Chưa chuyển
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($nghi->nguoiDuyet)
                                                {{ $nghi->nguoiDuyet->ho_ten }}<br>
                                                <small class="text-muted">{{ $nghi->ngay_duyet ? $nghi->ngay_duyet->format('d/m/Y H:i') : '' }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $nghi->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @if($nghi->trang_thai == 'cho_duyet' && (auth('admin')->user()->isAdminPhuong() || auth('admin')->user()->isAdmin()))
                                                    <!-- Modal Duyệt -->
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#duyetModal{{ $nghi->id }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <!-- Modal Từ chối -->
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#tuChoiModal{{ $nghi->id }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                                <form action="{{ route('admin.can-bo-nghi.destroy', $nghi->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Bạn có chắc muốn xóa báo nghỉ này?');"
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $danhSachNghi->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có cán bộ nào báo nghỉ</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Modals cho Duyệt và Từ chối -->
@foreach($danhSachNghi as $nghi)
    @if($nghi->trang_thai == 'cho_duyet' && (auth('admin')->user()->isAdminPhuong() || auth('admin')->user()->isAdmin()))
        <!-- Modal Duyệt -->
        <div class="modal fade" id="duyetModal{{ $nghi->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.can-bo-nghi.duyet', $nghi->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Duyệt báo nghỉ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Bạn có chắc muốn duyệt báo nghỉ của <strong>{{ $nghi->canBo->ho_ten ?? 'N/A' }}</strong> vào ngày <strong>{{ \Carbon\Carbon::parse($nghi->ngay_nghi)->format('d/m/Y') }}</strong>?</p>
                            <div class="mb-3">
                                <label class="form-label">Ghi chú (tùy chọn)</label>
                                <textarea name="ghi_chu_duyet" class="form-control" rows="3" maxlength="500"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-success">Duyệt</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Từ chối -->
        <div class="modal fade" id="tuChoiModal{{ $nghi->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.can-bo-nghi.tu-choi', $nghi->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Từ chối báo nghỉ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Bạn có chắc muốn từ chối báo nghỉ của <strong>{{ $nghi->canBo->ho_ten ?? 'N/A' }}</strong> vào ngày <strong>{{ \Carbon\Carbon::parse($nghi->ngay_nghi)->format('d/m/Y') }}</strong>?</p>
                            <div class="mb-3">
                                <label class="form-label">Lý do từ chối <span class="text-danger">*</span></label>
                                <textarea name="ly_do_tu_choi" class="form-control @error('ly_do_tu_choi') is-invalid @enderror" rows="3" maxlength="500" required></textarea>
                                @error('ly_do_tu_choi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-danger">Từ chối</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection
