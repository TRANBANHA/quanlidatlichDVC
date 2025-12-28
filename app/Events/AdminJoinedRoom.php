<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatRoom;

class AdminJoinedRoom implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $adminName;

    public function __construct(ChatRoom $room)
    {
        $this->room = $room;
        $this->adminName = $room->admin ? $room->admin->ho_ten : 'Cán bộ';
    }

    public function broadcastOn()
    {
        return new Channel('chat-room.' . $this->room->id);
    }

    public function broadcastAs()
    {
        return 'AdminJoinedRoom';
    }

    public function broadcastWith()
    {
        $message = $this->room->use_rasa 
            ? 'Cán bộ đã vào phòng chat. AI chatbot sẽ tiếp tục trả lời cho đến khi cán bộ bắt đầu chat.'
            : 'Cán bộ ' . $this->adminName . ' đã vào phòng chat. Rasa chatbot đã được tắt.';
            
        return [
            'room_id' => $this->room->id,
            'admin_id' => $this->room->quan_tri_id,
            'admin_name' => $this->adminName,
            'use_rasa' => $this->room->use_rasa,
            'rasa_disabled' => !$this->room->use_rasa, // Rasa đã tắt hay chưa
            'message' => $message
        ];
    }
}

