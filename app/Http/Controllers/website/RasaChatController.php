<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Services\RasaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RasaChatController extends Controller
{
    protected $rasaService;

    public function __construct(RasaService $rasaService)
    {
        $this->rasaService = $rasaService;
    }

    /**
     * Gửi tin nhắn đến Rasa chatbot
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'sender_id' => 'nullable|string',
        ]);

        $senderId = $request->input('sender_id') ?? 'user_' . (Auth::id() ?? 'guest');
        $message = $request->input('message');

        $response = $this->rasaService->sendMessage($message, $senderId);

        return response()->json($response);
    }

    /**
     * Kiểm tra trạng thái Rasa service
     */
    public function checkStatus()
    {
        try {
            // Thử gửi một tin nhắn test
            $response = $this->rasaService->sendMessage('test', 'status_check');
            
            return response()->json([
                'status' => $response['success'] ? 'online' : 'offline',
                'message' => $response['message'] ?? 'Rasa service status',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'offline',
                'message' => 'Không thể kết nối với Rasa service: ' . $e->getMessage(),
            ], 500);
        }
    }
}

