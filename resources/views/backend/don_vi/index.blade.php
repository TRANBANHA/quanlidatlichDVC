@extends('backend.components.layout')
@section('title')
    Quản lý đơn vị/phường
@endsection
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Quản lý đơn vị/phường</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="/admin">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li>Danh sách đơn vị/phường</li>
                </ul>
            </div>

            <div class="row mb-4">
                <div class="col">
                    <a href="{{ route('don-vi.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Thêm đơn vị/phường
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên đơn vị/phường</th>
                                <th>Mô tả</th>
                                <th>Số cán bộ</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($donVis as $donVi)
                                <tr>
                                    <td>{{ $loop->iteration + ($donVis->currentPage() - 1) * $donVis->perPage() }}</td>
                                    <td>{{ $donVi->ten_don_vi }}</td>
                                    <td>{{ $donVi->mo_ta ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $donVi->admins_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('don-vi.show', $donVi) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <a href="{{ route('don-vi.edit', $donVi) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <form action="{{ route('don-vi.destroy', $donVi) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Chưa có đơn vị/phường nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $donVis->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

