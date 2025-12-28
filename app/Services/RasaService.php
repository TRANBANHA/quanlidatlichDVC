<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RasaService
{
    protected $rasaUrl;

    public function __construct()
    {
        $this->rasaUrl = config('services.rasa.url', 'http://localhost:5005');
        
        // Log để debug
        Log::info('RasaService initialized', [
            'rasa_url' => $this->rasaUrl,
            'config_url' => config('services.rasa.url'),
            'env_url' => env('RASA_URL'),
        ]);
    }

    /**
     * Gửi tin nhắn đến Rasa và nhận phản hồi
     */
    public function sendMessage($message, $senderId = null)
    {
        try {
            $senderId = $senderId ?? 'user_' . uniqid();
            
            $url = "{$this->rasaUrl}/webhooks/rest/webhook";
            
            Log::info('Sending message to Rasa', [
                'url' => $url,
                'sender_id' => $senderId,
                'message' => $message,
            ]);
            
            $response = Http::timeout(10)->post($url, [
                'sender' => $senderId,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Rasa trả về array các messages
                if (is_array($data) && count($data) > 0) {
                    $text = collect($data)->pluck('text')->implode("\n");
                    return [
                        'success' => true,
                        'message' => $text,
                        'data' => $data,
                    ];
                }
                
                return [
                    'success' => true,
                    'message' => 'Đã nhận phản hồi từ Rasa',
                    'data' => $data,
                ];
            }

            Log::error('Rasa API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Không thể kết nối với Rasa chatbot. Vui lòng thử lại sau.',
                'error' => $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('Rasa Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kết nối với chatbot. Vui lòng thử lại sau.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Kiểm tra kết nối với Rasa server
     */
    public function checkConnection()
    {
        try {
            $response = Http::timeout(5)->get("{$this->rasaUrl}/status");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}

