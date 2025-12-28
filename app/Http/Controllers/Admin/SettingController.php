<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    /**
     * Hiển thị trang cấu hình website
     */
    public function index()
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Chỉ Admin tổng mới có quyền truy cập.');
        }

        // Lấy các cấu hình từ bảng cai_dat hoặc config
        $settings = [
            'site_name' => config('app.name', 'Hệ thống đặt lịch'),
            'site_description' => DB::table('cai_dat')->where('khoa', 'site_description')->value('gia_tri') ?? '',
            'site_keywords' => DB::table('cai_dat')->where('khoa', 'site_keywords')->value('gia_tri') ?? '',
            'site_logo' => DB::table('cai_dat')->where('khoa', 'site_logo')->value('gia_tri') ?? '',
            'site_favicon' => DB::table('cai_dat')->where('khoa', 'site_favicon')->value('gia_tri') ?? '',
            'contact_email' => DB::table('cai_dat')->where('khoa', 'contact_email')->value('gia_tri') ?? '',
            'contact_phone' => DB::table('cai_dat')->where('khoa', 'contact_phone')->value('gia_tri') ?? '',
            'contact_address' => DB::table('cai_dat')->where('khoa', 'contact_address')->value('gia_tri') ?? '',
            'facebook_url' => DB::table('cai_dat')->where('khoa', 'facebook_url')->value('gia_tri') ?? '',
            'youtube_url' => DB::table('cai_dat')->where('khoa', 'youtube_url')->value('gia_tri') ?? '',
            'zalo_url' => DB::table('cai_dat')->where('khoa', 'zalo_url')->value('gia_tri') ?? '',
        ];

        return view('backend.settings.index', compact('settings'));
    }

    /**
     * Cập nhật cấu hình website
     */
    public function update(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403, 'Chỉ Admin tổng mới có quyền cập nhật.');
        }

        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_keywords' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'site_favicon' => 'nullable|image|mimes:jpeg,png,jpg,ico|max:512',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string|max:500',
            'facebook_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'zalo_url' => 'nullable|url|max:255',
        ]);

        // Xử lý upload logo
        if ($request->hasFile('site_logo')) {
            $logo = $request->file('site_logo');
            $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('settings', $logoName, 'public');
            $this->saveSetting('site_logo', $logoPath);
        }

        // Xử lý upload favicon
        if ($request->hasFile('site_favicon')) {
            $favicon = $request->file('site_favicon');
            $faviconName = 'favicon_' . time() . '.' . $favicon->getClientOriginalExtension();
            $faviconPath = $favicon->storeAs('settings', $faviconName, 'public');
            $this->saveSetting('site_favicon', $faviconPath);
        }

        // Lưu các cấu hình khác
        foreach ($validated as $key => $value) {
            if ($key !== 'site_logo' && $key !== 'site_favicon' && $value !== null) {
                $this->saveSetting($key, $value);
            }
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Cập nhật cấu hình thành công!');
    }

    /**
     * Lưu setting vào database
     */
    private function saveSetting($key, $value)
    {
        DB::table('cai_dat')->updateOrInsert(
            ['khoa' => $key],
            ['gia_tri' => $value, 'updated_at' => now()]
        );
    }
}

