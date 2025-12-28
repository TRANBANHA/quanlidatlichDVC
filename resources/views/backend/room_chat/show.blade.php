@extends('backend.components.layout')
@section('title')
    Chat với {{ $room->ten_nguoi_dung }}
@endsection
@section('content')
    <div class="container py-4" style="min-height: calc(100vh - 120px);">
        <div class="row h-100">
            <div class="col-12">
                <div class="card mb-4" style="height: calc(100vh - 180px); min-height: 600px;">
                    <div class="card-header pb-3 pt-3 d-flex justify-content-between align-items-center" 
                         style="background: #f8f9fa; border-bottom: 1px solid #ebeef4; min-height: 70px;">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3" 
                                 style="width: 40px; height: 40px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $room->ten_nguoi_dung }}</h6>
                                <small class="text-muted">
                                    @if($room->email_nguoi_dung)
                                        <i class="fas fa-envelope me-1"></i> {{ $room->email_nguoi_dung }}
                                    @endif
                                    @if($room->so_dien_thoai)
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-phone me-1"></i> {{ $room->so_dien_thoai }}
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('admin.room-chats.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0 d-flex flex-column" style="height: calc(100% - 70px); overflow: hidden;">
                        <div id="chat-messages" class="flex-grow-1 overflow-auto px-4 py-3" style="max-height: calc(100vh - 320px);">
                            @forelse($messages as $message)
                                <div class="message-wrapper mb-4 {{ $message->loai_nguoi_gui == 'admin' ? 'text-end' : 'text-start' }}">
                                    <div class="message d-inline-block {{ $message->loai_nguoi_gui == 'admin' ? 'message-out' : 'message-in' }}"
                                         style="max-width: 75%; border-radius: 15px; position: relative;">
                                        <div class="message-content p-3">
                                            @if($message->loai_nguoi_gui != 'admin')
                                                <div class="fw-semibold text-primary mb-1" style="font-size: 0.875rem;">
                                                    {{ $message->ten_nguoi_gui }}
                                                </div>
                                            @endif
                                            <div class="message-text">
                                                {{ $message->tin_nhan }}
                                            </div>
                                            <div class="message-time mt-1" style="font-size: 0.75rem; opacity: 0.7;">
                                                <i class="far fa-clock me-1"></i>
                                                {{ $message->created_at->format('H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-comments fa-3x"></i>
                                    </div>
                                    <h6>Chưa có tin nhắn nào</h6>
                                    <p class="small">Hãy bắt đầu cuộc trò chuyện!</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="chat-input px-4 py-3" style="background: #f8f9fa; border-top: 1px solid #ebeef4;">
                            <form id="chat-form" class="mb-0">
                                @csrf
                                <div class="input-group">
                                    <input type="text" class="form-control" id="message-input" 
                                           placeholder="Nhập tin nhắn..." 
                                           style="border-radius: 20px 0 0 20px;">
                                    <button type="submit" class="btn btn-primary px-4" 
                                            style="border-radius: 0 20px 20px 0;">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .message-in {
        background: #f0f2f5;
        color: #333;
        margin-right: auto;
        margin-left: 0;
    }

    .message-out {
        background: #0084ff;
        color: white;
        margin-left: auto;
        margin-right: 0;
    }
    
    .message {
        word-wrap: break-word;
        word-break: break-word;
    }

    .message-out .message-time {
        color: rgba(255,255,255,0.8);
    }

    /* Custom Scrollbar */
    #chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    #chat-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #chat-messages::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    #chat-messages::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Animations */
    .message-wrapper {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Input Focus Effects */
    #message-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(0,132,255,0.15);
        border-color: #0084ff;
    }

    .btn-primary {
        background-color: #0084ff;
        border-color: #0084ff;
    }

    .btn-primary:hover {
        background-color: #0073e6;
        border-color: #0073e6;
    }
    </style>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        const roomId = {{ $room->id }};
        const adminId = {{ $admin->id }};

        // Kết nối Pusher
        const pusher = new Pusher('0acb86837b3ecb04fbc8', {
            cluster: 'ap1',
            forceTLS: true
        });

        // Subscribe vào channel của phòng chat
        const channel = pusher.subscribe('chat-room.' + roomId);
        
        // Lắng nghe tin nhắn mới - bind theo tên event đã đặt với broadcastAs()
        channel.bind('NewChatMessage', function(data) {
            if (data && data.tin_nhan) {
                addMessageToChat(data);
            }
        });
        // Fallback: bind theo tên class đầy đủ nếu cần
        channel.bind('App\\Events\\NewChatMessage', function(data) {
            if (data && data.tin_nhan) {
                addMessageToChat(data);
            }
        });

        // Form gửi tin nhắn
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');

        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const message = messageInput.value.trim();
            if (!message) return;

            // Disable input và button trong khi gửi
            messageInput.disabled = true;
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                const response = await fetch(`/admin/room-chats/${roomId}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Socket-Id': pusher.connection.socket_id || ''
                    },
                    body: JSON.stringify({ message: message }),
                });

                const data = await response.json();
                const messagePayload = data.message || data;
                if (messagePayload && (messagePayload.id || messagePayload.tin_nhan)) {
                    messageInput.value = '';
                    addMessageToChat({
                        loai_nguoi_gui: 'admin',
                        ten_nguoi_gui: '{{ $admin->ho_ten ?? 'Admin' }}',
                        tin_nhan: messagePayload.tin_nhan || messagePayload.message || message,
                        created_at: messagePayload.created_at || new Date()
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Không thể gửi tin nhắn');
            } finally {
                // Re-enable input và button
                messageInput.disabled = false;
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                messageInput.focus();
            }
        });

        // Thêm tin nhắn vào chat
        function addMessageToChat(data) {
            const chatMessages = document.getElementById('chat-messages');
            const isAdmin = data.loai_nguoi_gui === 'admin';
            const messageTime = data.created_at 
                ? new Date(data.created_at).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'})
                : new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});

            const messageHtml = `
                <div class="message-wrapper mb-4 ${isAdmin ? 'text-end' : 'text-start'}">
                    <div class="message d-inline-block ${isAdmin ? 'message-out' : 'message-in'}" 
                         style="max-width: 75%; border-radius: 15px;">
                        <div class="message-content p-3">
                            ${!isAdmin ? `
                                <div class="fw-semibold text-primary mb-1" style="font-size: 0.875rem;">
                                    ${data.ten_nguoi_gui || data.sender_name || 'Người dùng'}
                                </div>
                            ` : ''}
                            <div class="message-text">
                                ${data.tin_nhan || data.message}
                            </div>
                            <div class="message-time mt-1" style="font-size: 0.75rem; opacity: 0.7;">
                                <i class="far fa-clock me-1"></i>${messageTime}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            chatMessages.insertAdjacentHTML('beforeend', messageHtml);
            scrollToBottom();
        }

        // Cuộn xuống cuối
        function scrollToBottom() {
            const chatMessages = document.getElementById('chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Play sound khi có tin nhắn mới
        function playNotificationSound() {
            const audio = new Audio('/sounds/notification.mp3');
            audio.play().catch(() => {}); // Catch để tránh lỗi nếu browser chặn autoplay
        }

        // Auto scroll xuống cuối khi load trang
        document.addEventListener('DOMContentLoaded', scrollToBottom);

        // Bắt sự kiện nhấn Enter để gửi tin nhắn
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
        });

        // Focus vào input khi vào trang
        messageInput.focus();
    </script>
@endsection

