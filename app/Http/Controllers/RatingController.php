<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return back()->with('error', 'Vui lòng đăng nhập để đánh giá.');
        }

        $validated = $request->validate([
            'ho_so_id' => 'required|exists:ho_so,id',
            'nguoi_dung_id' => 'required|exists:nguoi_dung,id',
            'diem' => 'required|integer|min:1|max:5',
            'binh_luan' => 'nullable|string|max:1000',
        ]);

        // Kiểm tra xem người dùng có phải là chủ sở hữu hồ sơ không
        if ($validated['nguoi_dung_id'] != Auth::user()->id) {
            return back()->with('error', 'Bạn không có quyền đánh giá hồ sơ này.');
        }

        // Kiểm tra xem đã đánh giá chưa
        $existingRating = Rating::where('ho_so_id', $validated['ho_so_id'])->first();
        if ($existingRating) {
            return back()->with('error', 'Bạn đã đánh giá hồ sơ này rồi.');
        }

        // Tạo đánh giá mới
        Rating::create($validated);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá dịch vụ!');
    }
}
