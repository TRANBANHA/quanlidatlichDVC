<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NotificationAdminController extends Controller
{
    /**
     * Danh sách thông báo (Admin tổng)
     */
    public function index(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Chỉ Admin tổng mới có quyền truy cập.');
        }

        $query = Notification::with('user')->orderBy('created_at', 'desc');

        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tieu_de', 'like', "%{$search}%")
                  ->orWhere('noi_dung', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('ten', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Lọc theo người dùng
        if ($request->has('user_id') && $request->user_id) {
            $query->where('nguoi_dung_id', $request->user_id);
        }

        // Lọc theo ngày
        if ($request->has('publish_date') && $request->publish_date) {
            $query->whereDate('ngay_dang', $request->publish_date);
        }

        $notifications = $query->paginate(15);
        $users = User::all();

        return view('backend.notifications.index', compact('notifications', 'users'));
    }

    /**
     * Tạo thông báo mới
     */
    public function create()
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Chỉ Admin tổng mới có quyền tạo thông báo.');
        }

        $users = User::all();

        return view('backend.notifications.create', compact('users'));
    }

    /**
     * Lưu thông báo mới
     */
    public function store(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Chỉ Admin tổng mới có quyền tạo thông báo.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'publish_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'user_id' => 'nullable|exists:nguoi_dung,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|mimetypes:video/mp4,video/x-msvideo,video/x-matroska|max:20000',
        ]);

        $notification = new Notification();
        $notification->tieu_de = $validated['title'];
        $notification->noi_dung = $validated['content'];
        $notification->ngay_dang = $validated['publish_date'];
        $notification->ngay_het_han = $validated['expiry_date'] ?? null;
        $notification->nguoi_dung_id = $validated['user_id'] ?? null;

        // Upload hình ảnh
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->storeAs('notifications', 'notif_' . time() . '.' . $request->file('image')->getClientOriginalExtension(), 'public');
            $notification->hinh_anh = $imagePath;
        }

        // Upload video
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->storeAs('notifications', 'notif_' . time() . '.' . $request->file('video')->getClientOriginalExtension(), 'public');
            $notification->video = $videoPath;
        }

        $notification->save();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Tạo thông báo thành công!');
    }

    /**
     * Sửa thông báo
     */
    public function edit($id)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Chỉ Admin tổng mới có quyền sửa thông báo.');
        }

        $notification = Notification::findOrFail($id);
        $users = User::all();

        return view('backend.notifications.edit', compact('notification', 'users'));
    }

    /**
     * Cập nhật thông báo
     */
    public function update(Request $request, $id)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Chỉ Admin tổng mới có quyền cập nhật thông báo.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'publish_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'user_id' => 'nullable|exists:nguoi_dung,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|mimetypes:video/mp4,video/x-msvideo,video/x-matroska|max:20000',
        ]);

        $notification = Notification::findOrFail($id);
        $notification->tieu_de = $validated['title'];
        $notification->noi_dung = $validated['content'];
        $notification->ngay_dang = $validated['publish_date'];
        $notification->ngay_het_han = $validated['expiry_date'] ?? null;
        $notification->nguoi_dung_id = $validated['user_id'] ?? null;

        // Xử lý upload hình ảnh mới
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($notification->hinh_anh) {
                Storage::disk('public')->delete($notification->hinh_anh);
            }
            $imagePath = $request->file('image')->storeAs('notifications', 'notif_' . time() . '.' . $request->file('image')->getClientOriginalExtension(), 'public');
            $notification->hinh_anh = $imagePath;
        }

        // Xử lý upload video mới
        if ($request->hasFile('video')) {
            // Xóa video cũ
            if ($notification->video) {
                Storage::disk('public')->delete($notification->video);
            }
            $videoPath = $request->file('video')->storeAs('notifications', 'notif_' . time() . '.' . $request->file('video')->getClientOriginalExtension(), 'public');
            $notification->video = $videoPath;
        }

        $notification->save();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Cập nhật thông báo thành công!');
    }

    /**
     * Xóa thông báo
     */
    public function destroy($id)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Chỉ Admin tổng mới có quyền xóa thông báo.');
        }

        $notification = Notification::findOrFail($id);

        // Xóa file đính kèm
        if ($notification->hinh_anh) {
            Storage::disk('public')->delete($notification->hinh_anh);
        }
        if ($notification->video) {
            Storage::disk('public')->delete($notification->video);
        }

        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Xóa thông báo thành công!');
    }
}

