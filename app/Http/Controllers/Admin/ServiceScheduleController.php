<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceSchedule;
use Illuminate\Http\Request;

class ServiceScheduleController extends Controller
{
    /**
     * Hiển thị cấu hình lịch dịch vụ.
     */
    public function index()
    {
        $allServices = Service::all();


        foreach (range(1, 5) as $thu) {
            $lichDichVu = ServiceSchedule::where('thu_trong_tuan', $thu)->exists();
            if (!$lichDichVu) {
                ServiceSchedule::create([
                    'dich_vu_id' => 1,
                    'thu_trong_tuan' => $thu,
                    'gio_bat_dau' => '08:00',
                    'gio_ket_thuc' => '17:00',
                    'so_luong_toi_da' => 10,
                    'trang_thai' => true,
                ]);
            }
        }
        $lichDichVu = ServiceSchedule::all();
        return view('backend.serviceSchedule.index', compact('lichDichVu', 'allServices'));
    }

    /**
     * Cập nhật toàn bộ cấu hình lịch.
     */
    public function store(Request $request)
    {
        $id_dichvu = $request->input("dich_vu_id");

        foreach ($id_dichvu as $index => $dichvu) {
            $gioBatDau = $request->input("gio_bat_dau.$index");
            $gioKetThuc = $request->input("gio_ket_thuc.$index");
            $soLuong = $request->input("so_luong_toi_da.$index");
            $trangThai = $request->input("trang_thai.$index");
            $ghiChu = $request->input("ghi_chu.$index");

            // Xử lý file upload
            $originalName = null;
            $filePath = null;

            if ($request->hasFile("file_dinh_kem.$index")) {
                $file = $request->file("file_dinh_kem.$index");
                $originalName = $file->getClientOriginalName(); // Tên gốc
                $filePath = $file->store('files', 'public'); // Lưu file vào storage/app/public/files
            }


            $record = ServiceSchedule::where('thu_trong_tuan', $index)->first();

            if ($record) {
                // Nếu có file mới → cập nhật cả file; nếu không → giữ nguyên file cũ
                $record->update([
                    'dich_vu_id' => $dichvu,
                    'gio_bat_dau' => $gioBatDau,
                    'gio_ket_thuc' => $gioKetThuc,
                    'so_luong_toi_da' => $soLuong,
                    'trang_thai' => $trangThai,
                    'ghi_chu' => $ghiChu,
                    'file_dinh_kem' => $filePath ?? $record->file_dinh_kem,
                ]);
            } else {
                // Nếu chưa có bản ghi thì tạo mới
                ServiceSchedule::create([
                    'dich_vu_id' => $dichvu,
                    'thu_trong_tuan' => $index,
                    'gio_bat_dau' => $gioBatDau,
                    'gio_ket_thuc' => $gioKetThuc,
                    'so_luong_toi_da' => $soLuong,
                    'trang_thai' => $trangThai,
                    'ghi_chu' => $ghiChu,
                    'file_dinh_kem' => $filePath,
                ]);
            }
        }

        return redirect()->route('services-schedules.index')
            ->with('success', 'Đã lưu cấu hình thành công!');
    }

}
