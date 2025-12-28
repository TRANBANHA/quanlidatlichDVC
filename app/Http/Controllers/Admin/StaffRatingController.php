<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffRatingController extends Controller
{
    /**
     * Danh sách đánh giá của cán bộ
     */
    public function index(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Chỉ cán bộ mới xem được đánh giá của mình
        if (!$currentUser->isCanBo()) {
            abort(403, 'Chỉ cán bộ mới có quyền xem đánh giá.');
        }

        // Chỉ lấy đánh giá có quan_tri_vien_id khớp với cán bộ hiện tại và không NULL
        $query = Rating::where('quan_tri_vien_id', $currentUser->id)
            ->whereNotNull('quan_tri_vien_id') // Đảm bảo không NULL
            ->with(['hoSo.dichVu', 'hoSo.nguoiDung', 'nguoiDung'])
            ->orderBy('created_at', 'desc');

        // Lọc theo điểm
        if ($request->filled('diem')) {
            $query->where('diem', $request->diem);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('hoSo', function($q2) use ($search) {
                    $q2->where('ma_ho_so', 'like', "%{$search}%");
                })->orWhereHas('nguoiDung', function($q2) use ($search) {
                    $q2->where('ten', 'like', "%{$search}%");
                });
            });
        }

        $ratings = $query->paginate(15)->withQueryString();

        // Thống kê - chỉ đếm rating có quan_tri_vien_id khớp và không NULL
        $stats = [
            'tong' => Rating::where('quan_tri_vien_id', $currentUser->id)
                ->whereNotNull('quan_tri_vien_id')
                ->count(),
            'diem_tb' => Rating::where('quan_tri_vien_id', $currentUser->id)
                ->whereNotNull('quan_tri_vien_id')
                ->avg('diem') ?? 0,
            '5_sao' => Rating::where('quan_tri_vien_id', $currentUser->id)
                ->whereNotNull('quan_tri_vien_id')
                ->where('diem', 5)
                ->count(),
            '4_sao' => Rating::where('quan_tri_vien_id', $currentUser->id)
                ->whereNotNull('quan_tri_vien_id')
                ->where('diem', 4)
                ->count(),
            '3_sao' => Rating::where('quan_tri_vien_id', $currentUser->id)
                ->whereNotNull('quan_tri_vien_id')
                ->where('diem', 3)
                ->count(),
            '2_sao' => Rating::where('quan_tri_vien_id', $currentUser->id)
                ->whereNotNull('quan_tri_vien_id')
                ->where('diem', 2)
                ->count(),
            '1_sao' => Rating::where('quan_tri_vien_id', $currentUser->id)
                ->whereNotNull('quan_tri_vien_id')
                ->where('diem', 1)
                ->count(),
        ];

        return view('backend.staff.ratings.index', compact('ratings', 'stats'));
    }

    /**
     * Xem chi tiết đánh giá
     */
    public function show($id)
    {
        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isCanBo()) {
            abort(403);
        }

        $rating = Rating::with(['hoSo.dichVu', 'hoSo.nguoiDung', 'nguoiDung'])
            ->findOrFail($id);

        // Kiểm tra quyền
        if ($rating->quan_tri_vien_id != $currentUser->id) {
            abort(403, 'Bạn không có quyền xem đánh giá này.');
        }

        return view('backend.staff.ratings.show', compact('rating'));
    }
}

