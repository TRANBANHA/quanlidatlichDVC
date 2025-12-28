<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatRoom;

class RoomAssignedToAdmin implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $adminId;

    public function __construct(ChatRoom $room, $adminId)
    {
        $this->room = $room;
        $this->adminId = $adminId;
    }

    public function broadcastOn()
    {
        // Broadcast đến channel riêng của admin để họ nhận được thông báo
        return [
            new Channel('admin.' . $this->adminId),
            new Channel('admin-waiting-rooms'), // Channel chung cho tất cả admin
        ];
    }

    public function broadcastAs()
    {
        return 'RoomAssignedToAdmin';
    }

    public function broadcastWith()
    {
        return [
            'room_id' => $this->room->id,
            'room_code' => $this->room->ma_phong,
            'user_name' => $this->room->ten_nguoi_dung,
            'user_email' => $this->room->email_nguoi_dung,
            'user_phone' => $this->room->so_dien_thoai,
            'admin_id' => $this->room->quan_tri_id,
            'trang_thai' => $this->room->trang_thai,
            'message' => 'Bạn có phòng chat mới được gán từ người dùng: ' . $this->room->ten_nguoi_dung,
        ];
    }
}

