<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Danh sách thông báo
     */
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();
        
        $query = ThongBao::where('nguoi_dung_id', $user->id)
            ->with(['hoSo', 'dichVu'])
            ->orderBy('created_at', 'desc');

        // Lọc theo trạng thái đã đọc
        if ($request->has('is_read')) {
            $query->where('is_read', $request->is_read == '1');
        }

        $notifications = $query->paginate(20);

        // Đếm thông báo chưa đọc
        $unreadCount = ThongBao::where('nguoi_dung_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('website.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Đánh dấu đã đọc
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $notification = ThongBao::where('nguoi_dung_id', Auth::guard('web')->id())
                ->findOrFail($id);
            
            $notification->update(['is_read' => true]);

            // Luôn trả về JSON cho AJAX request (kiểm tra nhiều điều kiện)
            $isAjax = $request->ajax() 
                || $request->wantsJson() 
                || $request->expectsJson()
                || $request->header('X-Requested-With') === 'XMLHttpRequest'
                || $request->header('Accept') === 'application/json';
                
            if ($isAjax) {
                return response()->json(['success' => true], 200, [
                    'Content-Type' => 'application/json; charset=utf-8'
                ]);
            }
            
            return back()->with('success', 'Đã đánh dấu đã đọc');
        } catch (\Exception $e) {
            \Log::error('Mark as read error', [
                'id' => $id,
                'user_id' => Auth::guard('web')->id(),
                'error' => $e->getMessage(),
                'headers' => $request->headers->all()
            ]);
            
            // Luôn trả về JSON cho AJAX request
            $isAjax = $request->ajax() 
                || $request->wantsJson() 
                || $request->expectsJson()
                || $request->header('X-Requested-With') === 'XMLHttpRequest'
                || $request->header('Accept') === 'application/json';
                
            if ($isAjax) {
                return response()->json([
                    'success' => false, 
                    'message' => $e->getMessage()
                ], 400, [
                    'Content-Type' => 'application/json; charset=utf-8'
                ]);
            }
            
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Đánh dấu tất cả đã đọc
     */
    public function markAllAsRead()
    {
        ThongBao::where('nguoi_dung_id', Auth::guard('web')->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc');
    }

    /**
     * Xóa thông báo
     */
    public function destroy($id)
    {
        $notification = ThongBao::where('nguoi_dung_id', Auth::guard('web')->id())
            ->findOrFail($id);
        
        $notification->delete();

        return back()->with('success', 'Đã xóa thông báo');
    }
}

