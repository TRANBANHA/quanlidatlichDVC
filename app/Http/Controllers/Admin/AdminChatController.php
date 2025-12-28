<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Events\NewChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminChatController extends Controller
{
    public function dashboard()
    {
        $conversations = Conversation::with(['user', 'lastMessage'])
            ->where('is_active', true)
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('chat.admin-dashboard', compact('conversations'));
    }

    public function showConversation(Conversation $conversation)
    {
        // Gán admin cho conversation nếu chưa có
        if (!$conversation->admin_id) {
            $conversation->update(['admin_id' => Auth::id()]);
        }

        $messages = $conversation->messages()->with('sender')->get();

        // Đánh dấu tin nhắn là đã đọc
        ChatMessage::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', Auth::id())
            ->update(['is_read' => true]);

        return view('chat.admin-conversation', compact('conversation', 'messages'));
    }

    public function adminSendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'message' => $request->message
        ]);

        $conversation->update(['last_message_at' => now()]);

        broadcast(new NewChatMessage($message))->toOthers();

        return response()->json(['status' => 'success']);
    }

    public function getConversations()
    {
        $conversations = Conversation::with(['user', 'lastMessage'])
            ->where('is_active', true)
            ->orderBy('last_message_at', 'desc')
            ->get();

        return response()->json($conversations);
    }
}