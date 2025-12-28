<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key', '');
        $this->apiUrl = config('services.anthropic.api_url', 'https://api.anthropic.com/v1/messages');
    }

    /**
     * Gọi Claude API
     */
    public function callClaudeApi($message)
    {
        try {
            if (empty($this->apiKey)) {
                return [
                    'error' => 500,
                    'message' => 'API key chưa được cấu hình.',
                ];
            }

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(30)->post($this->apiUrl, [
                'model' => 'claude-3-sonnet-20240229',
                'max_tokens' => 1024,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $message,
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => $data['content'][0]['text'] ?? 'Không có phản hồi',
                    'data' => $data,
                ];
            }

            Log::error('Anthropic API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'error' => $response->status(),
                'message' => 'Không thể kết nối với Claude API. Vui lòng thử lại sau.',
            ];

        } catch (\Exception $e) {
            Log::error('Anthropic Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'error' => 500,
                'message' => 'Có lỗi xảy ra khi gọi API: ' . $e->getMessage(),
            ];
        }
    }
}

