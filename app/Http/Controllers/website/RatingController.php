<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\HoSo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * Form đánh giá (sau khi hoàn tất)
     */
    public function create($hoSoId)
    {
        $hoSo = HoSo::with(['dichVu', 'donVi', 'quanTriVien'])
            ->findOrFail($hoSoId);

        // Kiểm tra quyền
        if ($hoSo->nguoi_dung_id != Auth::guard('web')->id()) {
            abort(403, 'Bạn không có quyền đánh giá hồ sơ này');
        }

        // Kiểm tra đã hoàn tất chưa
        if ($hoSo->trang_thai !== HoSo::STATUS_COMPLETED) {
            return back()->withErrors(['error' => 'Chỉ có thể đánh giá hồ sơ đã hoàn tất']);
        }

        // Kiểm tra đã đánh giá chưa
        if ($hoSo->rating) {
            return redirect()->route('rating.edit', $hoSo->rating->id)
                ->with('info', 'Bạn đã đánh giá hồ sơ này. Bạn có thể chỉnh sửa đánh giá.');
        }

        return view('website.rating.create', compact('hoSo'));
    }

    /**
     * Lưu đánh giá
     */
    public function store(Request $request, $hoSoId)
    {
        $hoSo = HoSo::findOrFail($hoSoId);

        // Kiểm tra quyền
        if ($hoSo->nguoi_dung_id != Auth::guard('web')->id()) {
            abort(403);
        }

        // Kiểm tra đã đánh giá chưa
        if ($hoSo->rating) {
            return back()->withErrors(['error' => 'Bạn đã đánh giá hồ sơ này rồi']);
        }

        $request->validate([
            'diem' => 'required|integer|min:1|max:5',
            'binh_luan' => 'nullable|string|max:1000',
            'diem_thai_do' => 'nullable|integer|min:1|max:5',
            'diem_thoi_gian' => 'nullable|integer|min:1|max:5',
            'diem_chat_luong' => 'nullable|integer|min:1|max:5',
            'diem_co_so_vat_chat' => 'nullable|integer|min:1|max:5',
            'co_nen_gioi_thieu' => 'nullable|boolean',
            'y_kien_khac' => 'nullable|string|max:2000',
        ]);

        Rating::create([
            'ho_so_id' => $hoSo->id,
            'nguoi_dung_id' => Auth::guard('web')->id(),
            'quan_tri_vien_id' => $hoSo->quan_tri_vien_id, // Cán bộ xử lý
            'diem' => $request->diem,
            'binh_luan' => $request->binh_luan,
            'diem_thai_do' => $request->diem_thai_do,
            'diem_thoi_gian' => $request->diem_thoi_gian,
            'diem_chat_luong' => $request->diem_chat_luong,
            'diem_co_so_vat_chat' => $request->diem_co_so_vat_chat,
            'co_nen_gioi_thieu' => $request->has('co_nen_gioi_thieu') ? (bool)$request->co_nen_gioi_thieu : null,
            'y_kien_khac' => $request->y_kien_khac,
        ]);

        return redirect()->route('info.index', ['action' => 'tab2'])
            ->with('success', 'Cảm ơn bạn đã đánh giá dịch vụ!');
    }

    /**
     * Chỉnh sửa đánh giá
     */
    public function edit($ratingId)
    {
        $rating = Rating::with('hoSo.dichVu')->findOrFail($ratingId);

        if ($rating->nguoi_dung_id != Auth::guard('web')->id()) {
            abort(403);
        }

        return view('website.rating.edit', compact('rating'));
    }

    /**
     * Cập nhật đánh giá
     */
    public function update(Request $request, $ratingId)
    {
        $rating = Rating::findOrFail($ratingId);

        if ($rating->nguoi_dung_id != Auth::guard('web')->id()) {
            abort(403);
        }

        $request->validate([
            'diem' => 'required|integer|min:1|max:5',
            'binh_luan' => 'nullable|string|max:1000',
        ]);

        $rating->update([
            'diem' => $request->diem,
            'binh_luan' => $request->binh_luan,
        ]);

        return redirect()->route('my-bookings.show', $rating->ho_so_id)
            ->with('success', 'Đã cập nhật đánh giá');
    }
}

