<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\HoSo;
use App\Models\DonVi;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\ServiceSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $startDate = ServiceAssignment::min('ngay_phan_cong');
        $maxDate = ServiceAssignment::max('ngay_phan_cong');
        $listDonvi = DonVi::all();
        $services = Service::all();
        return view('website.register_services.index', compact('startDate', 'listDonvi', 'maxDate', 'services'));
    }

    public function getDatesByService(Request $request)
    {
        $serviceId = $request->input('service_id');
        
        // Lấy các thứ trong tuần của dịch vụ từ lich_dich_vu
        $serviceSchedule = ServiceSchedule::where('dich_vu_id', $serviceId)->get();
        $availableDays = $serviceSchedule->pluck('thu_trong_tuan')->toArray();
        
        // Lấy các ngày phân công đặc biệt cho dịch vụ này
        $assignments = ServiceAssignment::where('ma_dich_vu', $serviceId)
            ->get()
            ->pluck('ngay_phan_cong')
            ->map(fn($date) => Carbon::parse($date)->toDateString())
            ->toArray();
        
        // Lấy tất cả các ngày từ hôm nay đến 3 tháng sau
        $startDate = Carbon::today()->toDateString();
        $endDate = Carbon::today()->addMonths(3)->toDateString();
        
        // Tạo danh sách các ngày khả dụng
        $availableDates = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        while ($current <= $end) {
            $dayOfWeek = $current->dayOfWeekIso;
            $dateStr = $current->toDateString();
            
            // Thêm ngày nếu có trong phân công đặc biệt hoặc có trong lịch mặc định
            if (in_array($dateStr, $assignments) || in_array($dayOfWeek, $availableDays)) {
                $availableDates[] = [
                    'date' => $dateStr,
                    'formatted_date' => $current->format('d/m/Y'),
                    'day_of_week' => $dayOfWeek,
                    'day_name' => $current->locale('vi')->dayName,
                ];
            }
            
            $current->addDay();
        }

        return response()->json([
            'dates' => $availableDates,
            'service_id' => $serviceId,
        ]);
    }

    public function getServicesByDate(Request $request)
    {
        $date = $request->input('date');

        // Lấy danh sách giờ hẹn đã đăng ký theo từng đơn vị
        $gioHenList = HoSo::where('ngay_hen', $date)
            ->get(['don_vi_id', 'gio_hen'])
            ->groupBy('don_vi_id')
            ->map(fn($items) => $items->pluck('gio_hen'));

        return response()->json([
            'date' => $date,
            'gioHenList' => $gioHenList,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        // ✅ Bước 1: Validate dữ liệu nhập vào
        $validated = $request->validate([
            'service' => 'required|exists:dich_vu,id',
            'date' => 'required|date|after_or_equal:today',
            'don_vi_id' => 'required|exists:don_vi,id',
            'time' => 'required',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,xlsx,jpg,jpeg,png|max:5120', // 5MB, cho phép ảnh
            'ghi_chu' => 'nullable|string|max:1000',
        ], [
            // ✅ Thông báo lỗi tiếng Việt
            'service.required' => 'Vui lòng chọn dịch vụ.',
            'service.exists' => 'Dịch vụ không hợp lệ.',
            'date.required' => 'Vui lòng chọn ngày hẹn.',
            'date.after_or_equal' => 'Ngày hẹn phải từ hôm nay trở đi.',
            'don_vi_id.required' => 'Vui lòng chọn phường.',
            'don_vi_id.exists' => 'Phường không hợp lệ.',
            'time.required' => 'Vui lòng chọn giờ hẹn.',
            'file_path.mimes' => 'Tệp phải có định dạng: pdf, doc, docx, xlsx, jpg, jpeg, png.',
            'file_path.max' => 'Kích thước tệp tối đa 5MB.',
        ]);

        // ✅ Bước 2: Upload file (nếu có)
        $filePath = null;
        if ($request->hasFile('file_path') && $request->file('file_path')->isValid()) {
            try {
                $file = $request->file('file_path');
                $filename = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('ho_so', $filename, 'public');
            } catch (\Exception $e) {
                return back()->withErrors(['file_path' => 'Lỗi khi upload file: ' . $e->getMessage()])->withInput();
            }
        }
        
        // ✅ Bước 3: Sử dụng transaction và lock để tránh race condition
        DB::beginTransaction();
        try {
            // Normalize ngày để đảm bảo format đúng (chỉ lấy phần date, bỏ time)
            $ngayHen = Carbon::parse($request->date)->format('Y-m-d');
            
            // Log để debug
            \Log::info('Bắt đầu tính số thứ tự', [
                'dich_vu_id' => $request->service,
                'don_vi_id' => $request->don_vi_id,
                'ngay_hen' => $ngayHen,
                'date_raw' => $request->date
            ]);
            
            // Tính số thứ tự: Đếm số lượng hồ sơ đã có (không tính các hồ sơ đã hủy)
            // Sử dụng lockForUpdate để tránh race condition
            $bookedCount = HoSo::where('dich_vu_id', $request->service)
                ->where('don_vi_id', $request->don_vi_id)
                ->whereDate('ngay_hen', $ngayHen) // Sử dụng whereDate để so sánh chỉ phần ngày
                ->where('trang_thai', '!=', HoSo::STATUS_CANCELLED)
                ->lockForUpdate() // Lock để đảm bảo tính nhất quán
                ->count();
            
            // Tính số thứ tự (số lượng đã đăng ký + 1)
            $soThuTu = $bookedCount + 1;
            
            \Log::info('Tính số thứ tự - lần 1', [
                'bookedCount' => $bookedCount,
                'so_thu_tu' => $soThuTu
            ]);
            
            // Tự động phân công cán bộ: Random đều cho tất cả cán bộ của phường (không theo dịch vụ)
            $selectedCanBoId = null;
            
            // Lấy tất cả cán bộ của phường
            $canBoPhuong = \App\Models\Admin::where('don_vi_id', $request->don_vi_id)
                ->where('quyen', \App\Models\Admin::CAN_BO) // Chỉ cán bộ
                ->pluck('id')
                ->toArray();
            
            if (!empty($canBoPhuong)) {
                // Đếm số hồ sơ của từng cán bộ trong ngày (tất cả dịch vụ)
                $canBoWorkloads = [];
                foreach ($canBoPhuong as $canBoId) {
                    $workload = HoSo::where('quan_tri_vien_id', $canBoId)
                        ->whereDate('ngay_hen', $ngayHen) // Sử dụng whereDate
                        ->where('trang_thai', '!=', HoSo::STATUS_CANCELLED)
                        ->count();
                    $canBoWorkloads[$canBoId] = $workload;
                }
                
                // Tìm cán bộ có ít hồ sơ nhất trong ngày
                if (!empty($canBoWorkloads)) {
                    $minWorkload = min($canBoWorkloads);
                    // Lấy tất cả cán bộ có workload thấp nhất
                    $canBoWithMinWorkload = array_keys($canBoWorkloads, $minWorkload);
                    
                    // Random chọn trong số các cán bộ có workload thấp nhất để chia đều
                    $randomIndex = array_rand($canBoWithMinWorkload);
                    $selectedCanBoId = $canBoWithMinWorkload[$randomIndex];
                } else {
                    // Nếu không có hồ sơ nào, random chọn trong tất cả cán bộ phường
                    $selectedCanBoId = $canBoPhuong[array_rand($canBoPhuong)];
                }
            }
            
            // Kiểm tra lại lần cuối trước khi tạo hồ sơ (double check pattern)
            // Đảm bảo không có request nào khác đã tạo hồ sơ trong lúc xử lý
            $finalBookedCount = HoSo::where('dich_vu_id', $request->service)
                ->where('don_vi_id', $request->don_vi_id)
                ->whereDate('ngay_hen', $ngayHen) // Sử dụng whereDate
                ->where('trang_thai', '!=', HoSo::STATUS_CANCELLED) // Sửa: != thay vì =
                ->lockForUpdate()
                ->count();
            
            // Cập nhật lại số thứ tự dựa trên số lượng thực tế
            $soThuTu = $finalBookedCount + 1;
            
            // Log để debug
            \Log::info('Tính số thứ tự - final', [
                'dich_vu_id' => $request->service,
                'don_vi_id' => $request->don_vi_id,
                'ngay_hen' => $ngayHen,
                'finalBookedCount' => $finalBookedCount,
                'so_thu_tu_calculated' => $soThuTu,
                'all_records' => HoSo::where('dich_vu_id', $request->service)
                    ->where('don_vi_id', $request->don_vi_id)
                    ->whereDate('ngay_hen', $ngayHen)
                    ->where('trang_thai', '!=', HoSo::STATUS_CANCELLED)
                    ->get(['id', 'so_thu_tu', 'ma_ho_so', 'ngay_hen'])
                    ->toArray()
            ]);
            
            // Tạo mã hồ sơ trước
            $maHoSo = HoSo::generateCode();
            
            // ✅ Bước 4: Lưu dữ liệu   
            $hoSo = HoSo::create([
                'ma_ho_so' => $maHoSo,
                'dich_vu_id' => $request->service,
                'nguoi_dung_id' => Auth::user()->id,
                'don_vi_id' => $request->don_vi_id,
                'gio_hen' => $request->time,
                'ngay_hen' => $ngayHen, // Sử dụng ngày đã normalize
                'so_thu_tu' => $soThuTu,
                'ghi_chu' => $request->ghi_chu,
                'trang_thai' => HoSo::STATUS_RECEIVED,
                'file_path' => $filePath,
                'quan_tri_vien_id' => $selectedCanBoId, // Thêm cán bộ được chọn
            ]);
            
            // Commit transaction
            DB::commit();
            
            return back()->with('success', 'Đăng ký thành công! Số thứ tự của bạn: ' . $soThuTu);
        } catch (\Exception $e) {
            // Rollback nếu có lỗi
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi đăng ký: ' . $e->getMessage()])->withInput();
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
