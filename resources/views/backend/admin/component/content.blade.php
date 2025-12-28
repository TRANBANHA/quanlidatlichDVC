<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Quản lý tài khoản quản trị / cán bộ</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="/admin">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li>Danh sách tài khoản</li>
            </ul>
        </div>

        <!-- Filter và Import -->
        <div class="row mb-3">
            <div class="col-md-8">
                <form action="{{ route('quantri.index') }}" method="GET" class="d-flex gap-1 align-items-center">
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="Tìm kiếm theo tên..." value="{{ request('name') }}" style="max-width: 180px;">
                    <select name="don_vi_id" class="form-select form-select-sm" style="max-width: 160px;">
                        <option value="">Tất cả đơn vị</option>
                        @foreach ($donVis as $donVi)
                            <option value="{{ $donVi->id }}" {{ request('don_vi_id') == $donVi->id ? 'selected' : '' }}>
                                {{ $donVi->ten_don_vi }}
                            </option>
                        @endforeach
                    </select>
                    <select name="quyen" class="form-select form-select-sm" style="max-width: 130px;">
                        <option value="">Tất cả quyền</option>
                        <option value="1" {{ request('quyen') == '1' ? 'selected' : '' }}>Admin tổng</option>
                        <option value="2" {{ request('quyen') == '2' ? 'selected' : '' }}>Admin phường</option>
                        <option value="0" {{ request('quyen') == '0' ? 'selected' : '' }}>Cán bộ phường</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Tìm kiếm</button>
                    <a href="{{ route('quantri.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                </form>
            </div>
            <div class="col-md-4 text-end">
                @php
                    $currentUser = Auth::guard('admin')->user();
                @endphp
                @if($currentUser->isAdmin())
                    <a href="{{ route('don-vi.index') }}" class="btn btn-info">
                        <i class="fas fa-building"></i> Quản lý đơn vị
                    </a>
                @endif
                @if(!$currentUser->isCanBo())
                    <a href="{{ route('quantri.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Thêm tài khoản
                    </a>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-upload"></i> Import cán bộ
                    </button>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {!! session('warning') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle me-2"></i>
                {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Lỗi:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Họ tên</th>
                            <th>Tên đăng nhập</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Đơn vị/Phường</th>
                            <th>Quyền</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($listAdmin as $admin)
                            <tr>
                                <td>{{ $loop->iteration + ($listAdmin->currentPage() - 1) * $listAdmin->perPage() }}</td>
                                <td>{{ $admin->ho_ten ?? '-' }}</td>
                                <td>{{ $admin->ten_dang_nhap }}</td>
                                <td>{{ $admin->email ?? '-' }}</td>
                                <td>{{ $admin->so_dien_thoai ?? '-' }}</td>
                                <td>
                                    @if ($admin->donVi)
                                        <span class="badge bg-info">{{ $admin->donVi->ten_don_vi }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($admin->quyen == 1) bg-danger
                                        @elseif($admin->quyen == 2) bg-warning text-dark
                                        @else bg-primary
                                        @endif">
                                        {{ \App\Models\Admin::getRoleName($admin->quyen) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $currentUser = Auth::guard('admin')->user();
                                    @endphp
                                    @if(!$currentUser->isCanBo())
                                        @if($currentUser->isAdmin() || ($currentUser->isAdminPhuong() && $admin->quyen == 0 && $admin->don_vi_id == $currentUser->don_vi_id))
                                            <a href="{{ route('quantri.edit', $admin) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($currentUser->isAdmin() || ($currentUser->isAdminPhuong() && $admin->quyen == 0 && $admin->don_vi_id == $currentUser->don_vi_id && $admin->id != $currentUser->id))
                                            <form action="{{ route('quantri.destroy', $admin) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Không có dữ liệu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $listAdmin->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('quantri.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($selectedDonViId) && $selectedDonViId)
                    <input type="hidden" name="redirect_don_vi_id" value="{{ $selectedDonViId }}">
                @endif
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import cán bộ từ Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="don_vi_id" class="form-label">Đơn vị/phường <span class="text-danger">*</span></label>
                        @if(isset($selectedDonViId) && $selectedDonViId)
                            @php
                                $selectedDonVi = $donVis->firstWhere('id', $selectedDonViId);
                            @endphp
                            <input type="hidden" name="don_vi_id" value="{{ $selectedDonViId }}">
                            <input type="text" class="form-control" value="{{ $selectedDonVi->ten_don_vi ?? 'N/A' }}" readonly>
                            <small class="text-muted">Đang import cán bộ cho phường: <strong>{{ $selectedDonVi->ten_don_vi ?? 'N/A' }}</strong></small>
                        @else
                            <select name="don_vi_id" id="don_vi_id" class="form-select" required>
                                <option value="">-- Chọn đơn vị/phường --</option>
                                @foreach ($donVis as $donVi)
                                    <option value="{{ $donVi->id }}">{{ $donVi->ten_don_vi }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">Chọn file Excel <span class="text-danger">*</span></label>
                        <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Định dạng: Họ tên | Tên đăng nhập | Mật khẩu | Email | Số điện thoại</small>
                    </div>
                    <div class="alert alert-info">
                        <strong>Lưu ý:</strong>
                        <ul class="mb-0">
                            <li>File Excel phải có header ở dòng đầu tiên</li>
                            <li>Các cột: Họ tên, Tên đăng nhập, Mật khẩu (bắt buộc), Email, Số điện thoại (tùy chọn)</li>
                            <li>Tất cả cán bộ import sẽ được gán quyền "Cán bộ"</li>
                        </ul>
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            <a href="{{ asset('templates/mau-import-can-bo-phuong.csv') }}" class="btn btn-sm btn-outline-info" download>
                                <i class="fas fa-download"></i> Tải CSV trực tiếp
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
