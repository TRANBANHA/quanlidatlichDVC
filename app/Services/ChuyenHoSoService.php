<?php

namespace App\Services;

use App\Models\HoSo;
use App\Models\Admin;
use App\Models\CanBoNghi;
use App\Models\ThongBao;
use App\Mail\NotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChuyenHoSoService
{
    /**
     * Tự động chuyển hồ sơ của cán bộ nghỉ sang cán bộ khác
     * Chỉ chuyển khi báo nghỉ đã được duyệt
     * 
     * @param int $canBoId ID cán bộ nghỉ
     * @param string|Carbon $ngayNghi Ngày nghỉ
     * @return array Kết quả chuyển hồ sơ
     */
    public function chuyenHoSoKhiCanBoNghi($canBoId, $ngayNghi)
    {
        $ngayNghi = Carbon::parse($ngayNghi)->format('Y-m-d');
        
        // Kiểm tra báo nghỉ đã được duyệt chưa
        $canBoNghiRecord = \App\Models\CanBoNghi::where('can_bo_id', $canBoId)
            ->whereDate('ngay_nghi', $ngayNghi)
            ->where('trang_thai', \App\Models\CanBoNghi::TRANG_THAI_DA_DUYET)
            ->first();
        
        if (!$canBoNghiRecord) {
            return [
                'success' => false,
                'message' => 'Báo nghỉ chưa được duyệt hoặc không tồn tại',
            ];
        }
        
        // Lấy cán bộ nghỉ
        $canBoNghi = Admin::find($canBoId);
        if (!$canBoNghi) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy cán bộ',
            ];
        }

        // Lấy tất cả hồ sơ của cán bộ trong ngày nghỉ (chưa hủy)
        $hoSos = HoSo::where('quan_tri_vien_id', $canBoId)
            ->whereDate('ngay_hen', $ngayNghi)
            ->where('trang_thai', '!=', HoSo::STATUS_CANCELLED)
            ->get();

        if ($hoSos->isEmpty()) {
            return [
                'success' => true,
                'message' => 'Không có hồ sơ nào cần chuyển',
                'so_ho_so_chuyen' => 0,
            ];
        }

        $soHoSoChuyen = 0;
        $hoSoChuyenThanhCong = [];
        $hoSoChuyenThatBai = [];

        foreach ($hoSos as $hoSo) {
            try {
                // Tìm cán bộ thay thế (cùng phường, không nghỉ, có ít hồ sơ nhất)
                $canBoThayThe = $this->timCanBoThayThe($hoSo->don_vi_id, $ngayNghi, $canBoId);
                
                if (!$canBoThayThe) {
                    $hoSoChuyenThatBai[] = [
                        'ho_so' => $hoSo,
                        'ly_do' => 'Không tìm thấy cán bộ thay thế',
                    ];
                    continue;
                }

                // Lưu cán bộ cũ để thông báo
                $canBoCu = $hoSo->quanTriVien;
                
                // Chuyển hồ sơ
                $hoSo->quan_tri_vien_id = $canBoThayThe->id;
                $hoSo->save();

                // Gửi thông báo cho người dùng
                $this->guiThongBaoChuyenCanBo($hoSo, $canBoCu, $canBoThayThe);

                $soHoSoChuyen++;
                $hoSoChuyenThanhCong[] = [
                    'ho_so' => $hoSo,
                    'can_bo_moi' => $canBoThayThe,
                ];

                Log::info('Đã chuyển hồ sơ khi cán bộ nghỉ', [
                    'ho_so_id' => $hoSo->id,
                    'ma_ho_so' => $hoSo->ma_ho_so,
                    'can_bo_cu_id' => $canBoId,
                    'can_bo_moi_id' => $canBoThayThe->id,
                    'ngay_nghi' => $ngayNghi,
                ]);

            } catch (\Exception $e) {
                Log::error('Lỗi chuyển hồ sơ khi cán bộ nghỉ', [
                    'ho_so_id' => $hoSo->id,
                    'error' => $e->getMessage(),
                ]);
                
                $hoSoChuyenThatBai[] = [
                    'ho_so' => $hoSo,
                    'ly_do' => $e->getMessage(),
                ];
            }
        }

        // Đánh dấu đã chuyển hồ sơ (chỉ đánh dấu báo nghỉ đã được duyệt)
        \App\Models\CanBoNghi::where('can_bo_id', $canBoId)
            ->whereDate('ngay_nghi', $ngayNghi)
            ->where('trang_thai', \App\Models\CanBoNghi::TRANG_THAI_DA_DUYET)
            ->update(['da_chuyen_ho_so' => true]);

        return [
            'success' => true,
            'message' => "Đã chuyển {$soHoSoChuyen} hồ sơ",
            'so_ho_so_chuyen' => $soHoSoChuyen,
            'ho_so_chuyen_thanh_cong' => $hoSoChuyenThanhCong,
            'ho_so_chuyen_that_bai' => $hoSoChuyenThatBai,
        ];
    }

    /**
     * Tìm cán bộ thay thế (cùng phường, không nghỉ, có ít hồ sơ nhất)
     */
    private function timCanBoThayThe($donViId, $ngayNghi, $canBoNghiId)
    {
        // Lấy danh sách cán bộ nghỉ trong ngày
        $canBoNghiIds = CanBoNghi::danhSachCanBoNghiTrongNgay($ngayNghi, $donViId);
        $canBoNghiIds[] = $canBoNghiId; // Thêm cán bộ hiện tại vào danh sách loại trừ

        // Lấy tất cả cán bộ của phường (không nghỉ)
        $canBoPhuong = Admin::where('don_vi_id', $donViId)
            ->where('quyen', Admin::CAN_BO)
            ->whereNotIn('id', $canBoNghiIds)
            ->pluck('id')
            ->toArray();

        if (empty($canBoPhuong)) {
            return null;
        }

        // Đếm số hồ sơ của từng cán bộ trong ngày
        $canBoWorkloads = [];
        foreach ($canBoPhuong as $canBoId) {
            $workload = HoSo::where('quan_tri_vien_id', $canBoId)
                ->whereDate('ngay_hen', $ngayNghi)
                ->where('trang_thai', '!=', HoSo::STATUS_CANCELLED)
                ->count();
            $canBoWorkloads[$canBoId] = $workload;
        }

        // Tìm cán bộ có ít hồ sơ nhất
        if (!empty($canBoWorkloads)) {
            $minWorkload = min($canBoWorkloads);
            $canBoWithMinWorkload = array_keys($canBoWorkloads, $minWorkload);
            
            // Random chọn trong số các cán bộ có workload thấp nhất
            $randomIndex = array_rand($canBoWithMinWorkload);
            $selectedCanBoId = $canBoWithMinWorkload[$randomIndex];
            
            return Admin::find($selectedCanBoId);
        } else {
            // Nếu không có hồ sơ nào, random chọn trong tất cả cán bộ phường
            $randomIndex = array_rand($canBoPhuong);
            return Admin::find($canBoPhuong[$randomIndex]);
        }
    }

    /**
     * Gửi thông báo cho người dùng khi hồ sơ được chuyển cán bộ
     */
    private function guiThongBaoChuyenCanBo($hoSo, $canBoCu, $canBoMoi)
    {
        try {
            $message = "Hồ sơ {$hoSo->ma_ho_so} của bạn đã được chuyển từ cán bộ {$canBoCu->ho_ten} sang cán bộ {$canBoMoi->ho_ten}. Vui lòng liên hệ cán bộ mới nếu cần hỗ trợ.";

            $thongBao = ThongBao::create([
                'ho_so_id' => $hoSo->id,
                'nguoi_dung_id' => $hoSo->nguoi_dung_id,
                'dich_vu_id' => $hoSo->dich_vu_id,
                'ngay_hen' => $hoSo->ngay_hen,
                'message' => $message,
                'is_read' => false,
            ]);

            // Gửi email thông báo
            if ($hoSo->nguoiDung && $hoSo->nguoiDung->email) {
                $thongBao->load(['NguoiDung', 'hoSo', 'dichVu']);
                Mail::to($hoSo->nguoiDung->email)->send(new NotificationMail($thongBao));
            }
        } catch (\Exception $e) {
            Log::error('Lỗi gửi thông báo chuyển cán bộ', [
                'ho_so_id' => $hoSo->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
