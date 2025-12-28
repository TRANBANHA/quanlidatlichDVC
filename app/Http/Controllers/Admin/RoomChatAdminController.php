<?php

namespace App\Http\Controllers\Admin;

use App\Events\NewChatMessage;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomChatAdminController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        
        // Lọc room: chỉ hiển thị room được gán cho cán bộ này hoặc room chưa có cán bộ
        $query = ChatRoom::with('latestMessage');
        
        // Nếu là cán bộ (quyen = 0), chỉ hiển thị room được gán cho mình
        if ($admin && $admin->quyen == Admin::CAN_BO) {
            $query->where(function($q) use ($admin) {
                $q->where('quan_tri_id', $admin->id) // Room được gán cho cán bộ này
                  ->orWhereNull('quan_tri_id'); // Hoặc room chưa có cán bộ
            });
        }
        // Admin phường và Admin tổng xem tất cả room
        
        $rooms = $query->orderByDesc('hoat_dong_cuoi')->get();
        
        // Nếu request là AJAX hoặc muốn JSON, trả về JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($rooms);
        }
        
        // Trả về view
        return view('backend.room_chat.index', compact('rooms'));
    }
    
    public function show(ChatRoom $room)
    {
        if ($room->trang_thai === 'closed') {
            return redirect()->route('admin.room-chats.index')
                ->with('error', 'Phòng chat đã được người dùng đóng.');
        }
        
        $admin = Auth::guard('admin')->user();
        
        // Nếu room được gán cho cán bộ này và cán bộ chưa vào (use_rasa vẫn true)
        // Tự động claim và tắt Rasa
        if ($room->quan_tri_id == $admin->id && $room->use_rasa) {
            // Tắt Rasa vì cán bộ đã vào room
            $room->update([
                'trang_thai' => 'active',
                'use_rasa' => false,
                'hoat_dong_cuoi' => now(),
            ]);
            
            $room->refresh();
            
            // Broadcast event thông báo cán bộ đã vào
            broadcast(new \App\Events\AdminJoinedRoom($room))->toOthers();
            
            // Chỉ gửi tin nhắn chào mừng từ cán bộ
            $welcomeMessage = ChatMessage::create([
                'phong_chat_id' => $room->id,
                'nguoi_gui_id' => $admin->id,
                'loai_nguoi_gui' => 'admin',
                'ten_nguoi_gui' => $admin->ho_ten ?? 'Cán bộ',
                'tin_nhan' => 'Xin chào! Tôi là cán bộ ' . ($admin->donVi ? $admin->donVi->ten_don_vi : '') . '. Tôi sẽ hỗ trợ bạn ngay bây giờ.',
                'loai_tin_nhan' => 'text',
                'da_doc' => false,
            ]);
            broadcast(new \App\Events\NewChatMessage($welcomeMessage))->toOthers();
        }
        
        $messages = $room->messages()->orderBy('created_at')->get();
        
        // Đánh dấu tin nhắn là đã đọc
        ChatMessage::where('phong_chat_id', $room->id)
            ->where('loai_nguoi_gui', '!=', 'admin')
            ->update(['da_doc' => true]);
        
        // Refresh room để lấy dữ liệu mới nhất
        $room->refresh();
        
        return view('backend.room_chat.show', compact('room', 'messages', 'admin'));
    }

    public function claim(ChatRoom $room)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        if ($room->trang_thai === 'closed') {
            return response()->json(['message' => 'Room closed'], 410);
        }
        
        // Kiểm tra xem room này có được gán cho admin này không
        // Nếu có (người dùng đã chọn nhân viên này) → Tắt Rasa ngay khi vào
        // Nếu không (admin tự claim) → Vẫn giữ Rasa cho đến khi gửi tin nhắn đầu tiên
        $isAssignedToThisAdmin = $room->quan_tri_id == $admin->id;
        
        // Nếu được gán sẵn cho cán bộ này → Tắt Rasa ngay khi vào room
        // Nếu không được gán sẵn → Giữ Rasa để AI tiếp tục chat
        $shouldDisableRasa = $isAssignedToThisAdmin;
        
        $room->update([
            'quan_tri_id' => $admin->id, 
            'trang_thai' => 'active',
            'use_rasa' => !$shouldDisableRasa, // Nếu được gán sẵn thì tắt Rasa, nếu không thì giữ Rasa
            'hoat_dong_cuoi' => now(),
        ]);
        
        // Refresh để lấy dữ liệu mới
        $room->refresh();
        
        // Broadcast event thông báo cán bộ đã vào (để frontend cập nhật UI)
        // Chỉ gửi đến room này, chỉ cán bộ được chọn mới nhận được
        broadcast(new \App\Events\AdminJoinedRoom($room))->toOthers();
        
            // Nếu được gán sẵn và đã tắt Rasa → Chỉ gửi tin nhắn chào mừng từ cán bộ
            if ($isAssignedToThisAdmin && !$room->use_rasa) {
                // Gửi tin nhắn chào mừng từ cán bộ
                $welcomeMessage = ChatMessage::create([
                    'phong_chat_id' => $room->id,
                    'nguoi_gui_id' => $admin->id,
                    'loai_nguoi_gui' => 'admin',
                    'ten_nguoi_gui' => $admin->ho_ten ?? 'Cán bộ',
                    'tin_nhan' => 'Xin chào! Tôi là cán bộ ' . ($admin->donVi ? $admin->donVi->ten_don_vi : '') . '. Tôi sẽ hỗ trợ bạn ngay bây giờ.',
                    'loai_tin_nhan' => 'text',
                    'da_doc' => false,
                ]);
                broadcast(new \App\Events\NewChatMessage($welcomeMessage))->toOthers();
            }
        
        return response()->json($room->fresh());
    }

    public function randomClaim()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // Only admin tổng (quyen = 1) and admin phường (quyen = 2) can be auto-assigned
        if (!in_array((int)($admin->quyen ?? 0), [Admin::ADMIN, Admin::ADMIN_PHUONG])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $room = ChatRoom::whereNull('quan_tri_id')
            ->where('trang_thai', 'waiting')
            ->inRandomOrder()
            ->first();

        if (!$room) {
            return response()->json(['message' => 'No waiting room'], 404);
        }

        // Cập nhật room: gán cán bộ nhưng VẪN GIỮ RASA để AI tiếp tục chat
        // Rasa sẽ chỉ tắt khi cán bộ gửi tin nhắn đầu tiên
        $room->update([
            'quan_tri_id' => $admin->id, 
            'trang_thai' => 'active', 
            'use_rasa' => true, // Vẫn giữ Rasa để AI chat trước
            'hoat_dong_cuoi' => now()
        ]);
        
        // Refresh để lấy dữ liệu mới
        $room->refresh();
        
        // Broadcast event thông báo cán bộ đã vào
        broadcast(new \App\Events\AdminJoinedRoom($room))->toOthers();
        
        // Không gửi tin nhắn chào mừng ngay, để AI tiếp tục chat
        // Cán bộ sẽ gửi tin nhắn đầu tiên khi sẵn sàng

        return response()->json($room->fresh());
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

        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Refresh room để lấy trạng thái mới nhất
        $room->refresh();

        // Kiểm tra nếu đây là tin nhắn đầu tiên từ cán bộ và room đang dùng Rasa
        // Thì tắt Rasa để chuyển sang chat với cán bộ
        $isFirstMessage = !ChatMessage::where('phong_chat_id', $room->id)
            ->where('loai_nguoi_gui', 'admin')
            ->where('nguoi_gui_id', $admin->id)
            ->exists();

        if ($isFirstMessage && $room->use_rasa) {
            // Đây là tin nhắn đầu tiên từ cán bộ → Tắt Rasa
            $room->update([
                'use_rasa' => false,
                'trang_thai' => 'active',
            ]);
        }

        $message = ChatMessage::create([
            'phong_chat_id' => $room->id,
            'nguoi_gui_id' => $admin->id,
            'loai_nguoi_gui' => 'admin',
            'ten_nguoi_gui' => $admin->ho_ten ?? 'Admin',
            'tin_nhan' => $request->input('message'),
            'loai_tin_nhan' => 'text',
            'da_doc' => false,
        ]);

        $room->update(['hoat_dong_cuoi' => now(), 'trang_thai' => 'active']);

        broadcast(new NewChatMessage($message))->toOthers();

        return response()->json([
            'message' => $message,
            'use_rasa' => $room->use_rasa,
            'rasa_stopped' => $isFirstMessage && !$room->use_rasa,
        ]);
    }
}


