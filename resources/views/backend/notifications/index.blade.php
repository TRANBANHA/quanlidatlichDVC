@extends('backend.components.layout')
@section('title', 'Quản Lý Thông Báo')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Quản Lý Thông Báo</h5>
                    <a href="{{ route('admin.notifications.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus me-1"></i>Tạo Thông Báo
                    </a>
                </div>
                <div class="card-body">
                    <!-- Form tìm kiếm và lọc -->
                    <form method="GET" action="{{ route('admin.notifications.index') }}" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Tiêu đề, nội dung...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Người dùng</label>
                            <select name="user_id" class="form-select">
                                <option value="">Tất cả</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->ten ?? $user->name ?? $user->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ngày đăng</label>
                            <input type="date" name="publish_date" class="form-control" value="{{ request('publish_date') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Lọc
                            </button>
                        </div>
                    </form>

                    <!-- Bảng thông báo -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tiêu đề</th>
                                    <th>Người nhận</th>
                                    <th>Ngày đăng</th>
                                    <th>Ngày hết hạn</th>
                                    <th>Hình ảnh</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notifications as $index => $notification)
                                <tr>
                                    <td>{{ ($notifications->currentPage() - 1) * $notifications->perPage() + $index + 1 }}</td>
                                    <td>
                                        <strong>{{ Str::limit($notification->title, 50) }}</strong>
                                        @if($notification->expiry_date && $notification->expiry_date < now())
                                            <span class="badge bg-danger ms-2">Hết hạn</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($notification->user_id)
                                            {{ $notification->user->ten ?? $notification->user->name ?? $notification->user->email }}
                                        @else
                                            <span class="text-muted">Tất cả người dùng</span>
                                        @endif
                                    </td>
                                    <td>{{ $notification->publish_date ? \Carbon\Carbon::parse($notification->publish_date)->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $notification->expiry_date ? \Carbon\Carbon::parse($notification->expiry_date)->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($notification->image)
                                            <img src="{{ asset('storage/' . $notification->image) }}" alt="Image" style="max-width: 50px; max-height: 50px;">
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.notifications.edit', $notification->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Không có thông báo nào</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

