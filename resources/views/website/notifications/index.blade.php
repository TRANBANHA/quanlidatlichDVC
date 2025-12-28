@extends('website.components.layout')

@section('title', 'Thông báo')

@section('content')
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="fas fa-bell me-2"></i>Thông báo</h2>
            @if($unreadCount > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-check-double me-2"></i>Đánh dấu tất cả đã đọc
                    </button>
                </form>
            @endif
        </div>

        @if($unreadCount > 0)
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Bạn có <strong>{{ $unreadCount }}</strong> thông báo chưa đọc
            </div>
        @endif

        @if($notifications->count() > 0)
            <div class="list-group">
                @foreach($notifications as $notification)
                    <div class="list-group-item {{ !$notification->is_read ? 'list-group-item-primary' : '' }}">
                        <div class="d-flex w-100 justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    @if(!$notification->is_read)
                                        <span class="badge bg-danger me-2">Mới</span>
                                    @endif
                                    {{ $notification->message }}
                                </h6>
                                @if($notification->hoSo)
                                    <p class="mb-1 text-muted">
                                        <small>
                                            <i class="fas fa-file-alt me-1"></i>
                                            Mã hồ sơ: <strong>{{ $notification->hoSo->ma_ho_so }}</strong>
                                        </small>
                                    </p>
                                @endif
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                @if(!$notification->is_read)
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Đánh dấu đã đọc">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa thông báo này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-bell-slash fa-3x mb-3"></i>
                <h4>Chưa có thông báo nào</h4>
                <p>Bạn chưa có thông báo nào. Các thông báo về lịch hẹn và trạng thái hồ sơ sẽ hiển thị ở đây.</p>
            </div>
        @endif
    </div>
</div>
@endsection

