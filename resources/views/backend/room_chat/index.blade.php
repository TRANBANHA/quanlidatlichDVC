@extends('backend.components.layout')
@section('title')
    Quản lý Chat
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">
                                    <i class="fas fa-comments text-primary me-2"></i>
                                    Danh sách phòng chat
                                </h5>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary" onclick="randomClaimRoom()">
                                    <i class="fas fa-random me-2"></i>
                                    Nhận ngẫu nhiên
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body px-0 pt-0">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Người dùng</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Trạng thái</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quản trị viên</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Hoạt động cuối</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody id="rooms-list">
                                    @foreach($rooms as $room)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-2">
                                                    <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-user text-secondary"></i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0">{{ $room->ten_nguoi_dung }}</h6>
                                                        <div class="text-xs text-secondary mb-0">
                                                            @if($room->email_nguoi_dung)
                                                                <i class="fas fa-envelope me-1"></i> {{ $room->email_nguoi_dung }}
                                                            @endif
                                                            @if($room->so_dien_thoai)
                                                                <br><i class="fas fa-phone me-1"></i> {{ $room->so_dien_thoai }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($room->trang_thai == 'waiting')
                                                    <span class="badge badge-sm bg-gradient-warning">
                                                        <i class="fas fa-clock me-1"></i> Chờ
                                                    </span>
                                                @elseif($room->trang_thai == 'active')
                                                    <span class="badge badge-sm bg-gradient-success">
                                                        <i class="fas fa-check-circle me-1"></i> Đang hoạt động
                                                    </span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-secondary">
                                                        <i class="fas fa-times-circle me-1"></i> Đã đóng
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($room->quan_tri_id)
                                                    <span class="text-xs">Admin #{{ $room->quan_tri_id }}</span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-danger">
                                                        <i class="fas fa-user-times me-1"></i> Chưa có
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-xs text-secondary">
                                                    @if($room->hoat_dong_cuoi)
                                                        <i class="far fa-clock me-1"></i>
                                                        {{ $room->hoat_dong_cuoi->format('H:i d/m/Y') }}
                                                    @else
                                                        -
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex justify-content-end px-3 gap-2">
                                                    <a href="{{ route('admin.room-chats.show', $room->id) }}" 
                                                       class="btn btn-sm btn-primary px-3">
                                                        <i class="fas fa-comments me-1"></i> Chat
                                                    </a>
                                                    @if(!$room->quan_tri_id)
                                                        <button class="btn btn-sm btn-success px-3" 
                                                                onclick="claimRoom({{ $room->id }})">
                                                            <i class="fas fa-hand-paper me-1"></i> Nhận
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .avatar-sm {
        width: 36px;
        height: 36px;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.45em 0.9em;
        font-weight: 500;
    }

    .bg-gradient-success {
        background-image: linear-gradient(310deg, #2dce89 0%, #26adab 100%);
    }

    .bg-gradient-warning {
        background-image: linear-gradient(310deg, #fb6340 0%, #fbb140 100%);
    }

    .bg-gradient-danger {
        background-image: linear-gradient(310deg, #f5365c 0%, #f56036 100%);
    }

    .bg-gradient-secondary {
        background-image: linear-gradient(310deg, #627594 0%, #a8b8d8 100%);
    }

    .table > :not(caption) > * > * {
        padding: 0.5rem 0;
    }

    .btn {
        text-transform: none;
        font-weight: 500;
    }

    .btn-sm {
        padding: 0.45rem 1.2rem;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    /* Hover effect for table rows */
    #rooms-list tr {
        transition: all 0.2s ease;
    }

    #rooms-list tr:hover {
        background-color: #f8f9fa;
    }

    /* Loading animation for buttons */
    .btn-loading {
        position: relative;
        pointer-events: none;
    }

    .btn-loading:after {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin: -8px 0 0 -8px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 0.8s infinite linear;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Animation for new/updated rows */
    .row-updated {
        animation: highlight 2s ease-out;
    }

    @keyframes highlight {
        0% { background-color: rgba(0, 132, 255, 0.1); }
        100% { background-color: transparent; }
    }
    </style>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Subscribe kênh chờ để nhận thông báo phòng mới/ tin nhắn đầu tiên
        const pusher = new Pusher('0acb86837b3ecb04fbc8', { cluster: 'ap1', forceTLS: true });
        const waitingChannel = pusher.subscribe('admin-waiting-rooms');
        waitingChannel.bind('NewChatMessage', function() {
            // Khi có tin nhắn mới từ phòng chưa được nhận → cập nhật danh sách tại chỗ
            refreshRoomsList();
        });
        // Fallback bind theo tên class đầy đủ
        waitingChannel.bind('App\\Events\\NewChatMessage', function() {
            refreshRoomsList();
        });

        async function refreshRoomsList() {
            try {
                const res = await fetch('/admin/room-chats?ajax=1', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) return;
                const rooms = await res.json();
                updateRoomsTable(rooms);
            } catch (e) {
                console.error('Refresh rooms failed', e);
            }
        }

        function updateRoomsTable(rooms) {
            const tbody = document.getElementById('rooms-list');
            if (!tbody) return;

            const rowsHtml = rooms.map(room => {
                const statusBadge = getStatusBadge(room.trang_thai);
                const adminInfo = getAdminInfo(room.quan_tri_id);
                const lastActivity = formatLastActivity(room.hoat_dong_cuoi);
                const actionButtons = getActionButtons(room);

                return `
                    <tr class="row-updated">
                        <td>
                            <div class="d-flex px-3 py-2">
                                <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fas fa-user text-secondary"></i>
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0">${room.ten_nguoi_dung || '-'}</h6>
                                    <div class="text-xs text-secondary mb-0">
                                        ${room.email_nguoi_dung ? `<i class="fas fa-envelope me-1"></i> ${room.email_nguoi_dung}` : ''}
                                        ${room.so_dien_thoai ? `<br><i class="fas fa-phone me-1"></i> ${room.so_dien_thoai}` : ''}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>${statusBadge}</td>
                        <td>${adminInfo}</td>
                        <td>${lastActivity}</td>
                        <td class="align-middle">
                            <div class="d-flex justify-content-end px-3 gap-2">
                                ${actionButtons}
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            tbody.innerHTML = rowsHtml;
        }

        function getStatusBadge(status) {
            const badges = {
                waiting: `<span class="badge badge-sm bg-gradient-warning">
                            <i class="fas fa-clock me-1"></i> Chờ
                         </span>`,
                active: `<span class="badge badge-sm bg-gradient-success">
                            <i class="fas fa-check-circle me-1"></i> Đang hoạt động
                         </span>`,
                default: `<span class="badge badge-sm bg-gradient-secondary">
                            <i class="fas fa-times-circle me-1"></i> Đã đóng
                         </span>`
            };
            return badges[status] || badges.default;
        }

        function getAdminInfo(adminId) {
            return adminId 
                ? `<span class="text-xs">Admin #${adminId}</span>`
                : `<span class="badge badge-sm bg-gradient-danger">
                       <i class="fas fa-user-times me-1"></i> Chưa có
                   </span>`;
        }

        function formatLastActivity(timestamp) {
            if (!timestamp) return '<span class="text-xs text-secondary">-</span>';
            const date = new Date(timestamp);
            return `
                <span class="text-xs text-secondary">
                    <i class="far fa-clock me-1"></i>
                    ${date.toLocaleString('vi-VN', {
                        hour: '2-digit',
                        minute: '2-digit',
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    })}
                </span>
            `;
        }

        function getActionButtons(room) {
            const chatBtn = `
                <a href="/admin/room-chats/${room.id}" 
                   class="btn btn-sm btn-primary px-3">
                    <i class="fas fa-comments me-1"></i> Chat
                </a>
            `;
            
            const claimBtn = !room.quan_tri_id ? `
                <button class="btn btn-sm btn-success px-3" 
                        onclick="claimRoom(${room.id})">
                    <i class="fas fa-hand-paper me-1"></i> Nhận
                </button>
            ` : '';

            return chatBtn + claimBtn;
        }

        async function claimRoom(roomId) {
            const btn = event.target.closest('button');
            if (!btn) return;

            try {
                btn.classList.add('btn-loading');
                btn.disabled = true;

                const response = await fetch(`/admin/room-chats/${roomId}/claim`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                });

                const data = await response.json();
                if (data.id) {
                    window.location.href = `/admin/room-chats/${roomId}`;
                } else {
                    showNotification('Không thể nhận phòng chat', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Có lỗi xảy ra', 'error');
            } finally {
                btn.classList.remove('btn-loading');
                btn.disabled = false;
            }
        }

        async function randomClaimRoom() {
            const btn = event.target.closest('button');
            if (!btn) return;

            try {
                btn.classList.add('btn-loading');
                btn.disabled = true;

                const response = await fetch('/admin/room-chats/random-claim', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                });

                const data = await response.json();
                if (data.id) {
                    window.location.href = `/admin/room-chats/${data.id}`;
                } else {
                    showNotification('Không có phòng chat đang chờ', 'warning');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Có lỗi xảy ra', 'error');
            } finally {
                btn.classList.remove('btn-loading');
                btn.disabled = false;
            }
        }

        function showNotification(message, type = 'info') {
            // Implement your notification system here
            alert(message);
        }

        // Auto refresh mỗi 20 giây
        setInterval(refreshRoomsList, 20000);

        // Initial load
        document.addEventListener('DOMContentLoaded', refreshRoomsList);
    </script>
@endsection

