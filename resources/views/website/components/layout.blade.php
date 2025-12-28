<!DOCTYPE html>
<html lang="en">

@include('website.components.head')
<style>
    .navbar {
        box-shadow:  none !important;
    }
 </style>
<body>

    <!-- Spinner Start -->
    <div id="spinner"
        class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->


    <!-- Navbar start -->
    @include('website.components.nav')
    <!-- Navbar End -->


    <!-- Modal Search Start -->
    @include('website.components.modal-search')
    <!-- Modal Search End -->


    {{-- <!-- Features Start -->
    @include('website.components.feature')
    <!-- Features End --> --}}

    @yield('content')
    @include('website.components.chat')

    <!-- Footer Start -->
    @include('website.components.footer')
    <!-- Footer End -->

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('website') }}/lib/easing/easing.min.js"></script>
    <script src="{{ asset('website') }}/lib/waypoints/waypoints.min.js"></script>
    <script src="{{ asset('website') }}/lib/owlcarousel/owl.carousel.min.js"></script>
    @yield('scripts')
    @stack('scripts')
    <!-- Template Javascript -->
    <script src="{{ asset('website') }}/js/main.js"></script>
    <script>
        const notifications = @json($notifications ?? []);
        let currentIndex = 0;


        function showNotification() {
            // Ensure the element exists before proceeding
            const notificationDiv = document.getElementById('notification-' + currentIndex);


            if (!notificationDiv) {
                console.log('Notification element not found');
                return; // Exit if element is not found
            }

            notificationDiv.classList.add('notification-visible');
            notificationDiv.style.opacity = 1; // Ensure the element is fully visible initially

            console.log(notificationDiv); // For debugging

            // Đặt thời gian để mờ đi và chuyển sang thông báo tiếp theo
            setTimeout(() => {
                // Mờ đi thông báo hiện tại
                notificationDiv.style.opacity = 0;

                // Sau khi mờ đi, ẩn thông báo để không chiếm không gian
                setTimeout(() => {
                    notificationDiv.style.display = 'none';

                    // Tăng chỉ số và quay lại đầu nếu đến cuối
                    currentIndex = (currentIndex + 1) % notifications.length;

                    // Hiện thông báo tiếp theo
                    showNotification();
                }, 1000); // Thời gian mờ đi (1s)
            }, 5000); // Hiện thông báo trong 5 giây
        }
        showNotification();
        // // Bắt đầu hiển thị thông báo đầu tiên
        // showNotification();

        $(function() {
            var INDEX = 0;
            var currentRoomId = null; // numeric id for ChatRoom
            var currentRoomUseRasa = false; // Lưu trạng thái Rasa của room hiện tại

            // Khởi tạo Select2 cho dropdown phường trong modal chat
            if ($('#phuong-select').length) {
                $('#phuong-select').select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Chọn phường --',
                    allowClear: true,
                    dropdownParent: $('#selectPhuongModal'),
                    language: {
                        noResults: function() {
                            return "Không tìm thấy phường nào";
                        },
                        searching: function() {
                            return "Đang tìm kiếm...";
                        }
                    }
                });
            }

            // Event handler cho modal - tự động chọn phường khi modal hiện ra
            $('#selectPhuongModal').on('shown.bs.modal', function () {
                const phuongSelect = $("#phuong-select");
                
                // Ưu tiên 1: Lấy từ tài khoản (nếu đã đăng nhập)
                let defaultDonViId = $('meta[name="user-don-vi-id"]').attr('content');
                
                // Ưu tiên 2: Lấy từ localStorage
                if (!defaultDonViId) {
                    defaultDonViId = localStorage.getItem('selected_don_vi_id');
                }
                
                // Ưu tiên 3: Chọn phường đầu tiên (bỏ qua option rỗng)
                if (!defaultDonViId) {
                    const firstOption = phuongSelect.find('option[value!=""]').first();
                    if (firstOption.length > 0) {
                        defaultDonViId = firstOption.val();
                    }
                }
                
                // Chọn phường và trigger change để load nhân viên
                if (defaultDonViId && phuongSelect.find(`option[value="${defaultDonViId}"]`).length > 0) {
                    // Với Select2, cần set value và trigger change
                    phuongSelect.val(defaultDonViId);
                    phuongSelect.trigger('change.select2'); // Trigger cho Select2
                    phuongSelect.trigger('change'); // Trigger cho event handler thông thường
                }
            });

            // Khi nhấn vào chat-circle
            $("#chat-circle").click(async function() {
                // Nếu chưa có room, hiện modal chọn phường
                if (!currentRoomId) {
                    // Hiện modal - event handler trên sẽ tự động chọn phường
                    $('#selectPhuongModal').modal('show');
                } else {
                    // Đã có room, toggle chat box
                    $("#chat-circle").toggle('scale');
                    $(".chat-box").toggle('scale');
                }
            });

            // Khi chọn phường, load danh sách nhân viên
            $("#phuong-select").change(async function() {
                const donViId = $(this).val();
                const nhanVienContainer = $("#nhan-vien-container");
                const nhanVienSelect = $("#nhan-vien-select");
                const nhanVienLoading = $("#nhan-vien-loading");

                if (!donViId) {
                    nhanVienContainer.hide();
                    nhanVienSelect.html('');
                    return;
                }

                // Hiện container và loading
                nhanVienContainer.show();
                nhanVienLoading.show();
                nhanVienSelect.prop('disabled', true);
                nhanVienSelect.html('<option value="">-- Đang tải... --</option>');

                try {
                    // Gọi API lấy danh sách nhân viên
                    // GET request không cần CSRF token
                    const response = await fetch(`/room-chats/available-officers?don_vi_id=${donViId}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                        },
                    });

                    // Kiểm tra content-type trước khi parse JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        // Nếu không phải JSON, lấy text để xem lỗi
                        const text = await response.text();
                        console.error('Response is not JSON:', text.substring(0, 200));
                        throw new Error(`Server trả về HTML thay vì JSON. Status: ${response.status}`);
                    }

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    let options = '';

                    if (data.success && data.officers && data.officers.length > 0) {
                        data.officers.forEach(function(officer) {
                            // Chỉ hiển thị trạng thái bận hay sẵn sàng
                            let statusText = '';
                            if (officer.is_busy) {
                                statusText = ` [🟡 Đang bận (${officer.active_chats_count} chat)]`;
                            } else {
                                statusText = ' [🟢 Sẵn sàng]';
                            }
                            
                            options += `<option value="${officer.id}">${officer.ho_ten}${statusText}</option>`;
                        });
                    } else {
                        options += '<option value="">-- Không có nhân viên khả dụng --</option>';
                    }

                    nhanVienSelect.html(options);
                } catch (e) {
                    console.error('Error loading officers:', e);
                    let errorMsg = '-- Lỗi khi tải danh sách nhân viên --';
                    if (e.message) {
                        errorMsg += ` (${e.message})`;
                    }
                    nhanVienSelect.html(`<option value="">${errorMsg}</option>`);
                } finally {
                    nhanVienLoading.hide();
                    nhanVienSelect.prop('disabled', false);
                }
            });

            // Khi click nút bắt đầu chat trong modal
            $("#btn-start-chat").click(async function() {
                const donViId = $("#phuong-select").val();
                const quanTriId = $("#nhan-vien-select").val();

                if (!donViId) {
                    alert('Vui lòng chọn phường!');
                    return;
                }

                // Lưu vào localStorage
                localStorage.setItem('selected_don_vi_id', donViId);

                // Đóng modal
                $('#selectPhuongModal').modal('hide');

                // Hiện loading
                $("#phuong-loading").show();

                // Bắt đầu chat với phường và nhân viên đã chọn
                await startChatRoomWithPhuong(donViId, quanTriId);

                // Ẩn loading
                $("#phuong-loading").hide();
            });

            // Hàm tạo tin nhắn
            function generate_message(msg, type) {
                INDEX++;
                var str = `<div id='cm-msg-${INDEX}' class="chat-msg ${type}">
          <span class="msg-avatar"></span>
          <div class="cm-msg-text">${msg}</div>
       </div>`;
                $(".chat-logs").append(str);
                $("#cm-msg-" + INDEX).hide().fadeIn(300);
                $(".chat-logs").stop().animate({
                    scrollTop: $(".chat-logs")[0].scrollHeight
                }, 1000);
            }

            // Xử lý khi người dùng nhập nội dung vào chat-input
            $("#chat-input").keypress(function(e) {
                if (e.which == 13) { // Phím Enter
                    e.preventDefault(); // Ngăn chặn hành động mặc định (reload trang)
                    var input = $("#chat-input").val().trim().toLowerCase();
                    $("#chat-input").val('');
                    if (input === "") return;

                    generate_message(input, 'self');
                    if (currentRoomId) {
                        sendRoomMessage(input);
                    }
                }
            });

            // Hiển thị chat box
            $(".chat-box-toggle").click(function() {
                $(".chat-box").hide();
                $("#chat-circle").show();
            });

            // Gửi form khi nhấn nút gửi
            $("#chat-submit").click(function(e) {
                e.preventDefault(); // Ngăn chặn gửi form mặc định

                let input = $("#chat-input").val().trim(); // Lấy giá trị từ input
                $("#chat-input").val(''); // Reset input

                if (input === "") return; // Thoát nếu input rỗng

                // Hiển thị tin nhắn người dùng
                generate_message(input, 'self');

                if (currentRoomId) {
                    sendRoomMessage(input);
                } else {
                    startChatRoom().then(function() {
                        if (currentRoomId) {
                            sendRoomMessage(input);
                        }
                    });
                }
            });

            // Hàm bắt đầu chat với phường đã chọn
            async function startChatRoomWithPhuong(donViId, quanTriId = null) {
                try {
                    const requestBody = {
                        user_name: $('meta[name="user-name"]').attr('content') || 'Khách',
                        user_email: $('meta[name="user-email"]').attr('content') || null,
                        user_phone: $('meta[name="user-phone"]').attr('content') || null,
                        don_vi_id: parseInt(donViId),
                    };

                    // Nếu có chọn nhân viên, thêm vào request
                    if (quanTriId) {
                        requestBody.quan_tri_id = parseInt(quanTriId);
                    }

                    const response = await fetch('/room-chats/start', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        },
                        body: JSON.stringify(requestBody),
                    });
                    if (!response.ok) throw new Error('Không tạo được phòng chat');
                    const room = await response.json();
                    currentRoomId = room.id;
                    currentRoomUseRasa = room.use_rasa || false; // Lưu trạng thái Rasa
                    
                    // Cập nhật header chat
                    if (room.use_rasa) {
                        $('#chat-header-title').text('Chatbot Rasa');
                    } else if (room.quan_tri_id) {
                        $('#chat-header-title').text('Chat với cán bộ');
                    } else {
                        $('#chat-header-title').text('ChatBot');
                    }
                    
                    // Hiện chat box
                    $("#chat-circle").hide();
                    $(".chat-box").show();
                    
                    subscribeRoomChannel(currentRoomId);
                    // Load các tin nhắn cũ
                    await loadRoomMessages(currentRoomId);
                } catch (e) {
                    console.error(e);
                    alert('Có lỗi xảy ra khi tạo phòng chat. Vui lòng thử lại!');
                }
            }

            // Hàm bắt đầu chat (giữ lại để tương thích)
            async function startChatRoom() {
                // Lấy don_vi_id từ localStorage
                const donViId = localStorage.getItem('selected_don_vi_id');
                if (donViId) {
                    await startChatRoomWithPhuong(donViId);
                } else {
                    // Nếu chưa có phường, hiện modal
                    $('#selectPhuongModal').modal('show');
                }
            }

            // Reset nhân viên khi modal được mở lại
            $('#selectPhuongModal').on('show.bs.modal', function() {
                $("#nhan-vien-container").hide();
                $("#nhan-vien-select").html('');
            });

            async function loadRoomMessages(roomId) {
                try {
                    const res = await fetch(`/room-chats/${roomId}/messages`);
                    if (!res.ok) return;
                    const messages = await res.json();
                    messages.forEach(function(m) {
                        const messageText = m.tin_nhan || m.message || '';
                        const senderType = m.loai_nguoi_gui || m.sender_type || 'user';
                        generate_message(messageText, senderType === 'admin' ? 'user' : 'self');
                    });
                } catch (e) {
                    console.error(e);
                }
            }

            async function sendRoomMessage(message) {
                try {
                    const response = await fetch(`/room-chats/${currentRoomId}/messages`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            // Để Laravel loại trừ client hiện tại khi broadcast toOthers()
                            'X-Socket-Id': (window._pusher && window._pusher.connection && window._pusher.connection.socket_id) ? window._pusher.connection.socket_id : ''
                        },
                        body: JSON.stringify({ message }),
                    });
                    if (!response.ok) throw new Error('Gửi tin nhắn thất bại');
                    
                    // Xử lý response để hiển thị tin nhắn bot ngay lập tức
                    const data = await response.json();
                    console.log('Message response:', data);
                    
                    // Nếu có bot_message trong response, hiển thị ngay
                    if (data.bot_message && data.bot_message.tin_nhan) {
                        generate_message(data.bot_message.tin_nhan, 'user'); // 'user' = tin nhắn từ bot/admin
                    }
                } catch (e) {
                    console.error('Error sending message:', e);
                }
            }

            function subscribeRoomChannel(roomId) {
                if (!window._pusher) {
                    console.error('Pusher not initialized');
                    return;
                }
                const channelName = 'chat-room.' + roomId;
                console.log('Subscribing to channel:', channelName);
                const channel = window._pusher.subscribe(channelName);
                
                channel.bind('pusher:subscription_succeeded', function() {
                    console.log('Successfully subscribed to channel:', channelName);
                });
                
                channel.bind('pusher:subscription_error', function(status) {
                    console.error('Subscription error:', status);
                });
                
                // Bind với event name - Laravel broadcast với tên đã định nghĩa
                channel.bind('NewChatMessage', function(data) {
                    console.log('Received NewChatMessage:', data);
                    // Tin nhắn từ admin hiển thị dưới dạng 'user'
                    const messageText = data.tin_nhan || data.message || '';
                    const senderType = data.loai_nguoi_gui || data.sender_type || 'user';
                    if (messageText) {
                        generate_message(messageText, senderType === 'admin' ? 'user' : 'self');
                    }
                });
                
                // Fallback: bind với tên class đầy đủ (nếu broadcastAs không được sử dụng)
                channel.bind('App\\Events\\NewChatMessage', function(data) {
                    console.log('Received App\\Events\\NewChatMessage:', data);
                    const messageText = data.tin_nhan || data.message || '';
                    const senderType = data.loai_nguoi_gui || data.sender_type || 'user';
                    if (messageText) {
                        generate_message(messageText, senderType === 'admin' ? 'user' : 'self');
                    }
                });
                
                // Bind event khi cán bộ vào room (Rasa dừng)
                channel.bind('AdminJoinedRoom', function(data) {
                    console.log('Admin joined room, Rasa stopped:', data);
                    // Cập nhật trạng thái Rasa
                    currentRoomUseRasa = false;
                    // Cập nhật header
                    $('#chat-header-title').text('Chat với cán bộ');
                    // Hiển thị thông báo
                    generate_message('Cán bộ đã tham gia chat. Rasa chatbot đã dừng.', 'user');
                });
            }

            // Đánh dấu phòng đóng khi người dùng đóng tab hoặc reload
            window.addEventListener('beforeunload', function() {
                if (!currentRoomId) return;
                try {
                    fetch(`/room-chats/${currentRoomId}/leave`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        },
                        keepalive: true,
                        body: JSON.stringify({ reason: 'unload' }),
                    });
                } catch (e) {
                    // ignore
                }
            });






        });
    </script>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Khởi tạo Pusher ở website (client người dùng)
        const pusher = new Pusher('0acb86837b3ecb04fbc8', {
            cluster: 'ap1',
            forceTLS: true
        });
        window._pusher = pusher;
    </script>

</body>

</html>
