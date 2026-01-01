<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\HoSo;
use App\Models\Service;
use App\Models\Comment;
use App\Models\ThongBao;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        
        // Phân quyền xem hồ sơ
        if ($currentUser->isAdmin()) {
            // Admin tổng: Xem tất cả hồ sơ
        } elseif ($currentUser->isAdminPhuong()) {
            // Admin phường: Xem hồ sơ của phường mình
            $query->where('don_vi_id', $currentUser->don_vi_id);
        } else {
            // Cán bộ: Xem hồ sơ được phân công
            $query->where('quan_tri_vien_id', $currentUser->id);
        }
        
        // Filter theo dịch vụ
        if ($request->filled('dich_vu_id')) {
            $query->where('dich_vu_id', $request->dich_vu_id);
        }
        
        // Lấy danh sách dịch vụ để hiển thị filter
        $services = Service::orderBy('ten_dich_vu')->get();
        
        // Tính số lượng hồ sơ theo dịch vụ (cho Admin phường và Cán bộ)
        $serviceCounts = [];
        if ($currentUser->isAdminPhuong() || $currentUser->isCanBo()) {
            $countQuery = HoSo::whereDate('ngay_hen', $date_new);
            
            // Áp dụng quyền
            if ($currentUser->isAdminPhuong()) {
                $countQuery->where('don_vi_id', $currentUser->don_vi_id);
            } else {
                $countQuery->where('quan_tri_vien_id', $currentUser->id);
            }
            
            // Đếm theo dịch vụ
            $counts = $countQuery->selectRaw('dich_vu_id, COUNT(*) as count')
                ->groupBy('dich_vu_id')
                ->pluck('count', 'dich_vu_id')
                ->toArray();
            
            foreach ($services as $service) {
                $serviceCounts[$service->id] = $counts[$service->id] ?? 0;
            }
            
            // Tổng số hồ sơ
            $serviceCounts['all'] = $countQuery->count();
        }
        
        $ho_so = $query->paginate(10)->withQueryString();
        
        return view('backend.files.index', compact('ho_so', 'services', 'serviceCounts', 'currentUser'));
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
        $thongBao = ThongBao::create([
            'nguoi_dung_id' => $request->nguoi_dung_id,
            'ho_so_id' => $request->ho_so_id,
            'dich_vu_id' => $request->dich_vu_id,
            'ngay_hen' => $request->ngay_hen,
            'message' => $request->message,
        ]);

        // Load relationships và gửi email thông báo cho người dùng
        try {
            $thongBao->load(['NguoiDung', 'hoSo', 'dichVu']);
            if ($thongBao->NguoiDung && $thongBao->NguoiDung->email) {
                \Illuminate\Support\Facades\Mail::to($thongBao->NguoiDung->email)
                    ->send(new \App\Mail\NotificationMail($thongBao));
            }
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi email thông báo: ' . $e->getMessage());
        }

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