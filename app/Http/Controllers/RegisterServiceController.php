<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\HoSo;
use App\Models\DonVi;
use App\Models\Service;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\ServiceSchedule;
use App\Models\ServiceScheduleStaff;
use App\Models\ServiceAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        // Tự động phân công cán bộ dựa trên nhiều nguồn
        $selectedCanBoId = null;
        
        // Ưu tiên 1: Tìm từ ServiceAssignment (phân công theo ngày cụ thể)
        $assigned = ServiceAssignment::where('ngay_phan_cong', $request->date)
            ->where('ma_dich_vu', $request->service)
            ->first();
        // ma_can_bo đã được cast thành array trong model ServiceAssignment
        $canBoIds = $assigned && $assigned->ma_can_bo ? (array) $assigned->ma_can_bo : [];

        if (!empty($canBoIds)) {
            // Tính workload của từng cán bộ trong ngày
            $canBoWorkloads = [];
            foreach ($canBoIds as $canBoId) {
                $count = HoSo::where('ngay_hen', $request->date)
                    ->where('quan_tri_vien_id', $canBoId)
                    ->count();
                $canBoWorkloads[$canBoId] = $count;
            }

            // Chọn cán bộ có ít hồ sơ nhất (load balancing)
            $selectedCanBoId = array_key_first($canBoWorkloads);
            $minWorkload = $canBoWorkloads[$selectedCanBoId];

            foreach ($canBoWorkloads as $canBoId => $workload) {
                if ($workload < $minWorkload) {
                    $selectedCanBoId = $canBoId;
                    $minWorkload = $workload;
                }
            }
        }
        
        // Fallback 2: Nếu không có phân công theo ngày, tìm từ ServiceSchedule
        if (!$selectedCanBoId) {
            // Lấy thứ trong tuần của ngày hẹn (1 = Thứ 2, 7 = Chủ nhật)
            $ngayHen = Carbon::parse($request->date);
            $thuTrongTuan = $ngayHen->dayOfWeek; // 0 = Chủ nhật, 1 = Thứ 2, ..., 6 = Thứ 7
            // Chuyển đổi: 0 (Chủ nhật) -> 7, 1-6 giữ nguyên
            $thuTrongTuan = $thuTrongTuan == 0 ? 7 : $thuTrongTuan;
            
            // Tìm schedule của dịch vụ vào thứ đó
            $schedule = ServiceSchedule::where('dich_vu_id', $request->service)
                ->where('thu_trong_tuan', $thuTrongTuan)
                ->where('trang_thai', true)
                ->first();
            
            if ($schedule) {
                // Lấy các cán bộ đã được phân công vào schedule này
                $canBoIds = ServiceScheduleStaff::where('schedule_id', $schedule->id)
                    ->pluck('can_bo_id')
                    ->toArray();
                
                if (!empty($canBoIds)) {
                    // Random chọn 1 cán bộ trong danh sách
                    $selectedCanBoId = $canBoIds[array_rand($canBoIds)];
                    Log::info('Sử dụng cán bộ từ schedule', [
                        'schedule_id' => $schedule->id,
                        'dich_vu_id' => $request->service,
                        'thu_trong_tuan' => $thuTrongTuan,
                        'quan_tri_vien_id' => $selectedCanBoId
                    ]);
                }
            }
        }
        
        // Fallback 3: Nếu vẫn không có, random từ cán bộ của phường
        if (!$selectedCanBoId) {
            $canBoPhuong = Admin::where('don_vi_id', $request->don_vi_id)
                ->where('quyen', 0) // Cán bộ (quyen = 0)
                ->where('trang_thai', true)
                ->pluck('id')
                ->toArray();
            
            if (!empty($canBoPhuong)) {
                // Random chọn 1 cán bộ của phường
                $selectedCanBoId = $canBoPhuong[array_rand($canBoPhuong)];
                Log::info('Sử dụng cán bộ phường làm fallback', [
                    'dich_vu_id' => $request->service,
                    'don_vi_id' => $request->don_vi_id,
                    'quan_tri_vien_id' => $selectedCanBoId
                ]);
            } else {
                Log::warning('Không tìm thấy cán bộ để phân công', [
                    'dich_vu_id' => $request->service,
                    'don_vi_id' => $request->don_vi_id,
                    'ngay_hen' => $request->date
                ]);
            }
        }
        // ✅ Bước 3: Lưu dữ liệu   
        HoSo::create([
            'ma_ho_so' => HoSo::generateCode(),
            'dich_vu_id' => $request->service,
            'nguoi_dung_id' => Auth::user()->id,
            'don_vi_id' => $request->don_vi_id,
            'gio_hen' => $request->time,
            'ngay_hen' => $request->date,
            'ghi_chu' => $request->ghi_chu,
            'trang_thai' => HoSo::STATUS_RECEIVED,
            'file_path' => $filePath,
            'quan_tri_vien_id' => $selectedCanBoId, // Thêm cán bộ được chọn
        ]);

        return back()->with('success', 'Đăng ký thành công!');
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
