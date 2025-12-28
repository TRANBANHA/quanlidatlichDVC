<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\HoSo;
use App\Models\Comment;
use App\Models\ThongBao;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $date_new = Carbon::today(); // chỉ lấy ngày hiện tại
        $query = HoSo::whereDate('ngay_hen', $date_new)->with('nguoiDung', 'donVi', 'dichVu');
        
        $currentUser = Auth::guard('admin')->user();
        
        if ($currentUser->isAdmin()) {
            // Admin tổng: Xem tất cả hồ sơ
            $ho_so = $query->paginate(10);
        } elseif ($currentUser->isAdminPhuong()) {
            // Admin phường: Xem hồ sơ của phường mình
            $ho_so = $query->where('don_vi_id', $currentUser->don_vi_id)
                ->paginate(10);
        } else {
            // Cán bộ: Xem hồ sơ được phân công
            $ho_so = $query->where('quan_tri_vien_id', $currentUser->id)
                ->paginate(10);
        }
        
        return view('backend.files.index', compact('ho_so'));
    }


    public function sendInfo(Request $request)
    {
        $hoSo = HoSo::find($request->ho_so_id);
        $hoSo->update([
            'trang_thai' => HoSo::STATUS_NEED_SUPPLEMENT
        ]);
        if (!$hoSo || !$hoSo->nguoi_dung_id) {
            return back()->with('error', 'Không tìm thấy người dùng để gửi thông tin.');
        }

        // Ví dụ: gửi tin nhắn qua SMS, Zalo, hoặc chỉ lưu log
        // Ở đây demo là chỉ lưu thông báo vào database
        ThongBao::create([
            'nguoi_dung_id' => $request->nguoi_dung_id,
            'ho_so_id' => $request->ho_so_id,
            'dich_vu_id' => $request->dich_vu_id,
            'ngay_hen' => $request->ngay_hen,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Đã gửi thông tin nhắc nhở cho người dùng.');
    }

    public function sendInfoSuccess(Request $request)
    {
        $hoSo = HoSo::find($request->id);
         $hoSo->update([
            'trang_thai' => HoSo::STATUS_COMPLETED,
            'quan_tri_vien_id' => Auth::guard('admin')->user()->id

        ]);
        return redirect()->back()->with('success', 'Đánh dấu hoàn tất hồ sơ thành công!');
    }
}