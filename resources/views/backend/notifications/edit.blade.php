@extends('backend.components.layout')
@section('title', 'Sửa Thông Báo')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Sửa Thông Báo</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.notifications.update', $notification->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $notification->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="5" required>{{ old('content', $notification->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Người nhận</label>
                                <select name="user_id" class="form-select">
                                    <option value="">Tất cả người dùng</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $notification->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->ten ?? $user->name ?? $user->email }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày đăng <span class="text-danger">*</span></label>
                                <input type="date" name="publish_date" class="form-control @error('publish_date') is-invalid @enderror" value="{{ old('publish_date', $notification->publish_date ? \Carbon\Carbon::parse($notification->publish_date)->format('Y-m-d') : '') }}" required>
                                @error('publish_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày hết hạn</label>
                                <input type="date" name="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror" value="{{ old('expiry_date', $notification->expiry_date ? \Carbon\Carbon::parse($notification->expiry_date)->format('Y-m-d') : '') }}">
                                @error('expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hình ảnh</label>
                                @if($notification->image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $notification->image) }}" alt="Current image" style="max-width: 200px; max-height: 200px;">
                                        <p class="text-muted small">Hình ảnh hiện tại</p>
                                    </div>
                                @endif
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Để trống nếu không muốn thay đổi</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Video</label>
                                @if($notification->video)
                                    <div class="mb-2">
                                        <video controls style="max-width: 200px; max-height: 200px;">
                                            <source src="{{ asset('storage/' . $notification->video) }}" type="video/mp4">
                                        </video>
                                        <p class="text-muted small">Video hiện tại</p>
                                    </div>
                                @endif
                                <input type="file" name="video" class="form-control @error('video') is-invalid @enderror" accept="video/*">
                                @error('video')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Để trống nếu không muốn thay đổi</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Cập Nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

