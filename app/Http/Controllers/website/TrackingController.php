<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\HoSo;
use App\Models\User;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Trang tra cứu hồ sơ
     */
    public function index()
    {
        return view('website.tracking.index');
    }

    /**
     * Kết quả tra cứu
     */
    public function search(Request $request)
    {
        $request->validate([
            'type' => 'required|in:cccd,ma_ho_so',
            'keyword' => 'required|string|max:255'
        ]);

        $type = $request->type;
        $keyword = $request->keyword;

        $hoSos = collect();

        if ($type === 'cccd') {
            // Tìm user theo CCCD
            $user = User::where('cccd', $keyword)->first();
            
            if ($user) {
                $hoSos = HoSo::where('nguoi_dung_id', $user->id)
                    ->with(['dichVu', 'donVi', 'quanTriVien'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        } else {
            // Tìm theo mã hồ sơ
            $hoSos = HoSo::where('ma_ho_so', 'like', '%' . $keyword . '%')
                ->with(['dichVu', 'donVi', 'quanTriVien', 'nguoiDung'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('website.tracking.results', compact('hoSos', 'keyword', 'type'));
    }

    /**
     * Chi tiết hồ sơ (public - không cần đăng nhập)
     */
    public function show($maHoSo)
    {
        $hoSo = HoSo::where('ma_ho_so', $maHoSo)
            ->with(['dichVu', 'donVi', 'quanTriVien', 'hoSoFields', 'nguoiDung'])
            ->firstOrFail();

        return view('website.tracking.detail', compact('hoSo'));
    }
}

