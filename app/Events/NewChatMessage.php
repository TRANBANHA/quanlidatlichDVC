<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatMessage;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $roomId;

    public function __construct(ChatMessage $message)
    {
        $this->message = $message;
        $this->roomId = $message->phong_chat_id;
    }

    public function broadcastOn()
    {
        $channels = [new Channel('chat-room.' . $this->roomId)];

        // Nếu tin nhắn từ người dùng và phòng chưa có quản trị viên nhận,
        // phát thêm vào kênh chung cho admin để nhận thông báo ngay
        try {
            if (($this->message->loai_nguoi_gui ?? null) !== 'admin') {
                $room = $this->message->room()->first();
                if ($room && empty($room->quan_tri_id)) {
                    $channels[] = new Channel('admin-waiting-rooms');
                }
            }
        } catch (\Throwable $e) {
            // Bỏ qua lỗi không quan trọng khi load quan hệ
        }

        return $channels;
    }
    
    /**
     * Tên event khi broadcast (Laravel sẽ tự động format với namespace)
     */
    public function broadcastAs()
    {
        return 'NewChatMessage';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'phong_chat_id' => $this->message->phong_chat_id,
            'nguoi_gui_id' => $this->message->nguoi_gui_id,
            'ten_nguoi_gui' => $this->message->ten_nguoi_gui ?? optional($this->message->sender)->name,
            'loai_nguoi_gui' => $this->message->loai_nguoi_gui,
            'tin_nhan' => $this->message->tin_nhan,
            'loai_tin_nhan' => $this->message->loai_tin_nhan,
            'time' => $this->message->created_at->format('H:i'),
            'is_admin' => optional($this->message->sender)->is_admin ?? ($this->message->loai_nguoi_gui === 'admin'),
            // Giữ lại các key cũ để tương thích với frontend
            'chat_room_id' => $this->message->phong_chat_id,
            'sender_id' => $this->message->nguoi_gui_id,
            'sender_name' => $this->message->ten_nguoi_gui ?? optional($this->message->sender)->name,
            'sender_type' => $this->message->loai_nguoi_gui,
            'message' => $this->message->tin_nhan,
            'message_type' => $this->message->loai_tin_nhan,
        ];
    }
}