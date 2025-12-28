<?php

namespace App\Services;

use App\Events\NewChatMessage;
use App\Events\AdminJoinedRoom;
use App\Events\RoomAssignedToAdmin;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatService
{
    protected $rasaService;

    public function __construct(RasaService $rasaService)
    {
        $this->rasaService = $rasaService;
    }

    /**
     * Xử lý tin nhắn từ người dùng
     */
    public function handleUserMessage(ChatRoom $room, string $message, $user = null)
    {
        return DB::transaction(function () use ($room, $message, $user) {
            // Lưu tin nhắn người dùng
            $userMessage = ChatMessage::create([
                'phong_chat_id' => $room->id,
                'nguoi_gui_id' => $user ? $user->id : null,
                'loai_nguoi_gui' => 'user',
                'ten_nguoi_gui' => $user ? ($user->ho_ten ?? $user->email) : $room->ten_nguoi_dung,
                'tin_nhan' => $message,
                'loai_tin_nhan' => 'text',
                'da_doc' => false,
            ]);

            // Cập nhật trạng thái room
            $this->updateRoomStatus($room);

            // Nếu đang dùng Rasa → xử lý qua Rasa
            if ($room->use_rasa) {
                return $this->handleRasaMessage($room, $userMessage, $message, $user);
            }

            // Nếu có cán bộ → gửi cho cán bộ
            return $this->handleAdminMessage($room, $userMessage);
        });
    }

    /**
     * Xử lý tin nhắn từ cán bộ
     */
    public function handleAdminMessage(ChatRoom $room, ChatMessage $message)
    {
        // Đảm bảo Rasa đã tắt khi cán bộ gửi tin nhắn
        if ($room->use_rasa) {
            $room->update([
                'use_rasa' => false,
                'trang_thai' => 'active',
            ]);
        }

        // Cập nhật hoạt động cuối
        $room->update(['hoat_dong_cuoi' => now(), 'trang_thai' => 'active']);

        // Broadcast tin nhắn
        $message->refresh();
        broadcast(new NewChatMessage($message));

        return [
            'success' => true,
            'message' => $message,
            'type' => 'admin',
            'use_rasa' => false
        ];
    }

    /**
     * Xử lý tin nhắn qua Rasa
     */
    protected function handleRasaMessage(ChatRoom $room, ChatMessage $userMessage, string $message, $user = null)
    {
        $room->update(['trang_thai' => 'waiting', 'hoat_dong_cuoi' => now()]);

        // Broadcast tin nhắn người dùng
        broadcast(new NewChatMessage($userMessage))->toOthers();

        // Gửi đến Rasa
        $senderId = 'user_' . ($user ? $user->id : 'guest') . '_room_' . $room->id;
        $rasaResponse = $this->rasaService->sendMessage($message, $senderId);

        if ($rasaResponse['success'] && !empty($rasaResponse['message'])) {
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

            broadcast(new NewChatMessage($botMessage))->toOthers();

            return [
                'success' => true,
                'user_message' => $userMessage,
                'bot_message' => $botMessage,
                'type' => 'rasa',
                'use_rasa' => true
            ];
        }

        // Rasa không hoạt động → thông báo chuyển sang cán bộ
        return $this->handleRasaFallback($room, $userMessage, $rasaResponse);
    }

    /**
     * Xử lý khi Rasa không hoạt động
     */
    protected function handleRasaFallback(ChatRoom $room, ChatMessage $userMessage, array $rasaResponse)
    {
        $room->update(['trang_thai' => 'waiting']);

        $errorMessage = $rasaResponse['message'] ?? 'Hệ thống chatbot đang bận';
        if (strpos($errorMessage, 'model') !== false || strpos($errorMessage, 'train') !== false) {
            $errorMessage = 'Xin lỗi, hệ thống chatbot chưa sẵn sàng. Câu hỏi của bạn đã được chuyển đến cán bộ. Vui lòng chờ phản hồi.';
        } else {
            $errorMessage = 'Xin lỗi, hệ thống chatbot đang bận. Câu hỏi của bạn đã được chuyển đến cán bộ. Vui lòng chờ phản hồi.';
        }

        $fallbackMessage = ChatMessage::create([
            'phong_chat_id' => $room->id,
            'nguoi_gui_id' => null,
            'loai_nguoi_gui' => 'admin',
            'ten_nguoi_gui' => 'Hệ thống',
            'tin_nhan' => $errorMessage,
            'loai_tin_nhan' => 'text',
            'da_doc' => false,
        ]);

        broadcast(new NewChatMessage($fallbackMessage))->toOthers();

        return [
            'success' => false,
            'user_message' => $userMessage,
            'bot_message' => $fallbackMessage,
            'type' => 'fallback',
            'error' => $rasaResponse['message'] ?? 'Unknown error'
        ];
    }

    /**
     * Cập nhật trạng thái room
     */
    protected function updateRoomStatus(ChatRoom $room)
    {
        // Nếu cán bộ đã vào nhưng Rasa vẫn bật → Tắt Rasa
        if ($room->quan_tri_id && $room->use_rasa) {
            $room->update([
                'use_rasa' => false,
                'trang_thai' => 'active',
            ]);
        }
    }

    /**
     * Gán cán bộ cho room
     */
    public function assignAdminToRoom(ChatRoom $room, Admin $admin, bool $keepRasa = false)
    {
        return DB::transaction(function () use ($room, $admin, $keepRasa) {
            $room->update([
                'quan_tri_id' => $admin->id,
                'trang_thai' => 'active',
                'use_rasa' => $keepRasa,
                'hoat_dong_cuoi' => now(),
            ]);

            // Broadcast event
            broadcast(new AdminJoinedRoom($room))->toOthers();
            broadcast(new RoomAssignedToAdmin($room, $admin))->toOthers();

            return $room->fresh();
        });
    }

    /**
     * Gửi welcome message từ Rasa
     */
    public function sendRasaWelcomeMessage(ChatRoom $room, $user = null)
    {
        try {
            // Kiểm tra kết nối Rasa
            $connectionCheck = $this->rasaService->checkConnection();
            
            Log::info('Checking Rasa connection for welcome message', [
                'room_id' => $room->id,
                'connected' => $connectionCheck['connected'] ?? false,
                'has_model' => $connectionCheck['has_model'] ?? false,
            ]);
            
            if (!$connectionCheck['connected']) {
                Log::warning('Rasa not connected for welcome message', [
                    'room_id' => $room->id,
                ]);
                return false;
            }
            
            $senderId = 'user_' . ($user ? $user->id : 'guest') . '_room_' . $room->id;
            
            // Thử các trigger message
            $triggerMessages = ['/start', 'hello', 'xin chào', 'chào'];
            $rasaResponse = null;
            
            foreach ($triggerMessages as $trigger) {
                $rasaResponse = $this->rasaService->sendMessage($trigger, $senderId);
                if ($rasaResponse['success'] && !empty($rasaResponse['message'])) {
                    break;
                }
            }
            
            if ($rasaResponse && $rasaResponse['success'] && !empty($rasaResponse['message'])) {
                $botMessage = ChatMessage::create([
                    'phong_chat_id' => $room->id,
                    'nguoi_gui_id' => null,
                    'loai_nguoi_gui' => 'admin',
                    'ten_nguoi_gui' => 'Chatbot Rasa',
                    'tin_nhan' => $rasaResponse['message'],
                    'loai_tin_nhan' => 'text',
                    'da_doc' => false,
                ]);
                
                broadcast(new NewChatMessage($botMessage))->toOthers();
                return true;
            }
            
            Log::warning('Rasa welcome message failed', [
                'room_id' => $room->id,
                'response' => $rasaResponse,
            ]);
            
            return false;
        } catch (\Exception $e) {
            Log::error('Error sending Rasa welcome message', [
                'room_id' => $room->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

