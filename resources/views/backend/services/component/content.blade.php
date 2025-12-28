<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Quản lý Dịch vụ</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li>Danh sách Dịch vụ</li>
            </ul>
        </div>

        <!-- Search Form -->
        <div class="row mb-4">
            <div class="col">
                <form action="{{ route('services.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="Tìm kiếm theo tên dịch vụ..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        <a href="{{ route('services.create') }}" class="btn btn-success">Thêm mới</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên dịch vụ</th>
                            <th>Mô tả</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($services as $service)
                            <tr>
                                <td>{{ $loop->iteration + ($services->currentPage() - 1) * $services->perPage() }}</td>
                                <td>{{ $service->ten_dich_vu }}</td>
                                <td>{{ $service->mo_ta }}</td>
                                <td>
                                    <a href="{{ route('services.edit', $service->id) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <form action="{{ route('services.destroy', $service->id) }}" method="POST"
                                        style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xoá dịch vụ này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Không có dịch vụ nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $services->links() }}
        </div>
    </div>
</div>
