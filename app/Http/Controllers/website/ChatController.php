<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Events\NewChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function userChat()
    {
        // Tìm hoặc tạo conversation cho user
        $conversation = Conversation::firstOrCreate(
            ['user_id' => Auth::id()],
            ['is_active' => true]
        );

        $messages = $conversation->messages()->with('sender')->get();

        return view('chat.user', compact('conversation', 'messages'));
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'message' => $request->message
        ]);

        // Cập nhật thời gian last message
        $conversation->update(['last_message_at' => now()]);

        broadcast(new NewChatMessage($message))->toOthers();

        return response()->json([
            'status' => 'success',
            'message' => $message->load('sender')
        ]);
    }
}