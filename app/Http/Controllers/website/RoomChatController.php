<?php

namespace App\Http\Controllers\website;

use App\Events\NewChatMessage;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Admin;
use App\Models\ServiceSchedule;
use App\Models\ServiceScheduleStaff;
use App\Services\RasaService;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RoomChatController extends Controller
{
    protected $rasaService;
    protected $chatService;

    public function __construct(RasaService $rasaService, ChatService $chatService)
    {
        $this->rasaService = $rasaService;
        $this->chatService = $chatService;
    }

    public function start(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'user_email' => 'nullable|email|max:255',
            'user_phone' => 'nullable|string|max:50',
            'don_vi_id' => 'nullable|exists:don_vi,id', // Phường được chọn
            'quan_tri_id' => 'nullable|exists:quan_tri_vien,id', // Cán bộ được chọn (optional)
        ]);

        $donViId = $request->input('don_vi_id');
        $selectedAdminId = $request->input('quan_tri_id');
        
        $room = ChatRoom::create([
            'ma_phong' => ChatRoom::generateRoomId(),
            'nguoi_dung_id' => Auth::id(),
            'quan_tri_id' => null,
            'don_vi_id' => $donViId,
            'ten_nguoi_dung' => $request->input('user_name'),
            'email_nguoi_dung' => $request->input('user_email'),
            'so_dien_thoai' => $request->input('user_phone'),
            'trang_thai' => 'waiting',
            'use_rasa' => true, // Mặc định dùng Rasa khi chưa có cán bộ
            'hoat_dong_cuoi' => now(),
        ]);

        // Nếu người dùng đã chọn cán bộ cụ thể
        if ($selectedAdminId) {
            $selectedAdmin = Admin::find($selectedAdminId);
            if ($selectedAdmin && $selectedAdmin->don_vi_id == $donViId) {
                // Gán nhân viên được chọn nhưng VẪN BẬT RASA để AI chat thay
                // AI sẽ chat cho đến khi nhân viên vào room
                $room->update([
                    'quan_tri_id' => $selectedAdmin->id,
                    'trang_thai' => 'waiting',
                    'use_rasa' => true, // AI chat thay cho đến khi nhân viên vào
                    'hoat_dong_cuoi' => now(),
                ]);
                
                // Gửi tin nhắn chào mừng từ Rasa ngay sau khi tạo room
                $this->chatService->sendRasaWelcomeMessage($room, Auth::user());
            } else {
                // Nhân viên không hợp lệ → chỉ dùng Rasa
                $room->update([
                    'use_rasa' => true,
                    'trang_thai' => 'waiting',
                ]);
                
                // Gửi tin nhắn chào mừng từ Rasa
                $this->chatService->sendRasaWelcomeMessage($room, Auth::user());
            }
        } else {
            // Không chọn nhân viên cụ thể → Random cán bộ theo phường và thứ trong tuần
            $assignedAdmin = $this->getRandomOfficerByWardAndDay($donViId);

            if ($assignedAdmin) {
                // Có cán bộ rảnh → gán và tắt Rasa (vì cán bộ đã sẵn sàng)
                $room->update([
                    'quan_tri_id' => $assignedAdmin->id,
                    'trang_thai' => 'active',
                    'use_rasa' => false,
                    'hoat_dong_cuoi' => now(),
                ]);

                // Gửi tin nhắn chào mừng từ cán bộ
                $welcomeMessage = ChatMessage::create([
                    'phong_chat_id' => $room->id,
                    'nguoi_gui_id' => $assignedAdmin->id,
                    'loai_nguoi_gui' => 'admin',
                    'ten_nguoi_gui' => $assignedAdmin->ho_ten ?? 'Cán bộ',
                    'tin_nhan' => 'Xin chào! Tôi là cán bộ ' . ($assignedAdmin->donVi ? $assignedAdmin->donVi->ten_don_vi : '') . '. Tôi sẽ hỗ trợ bạn ngay bây giờ.',
                    'loai_tin_nhan' => 'text',
                    'da_doc' => false,
                ]);

                broadcast(new NewChatMessage($welcomeMessage))->toOthers();
            } else {
                // Không có cán bộ rảnh → dùng Rasa (AI chat thay cán bộ)
                // Gửi tin nhắn chào mừng từ Rasa ngay sau khi tạo room
                $room->update([
                    'use_rasa' => true,
                    'trang_thai' => 'waiting',
                ]);
                
                // Gửi tin nhắn chào mừng từ Rasa
                $this->chatService->sendRasaWelcomeMessage($room, Auth::user());
            }
        }

        return response()->json($room);
    }

    /**
     * Lấy cán bộ random theo phường và thứ trong tuần
     */
    private function getRandomOfficerByWardAndDay($donViId)
    {
        if (!$donViId) {
            return null;
        }

        // Lấy thứ trong tuần hiện tại (1=Thứ 2, 2=Thứ 3, ..., 7=Chủ nhật)
        $thuTrongTuan = Carbon::now()->dayOfWeekIso;
        
        // Tìm các schedule có thứ này và có cán bộ được phân công
        $schedules = ServiceSchedule::where('thu_trong_tuan', $thuTrongTuan)
            ->where('trang_thai', true)
            ->get();

        $availableOfficerIds = [];
        
        // Lấy danh sách cán bộ được phân công vào các schedule này
        foreach ($schedules as $schedule) {
            $staffIds = ServiceScheduleStaff::where('schedule_id', $schedule->id)
                ->pluck('can_bo_id')
                ->toArray();
            $availableOfficerIds = array_merge($availableOfficerIds, $staffIds);
        }

        // Lọc chỉ lấy cán bộ của phường này
        $officersBySchedule = Admin::whereIn('id', $availableOfficerIds)
            ->where('don_vi_id', $donViId)
            ->where('quyen', Admin::CAN_BO)
            ->get();

        // Nếu có cán bộ từ schedule, random chọn 1 cán bộ không đang chat
        if ($officersBySchedule->count() > 0) {
            foreach ($officersBySchedule->shuffle() as $officer) {
                $activeRooms = ChatRoom::where('quan_tri_id', $officer->id)
                    ->where('trang_thai', 'active')
                    ->where('hoat_dong_cuoi', '>', now()->subMinutes(5))
                    ->count();

                // Kiểm tra cán bộ có đang chat với AI (Rasa) không
                $rasaRooms = ChatRoom::where('quan_tri_id', $officer->id)
                    ->where('use_rasa', true)
                    ->where('trang_thai', '!=', 'closed')
                    ->where('hoat_dong_cuoi', '>', now()->subMinutes(5))
                    ->count();

                if ($activeRooms == 0 && $rasaRooms == 0) {
                    return $officer;
                }
            }
        }

        // Fallback: Nếu không có cán bộ từ schedule, tìm cán bộ của phường
        $canBos = Admin::where('don_vi_id', $donViId)
            ->where('quyen', Admin::CAN_BO)
            ->get();

        foreach ($canBos->shuffle() as $canBo) {
            $activeRooms = ChatRoom::where('quan_tri_id', $canBo->id)
                ->where('trang_thai', 'active')
                ->where('hoat_dong_cuoi', '>', now()->subMinutes(5))
                ->count();

            // Kiểm tra cán bộ có đang chat với AI (Rasa) không
            $rasaRooms = ChatRoom::where('quan_tri_id', $canBo->id)
                ->where('use_rasa', true)
                ->where('trang_thai', '!=', 'closed')
                ->where('hoat_dong_cuoi', '>', now()->subMinutes(5))
                ->count();

            if ($activeRooms == 0 && $rasaRooms == 0) {
                return $canBo;
            }
        }

        // Nếu không tìm thấy cán bộ rảnh, tìm admin phường hoặc admin tổng
        $admins = Admin::where('don_vi_id', $donViId)
            ->whereIn('quyen', [Admin::ADMIN_PHUONG, Admin::ADMIN])
            ->get();

        foreach ($admins->shuffle() as $admin) {
            $activeRooms = ChatRoom::where('quan_tri_id', $admin->id)
                ->where('trang_thai', 'active')
                ->where('hoat_dong_cuoi', '>', now()->subMinutes(5))
                ->count();

            // Kiểm tra admin có đang chat với AI (Rasa) không
            $rasaRooms = ChatRoom::where('quan_tri_id', $admin->id)
                ->where('use_rasa', true)
                ->where('trang_thai', '!=', 'closed')
                ->where('hoat_dong_cuoi', '>', now()->subMinutes(5))
                ->count();

            if ($activeRooms == 0 && $rasaRooms == 0) {
                return $admin;
            }
        }

        return null;
    }

    /**
     * Lấy danh sách cán bộ có thể chọn theo phường và thứ
     */
    public function getAvailableOfficers(Request $request)
    {
        // Đảm bảo request được coi là JSON request
        $request->headers->set('Accept', 'application/json');
        
        \Log::info('getAvailableOfficers called', [
            'don_vi_id' => $request->input('don_vi_id'),
            'all_params' => $request->all(),
            'method' => $request->method(),
            'wants_json' => $request->wantsJson(),
            'expects_json' => $request->expectsJson(),
        ]);
        
        try {
            // Validate và trả về JSON nếu có lỗi
            $validated = $request->validate([
                'don_vi_id' => 'required|exists:don_vi,id',
            ], [
                'don_vi_id.required' => 'Vui lòng chọn phường',
                'don_vi_id.exists' => 'Phường không tồn tại',
            ]);

            $donViId = $request->input('don_vi_id');
            $thuTrongTuan = $request->input('thu_trong_tuan', Carbon::now()->dayOfWeekIso);

            // Lấy tất cả cán bộ và admin phường của phường này (không cần check login)
            $officers = Admin::where('don_vi_id', $donViId)
                ->whereIn('quyen', [Admin::CAN_BO, Admin::ADMIN_PHUONG])
                ->get()
                ->map(function($officer) use ($thuTrongTuan) {
                    // Lấy ngày làm việc của nhân viên (từ schedule)
                    $scheduleIds = ServiceScheduleStaff::where('can_bo_id', $officer->id)
                        ->pluck('schedule_id')
                        ->toArray();
                    
                    $workingDays = ServiceSchedule::whereIn('id', $scheduleIds)
                        ->where('trang_thai', true)
                        ->pluck('thu_trong_tuan')
                        ->unique()
                        ->sort()
                        ->map(function($thu) {
                            $days = [1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7', 7 => 'Chủ nhật'];
                            return $days[$thu] ?? "Thứ $thu";
                        })
                        ->values()
                        ->toArray();

                    // Kiểm tra nhân viên có làm việc hôm nay không
                    $isWorkingToday = in_array($thuTrongTuan, ServiceSchedule::whereIn('id', $scheduleIds)
                        ->where('trang_thai', true)
                        ->pluck('thu_trong_tuan')
                        ->toArray());

                    // Kiểm tra cán bộ có đang bận với chat nào không
                    $activeRooms = ChatRoom::where('quan_tri_id', $officer->id)
                        ->where('trang_thai', 'active')
                        ->where('hoat_dong_cuoi', '>', now()->subMinutes(5))
                        ->count();

                    // Kiểm tra cán bộ có đang chat với AI (Rasa) không
                    $rasaRooms = ChatRoom::where('quan_tri_id', $officer->id)
                        ->where('use_rasa', true)
                        ->where('trang_thai', '!=', 'closed')
                        ->where('hoat_dong_cuoi', '>', now()->subMinutes(5))
                        ->count();

                    $totalActiveChats = $activeRooms + $rasaRooms;
                    $isBusy = $totalActiveChats > 0;

                    return [
                        'id' => $officer->id,
                        'ho_ten' => $officer->ho_ten,
                        'email' => $officer->email,
                        'so_dien_thoai' => $officer->so_dien_thoai,
                        'don_vi' => $officer->donVi ? $officer->donVi->ten_don_vi : '',
                        'working_days' => $workingDays, // Ngày làm việc
                        'is_working_today' => $isWorkingToday, // Có làm việc hôm nay không
                        'thu_hom_nay' => $thuTrongTuan, // Thứ hôm nay
                        'is_busy' => $isBusy, // Có đang bận không
                        'active_chats_count' => $totalActiveChats, // Số lượng chat đang active
                    ];
                });

            \Log::info('getAvailableOfficers success', [
                'don_vi_id' => $donViId,
                'officers_count' => $officers->count(),
            ]);

            return response()->json([
                'success' => true,
                'officers' => $officers,
                'thu_trong_tuan' => $thuTrongTuan,
                'count' => $officers->count(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors đã được xử lý ở trên
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error getting available officers: ' . $e->getMessage(), [
                'don_vi_id' => $request->input('don_vi_id'),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải danh sách nhân viên: ' . $e->getMessage(),
                'officers' => [],
            ], 500);
        }
    }

    public function messages(ChatRoom $room)
    {
        $messages = $room->messages()->orderBy('created_at')->get();
        return response()->json($messages);
    }

    public function send(Request $request, ChatRoom $room)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = Auth::user();
        $userMessage = $request->input('message');

        // Lưu tin nhắn người dùng
        $message = ChatMessage::create([
            'phong_chat_id' => $room->id,
            'nguoi_gui_id' => $user ? $user->id : null,
            'loai_nguoi_gui' => 'user',
            'ten_nguoi_gui' => $user ? ($user->ho_ten ?? $user->email) : $room->ten_nguoi_dung,
            'tin_nhan' => $userMessage,
            'loai_tin_nhan' => 'text',
            'da_doc' => false,
        ]);

        // QUAN TRỌNG: Refresh room để lấy trạng thái mới nhất (có thể cán bộ vừa vào)
        $room->refresh();
        $room->update(['hoat_dong_cuoi' => now()]);

        // Kiểm tra xem cán bộ đã gửi tin nhắn thực sự chưa (sau khi người dùng gửi tin nhắn)
        $adminHasSentMessageAfterUser = ChatMessage::where('phong_chat_id', $room->id)
            ->where('loai_nguoi_gui', 'admin')
            ->where('nguoi_gui_id', $room->quan_tri_id)
            ->where('created_at', '>', $message->created_at)
            ->exists();

        // LOGIC ĐƠN GIẢN:
        // 1. Nếu use_rasa = true → LUÔN gọi Rasa (AI chat)
        // 2. Nếu use_rasa = false VÀ cán bộ đã gửi tin nhắn sau tin nhắn người dùng → gửi cho cán bộ
        // 3. Nếu use_rasa = false VÀ cán bộ chưa gửi tin nhắn → vẫn gọi Rasa (AI chat thay)
        
        // Nếu đang dùng Rasa → LUÔN gọi Rasa
        if ($room->use_rasa) {
            // Đang dùng Rasa → gửi đến Rasa chatbot
            $room->update(['trang_thai' => 'waiting']);
            
            // Broadcast tin nhắn người dùng
            broadcast(new NewChatMessage($message))->toOthers();
            
            // Gửi đến Rasa và nhận phản hồi
            $senderId = 'user_' . ($user ? $user->id : 'guest') . '_room_' . $room->id;
            
            \Log::info('Calling Rasa service', [
                'room_id' => $room->id,
                'message' => $userMessage,
                'sender_id' => $senderId,
                'use_rasa' => $room->use_rasa,
                'quan_tri_id' => $room->quan_tri_id,
            ]);
            
            $rasaResponse = $this->rasaService->sendMessage($userMessage, $senderId);
            
            \Log::info('Rasa response', [
                'room_id' => $room->id,
                'success' => $rasaResponse['success'] ?? false,
                'message' => $rasaResponse['message'] ?? 'No message',
            ]);
            
            if ($rasaResponse['success']) {
                // Lưu phản hồi từ Rasa
                $botMessage = ChatMessage::create([
                    'phong_chat_id' => $room->id,
                    'nguoi_gui_id' => null,
                    'loai_nguoi_gui' => 'admin', // Dùng admin cho bot
                    'ten_nguoi_gui' => 'Chatbot Rasa',
                    'tin_nhan' => $rasaResponse['message'],
                    'loai_tin_nhan' => 'text',
                    'da_doc' => false,
                ]);
                
                // Broadcast phản hồi từ Rasa
                broadcast(new NewChatMessage($botMessage))->toOthers();
                
                return response()->json([
                    'success' => true,
                    'user_message' => $message,
                    'bot_message' => $botMessage,
                    'type' => 'rasa',
                    'use_rasa' => true
                ]);
            } else {
                // Rasa không hoạt động, thông báo chuyển sang cán bộ
                $room->update(['trang_thai' => 'waiting']);
                
                $fallbackMessage = ChatMessage::create([
                    'phong_chat_id' => $room->id,
                    'nguoi_gui_id' => null,
                    'loai_nguoi_gui' => 'admin',
                    'ten_nguoi_gui' => 'Hệ thống',
                    'tin_nhan' => 'Xin lỗi, hệ thống chatbot đang bận. Câu hỏi của bạn đã được chuyển đến cán bộ. Vui lòng chờ phản hồi.',
                    'loai_tin_nhan' => 'text',
                    'da_doc' => false,
                ]);
                
                broadcast(new NewChatMessage($fallbackMessage))->toOthers();
                
                return response()->json([
                    'success' => false,
                    'user_message' => $message,
                    'bot_message' => $fallbackMessage,
                    'type' => 'fallback',
                    'error' => $rasaResponse['message']
                ]);
            }
        }
        
        // Nếu use_rasa = false → Cán bộ đã vào room, chỉ gửi tin nhắn cho cán bộ, KHÔNG gọi Rasa
        if (!$room->use_rasa) {
            // Cán bộ đã vào room → gửi tin nhắn cho cán bộ qua Pusher
            $room->update([
                'trang_thai' => 'active', 
            ]);
            broadcast(new NewChatMessage($message))->toOthers();
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'type' => 'admin',
                'use_rasa' => false
            ]);
        }
        
        // Broadcast tin nhắn người dùng
        broadcast(new NewChatMessage($message))->toOthers();
        
        // Gửi đến Rasa và nhận phản hồi
        $senderId = 'user_' . ($user ? $user->id : 'guest') . '_room_' . $room->id;
        
        \Log::info('Calling Rasa service (fallback)', [
            'room_id' => $room->id,
            'message' => $userMessage,
            'sender_id' => $senderId,
            'use_rasa' => $room->use_rasa,
            'quan_tri_id' => $room->quan_tri_id,
        ]);
        
        $rasaResponse = $this->rasaService->sendMessage($userMessage, $senderId);
        
        \Log::info('Rasa response (fallback)', [
            'room_id' => $room->id,
            'success' => $rasaResponse['success'] ?? false,
            'message' => $rasaResponse['message'] ?? 'No message',
        ]);
        
        if ($rasaResponse['success']) {
            // Lưu phản hồi từ Rasa
            $botMessage = ChatMessage::create([
                'phong_chat_id' => $room->id,
                'nguoi_gui_id' => null,
                'loai_nguoi_gui' => 'admin',
                'ten_nguoi_gui' => 'Chatbot Rasa',
                'tin_nhan' => $rasaResponse['message'],
                'loai_tin_nhan' => 'text',
                'da_doc' => false,
            ]);
            
            // Broadcast phản hồi từ Rasa
            broadcast(new NewChatMessage($botMessage))->toOthers();
            
            return response()->json([
                'success' => true,
                'user_message' => $message,
                'bot_message' => $botMessage,
                'type' => 'rasa',
                'use_rasa' => true
            ]);
        } else {
            // Rasa không hoạt động
            $fallbackMessage = ChatMessage::create([
                'phong_chat_id' => $room->id,
                'nguoi_gui_id' => null,
                'loai_nguoi_gui' => 'admin',
                'ten_nguoi_gui' => 'Hệ thống',
                'tin_nhan' => 'Xin lỗi, hệ thống chatbot đang bận. Câu hỏi của bạn đã được chuyển đến cán bộ. Vui lòng chờ phản hồi.',
                'loai_tin_nhan' => 'text',
                'da_doc' => false,
            ]);
            
            broadcast(new NewChatMessage($fallbackMessage))->toOthers();
            
            return response()->json([
                'success' => false,
                'user_message' => $message,
                'bot_message' => $fallbackMessage,
                'type' => 'fallback',
                'error' => $rasaResponse['message']
            ]);
        }
    }

    // Người dùng thoát hoặc reload: đánh dấu phòng đã đóng để admin không vào nữa
    public function leave(ChatRoom $room)
    {
        // Nếu phòng đã closed thì bỏ qua
        if ($room->trang_thai === 'closed') {
            return response()->json(['status' => 'ok', 'already' => true]);
        }

        $room->update([
            'trang_thai' => 'closed',
            'hoat_dong_cuoi' => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }
}


