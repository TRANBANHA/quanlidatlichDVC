<?php
use App\Events\RealTimeMessage;
use Illuminate\Support\Facades\Auth;
// Auth::routes(); // Đã có custom auth routes trong website.php
// require base_path('routes/admin.php');

Route::get('/test-route', function() {
    return 'Route works! posts.show = ' . route('posts.show', 'test');
});

Route::get('/test-pusher-connection', function() {
    try {
        // Test cấu hình
        $broadcastConfig = config('broadcasting.connections.pusher');
        
        // Test kết nối bằng cách gửi event test
        event(new RealTimeMessage('Test connection to Pusher'));
        
        return response()->json([
            'status' => 'success',
            'config' => [
                'cluster' => $broadcastConfig['options']['cluster'],
                'host' => $broadcastConfig['options']['host'],
                'app_id' => $broadcastConfig['app_id'],
                'key' => substr($broadcastConfig['key'], 0, 10) . '...' // Ẩn bớt key
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});
require base_path('routes/website.php');
