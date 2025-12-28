<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\DonVi;
use App\Models\Service;
use App\Models\ServicePhuong;
use App\Models\ServiceSchedule;
use App\Models\ServiceScheduleStaff;
use App\Models\HoSo;
use App\Models\Admin;
use App\Models\HoSoField;
use App\Models\ThongBao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Bước 1: Chọn phường
     */
    public function selectPhuong()
    {
        $donVis = DonVi::orderBy('ten_don_vi')->get();
        return view('website.booking.select-phuong', compact('donVis'));
    }

    /**
     * Bước 2: Chọn dịch vụ (theo phường đã chọn)
     */
    public function selectService(Request $request)
    {
        $request->validate([
            'don_vi_id' => 'required|exists:don_vi,id'
        ]);

        $donViId = $request->don_vi_id;
        $donVi = DonVi::findOrFail($donViId);

        // Lấy các dịch vụ có kích hoạt cho phường này
        $services = Service::whereHas('servicePhuongs', function($query) use ($donViId) {
            $query->where('don_vi_id', $donViId)
                  ->where('kich_hoat', true);
        })->with(['servicePhuongs' => function($query) use ($donViId) {
            $query->where('don_vi_id', $donViId);
        }])->get();

        return view('website.booking.select-service', compact('donVi', 'services'));
    }

    /**
     * Bước 3: Chọn ngày (theo lịch dịch vụ)
     */
    public function selectDate(Request $request)
    {
        // Xử lý cả GET và POST
        if ($request->isMethod('post')) {
            $request->validate([
                'don_vi_id' => 'required|exists:don_vi,id',
                'dich_vu_id' => 'required|exists:dich_vu,id'
            ]);
        } else {
            // GET request - kiểm tra query params
            if (!$request->has(['don_vi_id', 'dich_vu_id']) || 
                !$request->filled(['don_vi_id', 'dich_vu_id'])) {
                return redirect()->route('booking.select-phuong')
                    ->with('error', 'Vui lòng chọn lại thông tin đặt lịch.');
            }
        }

        $donViId = $request->don_vi_id;
        $dichVuId = $request->dich_vu_id;

        $donVi = DonVi::findOrFail($donViId);
        $dichVu = Service::findOrFail($dichVuId);
        $servicePhuong = ServicePhuong::where('dich_vu_id', $dichVuId)
            ->where('don_vi_id', $donViId)
            ->firstOrFail();

        // Lấy lịch dịch vụ
        $schedules = ServiceSchedule::where('dich_vu_id', $dichVuId)
            ->where('trang_thai', true)
            ->get();

        // Tạo danh sách ngày khả dụng (bắt đầu từ sau 2 tuần, trong 30 ngày)
        $availableDates = $this->getAvailableDates($schedules, $servicePhuong, 30, 14);

        return view('website.booking.select-date', compact('donVi', 'dichVu', 'servicePhuong', 'availableDates'));
    }

    /**
     * Bước 4: Upload form hồ sơ
     */
    public function uploadForm(Request $request)
    {
        // POST request - validation đầy đủ
        if ($request->isMethod('post')) {
            $request->validate([
                'don_vi_id' => 'required|exists:don_vi,id',
                'dich_vu_id' => 'required|exists:dich_vu,id',
                'ngay_hen' => 'required|date|after_or_equal:today',
                'gio_hen' => 'required'
            ]);
        } else {
            // GET request - kiểm tra query params, không dùng validation để tránh redirect loop
            if (!$request->has(['don_vi_id', 'dich_vu_id', 'ngay_hen', 'gio_hen']) || 
                !$request->filled(['don_vi_id', 'dich_vu_id', 'ngay_hen', 'gio_hen'])) {
                // Nếu thiếu params, redirect về trang chọn phường
                return redirect()->route('booking.select-phuong')
                    ->with('error', 'Vui lòng chọn lại thông tin đặt lịch.');
            }
        }

        try {
            $donVi = DonVi::findOrFail($request->don_vi_id);
            $dichVu = Service::with('serviceFields')->findOrFail($request->dich_vu_id);
            $servicePhuong = ServicePhuong::where('dich_vu_id', $request->dich_vu_id)
                ->where('don_vi_id', $request->don_vi_id)
                ->firstOrFail();

            $ngayHen = $request->ngay_hen;
            $gioHen = $request->gio_hen;

            return view('website.booking.upload-form', compact('donVi', 'dichVu', 'servicePhuong', 'ngayHen', 'gioHen'));
        } catch (\Exception $e) {
            // Nếu có lỗi (không tìm thấy đơn vị, dịch vụ, etc), redirect về trang chọn phường
            return redirect()->route('booking.select-phuong')
                ->with('error', 'Thông tin không hợp lệ. Vui lòng chọn lại.');
        }
    }

    /**
     * Bước 5: Xác nhận và lưu đặt lịch
     */
    public function store(Request $request)
    {
        // Kiểm tra user đã đăng nhập chưa
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login.form')
                ->with('error', 'Vui lòng đăng nhập để đặt lịch.')
                ->with('redirect', route('booking.upload-form', [
                    'don_vi_id' => $request->don_vi_id,
                    'dich_vu_id' => $request->dich_vu_id,
                    'ngay_hen' => $request->ngay_hen,
                    'gio_hen' => $request->gio_hen
                ]));
        }

        // Validation cơ bản - sử dụng Validator để có thể redirect tùy chỉnh
        $validator = \Validator::make($request->all(), [
            'don_vi_id' => 'required|exists:don_vi,id',
            'dich_vu_id' => 'required|exists:dich_vu,id',
            'ngay_hen' => 'required|date|after_or_equal:today',
            'gio_hen' => 'required',
            'ghi_chu' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            // Nếu thiếu tham số cơ bản, redirect về select-phuong
            if (!$request->has(['don_vi_id', 'dich_vu_id', 'ngay_hen', 'gio_hen']) || 
                !$request->filled(['don_vi_id', 'dich_vu_id', 'ngay_hen', 'gio_hen'])) {
                return redirect()->route('booking.select-phuong')
                    ->with('error', 'Vui lòng chọn lại thông tin đặt lịch.');
            }
            
            return redirect()->route('booking.upload-form', [
                'don_vi_id' => $request->don_vi_id,
                'dich_vu_id' => $request->dich_vu_id,
                'ngay_hen' => $request->ngay_hen,
                'gio_hen' => $request->gio_hen
            ])->withErrors($validator)->withInput();
        }

        // Lấy dịch vụ để validate các trường động
        $dichVu = Service::with('serviceFields')->findOrFail($request->dich_vu_id);
        
        // Validation cho các trường động
        $dynamicRules = [];
        foreach ($dichVu->serviceFields as $field) {
            if ($field->bat_buoc) {
                if ($field->loai_truong === 'file') {
                    $dynamicRules[$field->ten_truong] = 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120';
                } else {
                    $dynamicRules[$field->ten_truong] = 'required';
                }
            }
        }
        
        if (!empty($dynamicRules)) {
            $dynamicValidator = \Validator::make($request->all(), $dynamicRules);
            if ($dynamicValidator->fails()) {
                return redirect()->route('booking.upload-form', [
                    'don_vi_id' => $request->don_vi_id,
                    'dich_vu_id' => $request->dich_vu_id,
                    'ngay_hen' => $request->ngay_hen,
                    'gio_hen' => $request->gio_hen
                ])->withErrors($dynamicValidator)->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $user = Auth::guard('web')->user();
            
            // Kiểm tra số lượng còn trống
            $servicePhuong = ServicePhuong::where('dich_vu_id', $request->dich_vu_id)
                ->where('don_vi_id', $request->don_vi_id)
                ->firstOrFail();

            $bookedCount = HoSo::where('dich_vu_id', $request->dich_vu_id)
                ->where('don_vi_id', $request->don_vi_id)
                ->where('ngay_hen', $request->ngay_hen)
                ->where('trang_thai', '!=', HoSo::STATUS_CANCELLED)
                ->count();

            if ($bookedCount >= $servicePhuong->so_luong_toi_da) {
                return redirect()->route('booking.upload-form', [
                    'don_vi_id' => $request->don_vi_id,
                    'dich_vu_id' => $request->dich_vu_id,
                    'ngay_hen' => $request->ngay_hen,
                    'gio_hen' => $request->gio_hen
                ])->withErrors(['ngay_hen' => 'Ngày này đã hết chỗ. Vui lòng chọn ngày khác.'])->withInput();
            }

            // Tính số thứ tự (số lượng đã đăng ký + 1)
            $soThuTu = $bookedCount + 1;

            // Tự động phân công cán bộ dựa trên lịch dịch vụ
            $quanTriVienId = null;
            
            // Lấy thứ trong tuần của ngày hẹn (1 = Thứ 2, 7 = Chủ nhật)
            $ngayHen = Carbon::parse($request->ngay_hen);
            $thuTrongTuan = $ngayHen->dayOfWeek; // 0 = Chủ nhật, 1 = Thứ 2, ..., 6 = Thứ 7
            // Chuyển đổi: 0 (Chủ nhật) -> 7, 1-6 giữ nguyên
            $thuTrongTuan = $thuTrongTuan == 0 ? 7 : $thuTrongTuan;
            
            // Tìm schedule của dịch vụ vào thứ đó
            $schedule = ServiceSchedule::where('dich_vu_id', $request->dich_vu_id)
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
                    $quanTriVienId = $canBoIds[array_rand($canBoIds)];
                } else {
                    // Nếu schedule không có cán bộ, log để debug
                    \Log::warning('Schedule không có cán bộ được gán', [
                        'schedule_id' => $schedule->id,
                        'dich_vu_id' => $request->dich_vu_id,
                        'thu_trong_tuan' => $thuTrongTuan,
                        'ngay_hen' => $request->ngay_hen
                    ]);
                }
            } else {
                // Nếu không tìm thấy schedule, thử tìm cán bộ của phường làm fallback
                $canBoPhuong = Admin::where('don_vi_id', $request->don_vi_id)
                    ->where('quyen', 0) // Cán bộ
                    ->where('trang_thai', true)
                    ->pluck('id')
                    ->toArray();
                
                if (!empty($canBoPhuong)) {
                    // Random chọn 1 cán bộ của phường
                    $quanTriVienId = $canBoPhuong[array_rand($canBoPhuong)];
                    \Log::info('Không tìm thấy schedule, sử dụng cán bộ phường làm fallback', [
                        'dich_vu_id' => $request->dich_vu_id,
                        'don_vi_id' => $request->don_vi_id,
                        'thu_trong_tuan' => $thuTrongTuan,
                        'quan_tri_vien_id' => $quanTriVienId
                    ]);
                } else {
                    \Log::warning('Không tìm thấy schedule và không có cán bộ phường', [
                        'dich_vu_id' => $request->dich_vu_id,
                        'don_vi_id' => $request->don_vi_id,
                        'thu_trong_tuan' => $thuTrongTuan,
                        'ngay_hen' => $request->ngay_hen
                    ]);
                }
            }

            // Tạo mã hồ sơ trước
            $maHoSo = HoSo::generateCode();
            
            // Tạo hồ sơ
            $hoSo = HoSo::create([
                'ma_ho_so' => $maHoSo,
                'dich_vu_id' => $request->dich_vu_id,
                'nguoi_dung_id' => $user->id,
                'don_vi_id' => $request->don_vi_id,
                'ngay_hen' => $request->ngay_hen,
                'gio_hen' => $request->gio_hen,
                'so_thu_tu' => $soThuTu,
                'ghi_chu' => $request->ghi_chu,
                'trang_thai' => HoSo::STATUS_RECEIVED,
                'quan_tri_vien_id' => $quanTriVienId, // Tự động phân công cán bộ
            ]);

            // Refresh để đảm bảo có ma_ho_so
            $hoSo->refresh();
            
            // Đảm bảo ma_ho_so có giá trị
            if (empty($hoSo->ma_ho_so)) {
                $hoSo->ma_ho_so = $maHoSo ?: 'HS' . now()->format('Ymd') . strtoupper(Str::random(4)) . '-' . $hoSo->id;
                $hoSo->save();
            }

            // Lưu các trường dữ liệu động
            foreach ($dichVu->serviceFields as $field) {
                $fieldName = $field->ten_truong;
                $fieldValue = $request->input($fieldName);

                // Xử lý upload file
                if ($field->loai_truong === 'file' && $request->hasFile($fieldName)) {
                    $file = $request->file($fieldName);
                    
                    // Kiểm tra file hợp lệ
                    if (!$file->isValid()) {
                        throw new \Exception('File không hợp lệ: ' . $fieldName);
                    }
                    
                    // Đảm bảo ma_ho_so có giá trị, nếu không dùng ID
                    $maHoSoForPath = $hoSo->ma_ho_so ?: ('ho-so-' . $hoSo->id);
                    
                    // Làm sạch ma_ho_so để dùng trong path (loại bỏ ký tự đặc biệt)
                    $maHoSoForPath = preg_replace('/[^a-zA-Z0-9\-_]/', '-', $maHoSoForPath);
                    
                    // Đảm bảo ma_ho_so không rỗng
                    if (empty($maHoSoForPath)) {
                        $maHoSoForPath = 'hoso-' . $hoSo->id;
                    }
                    
                    // Tạo filename an toàn
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    
                    // Nếu không có extension, lấy từ MIME type hoặc dùng 'bin'
                    if (empty($extension)) {
                        $mimeType = $file->getMimeType();
                        $extension = match(true) {
                            str_contains($mimeType, 'pdf') => 'pdf',
                            str_contains($mimeType, 'image') => 'jpg',
                            str_contains($mimeType, 'word') => 'doc',
                            default => 'bin'
                        };
                    }
                    
                    $safeFieldName = Str::slug($fieldName);
                    // Đảm bảo safeFieldName không rỗng
                    if (empty($safeFieldName)) {
                        $safeFieldName = 'file';
                    }
                    
                    // Tạo filename, đảm bảo không rỗng
                    $filename = time() . '_' . $safeFieldName . '.' . $extension;
                    
                    // Kiểm tra filename không rỗng
                    if (empty($filename) || $filename === '.' . $extension || !str_contains($filename, '.')) {
                        $filename = time() . '_file.' . $extension;
                    }
                    
                    // Đảm bảo filename không rỗng sau khi tạo lại
                    if (empty(trim($filename))) {
                        $filename = time() . '_document.' . $extension;
                    }
                    
                    // Tạo directory path, đảm bảo không rỗng
                    $directory = 'ho-so/' . trim($maHoSoForPath, '/');
                    
                    if (empty($directory) || $directory === 'ho-so/' || $directory === 'ho-so') {
                        $directory = 'ho-so/hoso-' . $hoSo->id;
                    }
                    
                    // Loại bỏ ký tự đặc biệt khỏi directory
                    $directory = preg_replace('/[^a-zA-Z0-9\/\-_]/', '-', $directory);
                    
                    // Đảm bảo directory không rỗng
                    if (empty(trim($directory, '/'))) {
                        $directory = 'ho-so/hoso-' . $hoSo->id;
                    }
                    
                    // Kiểm tra lại directory và filename trước khi upload
                    $directory = trim($directory);
                    $filename = trim($filename);
                    
                    if (empty($directory) || empty($filename)) {
                        \Log::error('Invalid path before upload', [
                            'directory' => $directory,
                            'filename' => $filename,
                            'ma_ho_so' => $hoSo->ma_ho_so,
                            'ho_so_id' => $hoSo->id,
                            'field_name' => $fieldName,
                            'extension' => $extension,
                            'safeFieldName' => $safeFieldName,
                            'maHoSoForPath' => $maHoSoForPath
                        ]);
                        throw new \Exception('Không thể tạo đường dẫn lưu file. Directory: "' . $directory . '", Filename: "' . $filename . '"');
                    }
                    
                    // Log trước khi upload để debug
                    \Log::info('Uploading file', [
                        'directory' => $directory,
                        'filename' => $filename,
                        'full_path' => $directory . '/' . $filename,
                        'ma_ho_so' => $hoSo->ma_ho_so,
                        'field_name' => $fieldName,
                        'file_size' => $file->getSize()
                    ]);
                    
                    // Upload file
                    try {
                        // Kiểm tra lại lần cuối
                        if (empty($directory) || empty($filename)) {
                            throw new \Exception('Directory hoặc filename rỗng. Directory: "' . $directory . '", Filename: "' . $filename . '"');
                        }
                        
                        // Đảm bảo directory và filename không chứa ký tự đặc biệt nguy hiểm
                        $directory = str_replace(['..', '//'], ['', '/'], $directory);
                        $filename = str_replace(['..', '/', '\\'], ['', '-', '-'], $filename);
                        
                        // Tạo full path để kiểm tra
                        $fullPath = $directory . '/' . $filename;
                        
                        // Kiểm tra lại sau khi làm sạch
                        if (empty(trim($directory)) || empty(trim($filename))) {
                            throw new \Exception('Directory hoặc filename rỗng sau khi làm sạch. Directory: "' . $directory . '", Filename: "' . $filename . '"');
                        }
                        
                        // Gọi storeAs với validation - đảm bảo cả hai tham số đều không rỗng
                        \Log::info('Calling storeAs', [
                            'directory' => $directory,
                            'filename' => $filename,
                            'directory_empty' => empty($directory),
                            'filename_empty' => empty($filename),
                            'directory_length' => strlen($directory),
                            'filename_length' => strlen($filename)
                        ]);
                        
                        // Dùng Storage::put() với nội dung file thay vì putFileAs() để tránh lỗi "Path cannot be empty"
                        try {
                            // Đảm bảo directory tồn tại
                            $cleanDirectory = rtrim($directory, '/');
                            
                            // Kiểm tra lại directory và filename trước khi upload
                            if (empty($cleanDirectory) || empty($filename)) {
                                throw new \Exception('Directory hoặc filename rỗng. Directory: "' . $cleanDirectory . '", Filename: "' . $filename . '"');
                            }
                            
                            // Đảm bảo cả hai đều là string và không rỗng
                            $cleanDirectory = (string) trim($cleanDirectory);
                            $filename = (string) trim($filename);
                            
                            if (empty($cleanDirectory) || empty($filename)) {
                                throw new \Exception('Directory hoặc filename rỗng sau khi convert. Directory: "' . $cleanDirectory . '", Filename: "' . $filename . '"');
                            }
                            
                            // Tạo full path
                            $fullPath = $cleanDirectory . '/' . $filename;
                            
                            // Log trước khi upload
                            \Log::info('About to upload file using Storage::put', [
                                'directory' => $cleanDirectory,
                                'filename' => $filename,
                                'full_path' => $fullPath,
                                'file_pathname' => $file->getPathname(),
                                'file_real_path' => $file->getRealPath(),
                                'file_size' => $file->getSize(),
                                'file_mime' => $file->getMimeType(),
                                'file_is_valid' => $file->isValid()
                            ]);
                            
                            // Đảm bảo directory tồn tại
                            if (!Storage::disk('public')->exists($cleanDirectory)) {
                                Storage::disk('public')->makeDirectory($cleanDirectory, 0755, true);
                            }
                            
                            // Đọc nội dung file - thử nhiều cách
                            $fileContents = null;
                            
                            // Cách 1: Thử getRealPath() trước
                            $fileRealPath = $file->getRealPath();
                            if (!empty($fileRealPath) && file_exists($fileRealPath)) {
                                $fileContents = file_get_contents($fileRealPath);
                            }
                            
                            // Cách 2: Nếu không được, thử getPathname()
                            if ($fileContents === false || $fileContents === null) {
                                $filePathname = $file->getPathname();
                                if (!empty($filePathname) && file_exists($filePathname)) {
                                    $fileContents = file_get_contents($filePathname);
                                }
                            }
                            
                            // Cách 3: Nếu vẫn không được, đọc từ stream
                            if ($fileContents === false || $fileContents === null) {
                                $fileStream = fopen($file->getRealPath() ?: $file->getPathname(), 'rb');
                                if ($fileStream) {
                                    $fileContents = stream_get_contents($fileStream);
                                    fclose($fileStream);
                                }
                            }
                            
                            // Cách 4: Cuối cùng, thử dùng getContent() nếu có
                            if (($fileContents === false || $fileContents === null) && method_exists($file, 'getContent')) {
                                $fileContents = $file->getContent();
                            }
                            
                            // Kiểm tra nếu vẫn không đọc được
                            if ($fileContents === false || $fileContents === null) {
                                throw new \Exception('Không thể đọc nội dung file. Pathname: "' . $file->getPathname() . '", RealPath: "' . ($file->getRealPath() ?: 'N/A') . '"');
                            }
                            
                            // Lưu file
                            $saved = Storage::disk('public')->put($fullPath, $fileContents);
                            
                            if (!$saved) {
                                throw new \Exception('Không thể lưu file vào: ' . $fullPath);
                            }
                            
                            // Path trả về là full path
                            $path = $fullPath;
                        } catch (\Exception $storageException) {
                            \Log::error('Storage::put failed', [
                                'error' => $storageException->getMessage(),
                                'trace' => $storageException->getTraceAsString(),
                                'directory' => $cleanDirectory ?? $directory,
                                'filename' => $filename,
                                'file_real_path' => $file->getRealPath() ?? 'N/A'
                            ]);
                            throw $storageException;
                        }
                        
                        if (empty($path)) {
                            \Log::error('File upload failed - empty path returned', [
                                'directory' => $directory,
                                'filename' => $filename,
                                'ma_ho_so' => $hoSo->ma_ho_so,
                                'ho_so_id' => $hoSo->id
                            ]);
                            throw new \Exception('Không thể lưu file. Path rỗng.');
                        }
                    } catch (\Exception $e) {
                        \Log::error('File upload exception', [
                            'error' => $e->getMessage(),
                            'directory' => $directory,
                            'filename' => $filename,
                            'ma_ho_so' => $hoSo->ma_ho_so,
                            'ho_so_id' => $hoSo->id,
                            'file_size' => $file->getSize(),
                            'file_mime' => $file->getMimeType()
                        ]);
                        throw new \Exception('Lỗi khi upload file: ' . $e->getMessage());
                    }
                    
                    $fieldValue = $path;
                }

                // Chỉ tạo HoSoField nếu có giá trị hoặc là file đã upload
                if (!empty($fieldValue) || ($field->loai_truong === 'file' && $request->hasFile($fieldName))) {
                    HoSoField::create([
                        'ho_so_id' => $hoSo->id,
                        'ten_truong' => $fieldName,
                        'gia_tri' => $fieldValue,
                    ]);
                }
            }

            // Tạo thông báo
            ThongBao::create([
                'ho_so_id' => $hoSo->id,
                'nguoi_dung_id' => $user->id,
                'dich_vu_id' => $request->dich_vu_id,
                'ngay_hen' => $request->ngay_hen,
                'message' => 'Đặt lịch thành công! Mã hồ sơ: ' . $hoSo->ma_ho_so . '. Số thứ tự: ' . $soThuTu . '. Vui lòng đến đúng giờ hẹn.',
            ]);

            DB::commit();

            return redirect()->route('booking.success', $hoSo->id)
                ->with('success', 'Đặt lịch thành công! Mã hồ sơ: ' . $hoSo->ma_ho_so . '. Số thứ tự của bạn: ' . $soThuTu);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            // Redirect về upload-form với các tham số cần thiết
            return redirect()->route('booking.upload-form', [
                'don_vi_id' => $request->don_vi_id,
                'dich_vu_id' => $request->dich_vu_id,
                'ngay_hen' => $request->ngay_hen,
                'gio_hen' => $request->gio_hen
            ])->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Booking store error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            // Redirect về upload-form với các tham số cần thiết
            return redirect()->route('booking.upload-form', [
                'don_vi_id' => $request->don_vi_id,
                'dich_vu_id' => $request->dich_vu_id,
                'ngay_hen' => $request->ngay_hen,
                'gio_hen' => $request->gio_hen
            ])->withErrors(['error' => 'Có lỗi xảy ra khi đặt lịch. Vui lòng thử lại.'])->withInput();
        }
    }

    /**
     * Trang thành công
     */
    public function success($hoSoId)
    {
        $hoSo = HoSo::with(['dichVu', 'donVi', 'hoSoFields'])->findOrFail($hoSoId);
        
        // Kiểm tra quyền xem
        if ($hoSo->nguoi_dung_id != Auth::guard('web')->id()) {
            abort(403);
        }

        return view('website.booking.success', compact('hoSo'));
    }

    /**
     * Lấy danh sách ngày khả dụng
     * @param int $days Tổng số ngày để kiểm tra (mặc định 30 ngày)
     * @param int $startDays Số ngày bắt đầu từ hôm nay (mặc định 14 = sau 2 tuần)
     */
    private function getAvailableDates($schedules, $servicePhuong, $days = 30, $startDays = 14)
    {
        $availableDates = [];
        $today = Carbon::today();

        // Kiểm tra nếu không có lịch nào
        if ($schedules->isEmpty()) {
            \Log::warning('Không có lịch dịch vụ', [
                'dich_vu_id' => $servicePhuong->dich_vu_id,
                'don_vi_id' => $servicePhuong->don_vi_id
            ]);
            return $availableDates;
        }

        // Bắt đầu từ sau $startDays ngày (mặc định 14 ngày = 2 tuần)
        $startDate = $today->copy()->addDays($startDays);

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            
            // Chuyển đổi từ Carbon dayOfWeek (0-6) sang format database (1-7)
            // Carbon: 0=Chủ nhật, 1=Thứ 2, ..., 6=Thứ 7
            // Database: 1=Thứ 2, 2=Thứ 3, ..., 6=Thứ 7, 7=Chủ nhật
            $carbonDayOfWeek = $date->dayOfWeek; // 0-6
            $thuTrongTuan = $carbonDayOfWeek == 0 ? 7 : $carbonDayOfWeek; // Chuyển Chủ nhật từ 0 -> 7

            // Kiểm tra xem ngày này có trong lịch không
            foreach ($schedules as $schedule) {
                if ($schedule->thu_trong_tuan == $thuTrongTuan) {
                    // Kiểm tra số lượng đã đặt
                    $bookedCount = HoSo::where('dich_vu_id', $servicePhuong->dich_vu_id)
                        ->where('don_vi_id', $servicePhuong->don_vi_id)
                        ->where('ngay_hen', $date->format('Y-m-d'))
                        ->where('trang_thai', '!=', HoSo::STATUS_CANCELLED)
                        ->count();

                    $availableSlots = $servicePhuong->so_luong_toi_da - $bookedCount;
                    
                    // Chỉ thêm ngày nếu còn chỗ trống
                    if ($availableSlots > 0) {
                        $availableDates[] = [
                            'date' => $date->format('Y-m-d'),
                            'display' => $date->format('d/m/Y'),
                            'day_name' => $this->getDayName($carbonDayOfWeek),
                            'schedule' => $schedule,
                            'available_slots' => $availableSlots,
                        ];
                    }
                    break; // Đã tìm thấy schedule cho thứ này, không cần kiểm tra schedule khác
                }
            }
        }

        // Log để debug
        if (empty($availableDates)) {
            \Log::warning('Không có ngày khả dụng', [
                'dich_vu_id' => $servicePhuong->dich_vu_id,
                'don_vi_id' => $servicePhuong->don_vi_id,
                'schedules_count' => $schedules->count(),
                'schedules_thu' => $schedules->pluck('thu_trong_tuan')->unique()->toArray(),
                'so_luong_toi_da' => $servicePhuong->so_luong_toi_da,
                'start_date' => $startDate->format('Y-m-d'),
            ]);
        }

        return $availableDates;
    }

    /**
     * Lấy tên thứ (theo Carbon dayOfWeek: 0=Chủ nhật, 1=Thứ 2, ..., 6=Thứ 7)
     */
    private function getDayName($carbonDayOfWeek)
    {
        $days = [
            0 => 'Chủ nhật',
            1 => 'Thứ 2',
            2 => 'Thứ 3',
            3 => 'Thứ 4',
            4 => 'Thứ 5',
            5 => 'Thứ 6',
            6 => 'Thứ 7',
        ];
        return $days[$carbonDayOfWeek] ?? '';
    }
}
