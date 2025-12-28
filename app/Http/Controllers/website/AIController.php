<?php

namespace App\Http\Controllers\website;

use App\Services\AnthropicService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    protected $anthropicService;

    public function __construct(AnthropicService $anthropicService)
    {
        $this->anthropicService = $anthropicService;
    }

    public function generateText(Request $request)
    {
        Log::info('Request Data', $request->all());

        $response = $this->anthropicService->callClaudeApi($request->message);
        if (isset($response['error'])) {
            return response()->json([
                'error' => $response['error'],
                'message' => $response['message'],
            ], $response['error']);
        }

        return response()->json($response);
    }
}
