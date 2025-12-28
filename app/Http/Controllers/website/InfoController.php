<?php

namespace App\Http\Controllers\website;

use App\Models\HoSo;
use App\Models\User;
use App\Models\DonVi;
use App\Models\ThongBao;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class InfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route("login")->with('success', "Vui lòng đăng nhập tài khoản");
        }
        $user = auth()->user();
        $donVis = DonVi::all();
        
        // Load hồ sơ với rating được filter theo người dùng hiện tại
        $ho_so = HoSo::with([
            'nguoiDung', 
            'donVi', 
            'dichVu', 
            'thongBao', 
            'hoSoFields',
            'rating' => function($query) use ($user) {
                $query->where('nguoi_dung_id', $user->id);
            }
        ])
        ->where('nguoi_dung_id', Auth::user()->id)
        ->paginate(10);
        
        // Lấy thông báo cho tab4
        $thongBaos = ThongBao::where('nguoi_dung_id', $user->id)
            ->with(['hoSo', 'dichVu'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Đếm thông báo chưa đọc
        $unreadCount = ThongBao::where('nguoi_dung_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        return view('website.info.info', compact('user', 'donVis', 'ho_so', 'thongBaos', 'unreadCount'));
    }

    public function lookup(Request $request)
    {
        $type = $request->get('type', 'ma_ho_so');
        $keyword = trim($request->get('keyword', ''));
        $results = collect();

        if ($keyword !== '') {
            $request->validate([
                'type' => 'required|in:ma_ho_so,cccd',
                'keyword' => $type === 'ma_ho_so' ? 'required|string|min:5' : 'required|digits:12',
            ], [
                'keyword.required' => 'Vui lòng nhập thông tin tra cứu.',
                'keyword.digits' => 'Số CCCD phải gồm 12 chữ số.',
            ]);

            $query = HoSo::with(['dichVu', 'donVi', 'nguoiDung'])
                ->latest();

            if ($type === 'ma_ho_so') {
                $query->where('ma_ho_so', $keyword);
            } else {
                $query->whereHas('nguoiDung', function ($subQuery) use ($keyword) {
                    $subQuery->where('cccd', $keyword);
                });
            }

            $results = $query->take(20)->get();
        }

        return view('website.info.lookup', [
            'type' => $type,
            'keyword' => $keyword,
            'results' => $results,
        ]);
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|confirmed',
        ]);

        // Cập nhật mật khẩu cho người dùng
        $user = auth()->user();
        $user->update([
            'mat_khau' => bcrypt($request->new_password),
        ]);

        return back()->with('success', 'Mật khẩu đã được thay đổi!');
    }

    /**
     * Hiển thị form hủy hồ sơ
     */
    public function showCancelForm($hoSo)
    {
        // Lấy ID nếu là model
        $hoSoId = is_numeric($hoSo) ? $hoSo : (is_object($hoSo) ? $hoSo->id : $hoSo);
        
        // Kiểm tra quyền
        if (!Auth::check()) {
            return redirect()->route('login.form')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        
        // Lấy hồ sơ
        $hoSo = HoSo::with(['dichVu', 'donVi'])->findOrFail($hoSoId);
        
        // Kiểm tra quyền sở hữu
        if ($hoSo->nguoi_dung_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền hủy hồ sơ này.');
        }
        
        // Kiểm tra có thể hủy không
        if (!$hoSo->canBeCancelled()) {
            return redirect('/info?action=tab2')->with('error', 'Hồ sơ này không thể hủy trong trạng thái hiện tại.');
        }
        
        return view('website.info.cancel', compact('hoSo'));
    }

    /**
     * Xử lý hủy hồ sơ
     */
    public function cancel(Request $request, $hoSo)
    {
        try {
            // Lấy ID nếu là model
            $hoSoId = is_numeric($hoSo) ? $hoSo : (is_object($hoSo) ? $hoSo->id : $hoSo);
            
            // Kiểm tra quyền
            if (!Auth::check()) {
                return redirect()->route('login.form')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
            }
            
            // Lấy hồ sơ
            $hoSo = HoSo::findOrFail($hoSoId);
            
            // Kiểm tra quyền sở hữu
            if ($hoSo->nguoi_dung_id !== Auth::id()) {
                abort(403, 'Bạn không có quyền hủy hồ sơ này.');
            }
            
            // Kiểm tra có thể hủy không
            if (!$hoSo->canBeCancelled()) {
                return redirect('/info?action=tab2')->with('error', 'Hồ sơ này không thể hủy trong trạng thái hiện tại.');
            }
            
            // Lấy lý do hủy - KHÔNG validate, chỉ lấy giá trị
            $lyDoHuy = null;
            $lyDoHuyType = $request->input('ly_do_huy_type', '');
            
            // Nếu không có lý do, để null (cho phép hủy không cần lý do)
            if (empty($lyDoHuyType)) {
                $lyDoHuy = null;
            } elseif ($lyDoHuyType === 'khac') {
                // Nếu chọn "Khác", lấy nội dung từ textarea
                $lyDoHuy = $request->input('ly_do_huy_khac', '');
                $lyDoHuy = trim($lyDoHuy);
                if (empty($lyDoHuy)) {
                    $lyDoHuy = null;
                }
            } else {
                // Lấy lý do từ select
                $lyDoHuy = $lyDoHuyType;
            }
            
            // Giới hạn độ dài
            if ($lyDoHuy && strlen($lyDoHuy) > 255) {
                $lyDoHuy = substr($lyDoHuy, 0, 255);
            }
            
            // Cập nhật trực tiếp vào database (bỏ qua model validation)
            DB::table('ho_so')
                ->where('id', $hoSo->id)
                ->update([
                    'trang_thai' => HoSo::STATUS_CANCELLED,
                    'cancelled_at' => now(),
                    'ly_do_huy' => $lyDoHuy,
                    'updated_at' => now(),
                ]);
            
            // Tạo thông báo (bỏ qua nếu có lỗi)
            try {
                $message = "Bạn đã hủy lịch hẹn. Mã hồ sơ: {$hoSo->ma_ho_so}";
                if ($lyDoHuy) {
                    $message .= ". Lý do: {$lyDoHuy}";
                }
                
                ThongBao::create([
                    'ho_so_id' => $hoSo->id,
                    'nguoi_dung_id' => $hoSo->nguoi_dung_id,
                    'dich_vu_id' => $hoSo->dich_vu_id,
                    'ngay_hen' => $hoSo->ngay_hen,
                    'message' => $message,
                    'is_read' => false, // Mặc định chưa đọc
                ]);
            } catch (\Exception $e) {
                // Bỏ qua lỗi tạo thông báo, vẫn redirect thành công
                \Log::warning('Failed to create ThongBao', ['error' => $e->getMessage()]);
            }
            
            // Luôn redirect về tab2, không dùng back()
            return redirect('/info?action=tab2')->with('success', 'Đã hủy lịch hẹn thành công.');
            
        } catch (\Exception $e) {
            \Log::error('Cancel ho so error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Luôn redirect về tab2, không dùng back()
            return redirect('/info?action=tab2')->with('error', 'Có lỗi xảy ra khi hủy lịch hẹn: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form chỉnh sửa hồ sơ
     */
    public function editHoSo($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để chỉnh sửa hồ sơ.');
        }

        $hoSo = HoSo::with(['dichVu.serviceFields', 'donVi', 'hoSoFields'])->findOrFail($id);

        // Kiểm tra quyền
        if ($hoSo->nguoi_dung_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa hồ sơ này.');
        }

        // Kiểm tra có thể chỉnh sửa không
        if (!$hoSo->canBeEdited()) {
            return redirect()->route('info.index', ['action' => 'tab2'])
                ->with('error', 'Hồ sơ này không thể chỉnh sửa trong trạng thái hiện tại.');
        }

        return view('website.info.edit', compact('hoSo'));
    }

    /**
     * Cập nhật hồ sơ
     */
    public function updateHoSo(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        $hoSo = HoSo::with(['dichVu.serviceFields'])->findOrFail($id);

        // Kiểm tra quyền
        if ($hoSo->nguoi_dung_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa hồ sơ này.');
        }

        // Kiểm tra có thể chỉnh sửa không
        if (!$hoSo->canBeEdited()) {
            return redirect()->route('info.index', ['action' => 'tab2'])
                ->with('error', 'Hồ sơ này không thể chỉnh sửa trong trạng thái hiện tại.');
        }

        // Validation cho các trường động
        $dynamicRules = [];
        foreach ($hoSo->dichVu->serviceFields as $field) {
            if ($field->loai_truong === 'file') {
                // File không bắt buộc khi edit (vì có thể đã có file cũ)
                // Tăng giới hạn lên 20MB (20480 KB) để cho phép upload file lớn hơn
                $dynamicRules[$field->ten_truong] = 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480';
            } elseif ($field->bat_buoc) {
                $dynamicRules[$field->ten_truong] = 'required';
            } else {
                // Các trường không bắt buộc và không phải file
                $dynamicRules[$field->ten_truong] = 'nullable';
            }
        }

        $request->validate(array_merge([
            'ghi_chu' => 'nullable|string|max:1000',
        ], $dynamicRules));

        DB::beginTransaction();
        try {
            // Cập nhật ghi chú
            if ($request->filled('ghi_chu')) {
                $hoSo->ghi_chu = $request->ghi_chu;
            }

            // Xử lý các trường động
            foreach ($hoSo->dichVu->serviceFields as $field) {
                $fieldName = $field->ten_truong;
                
                if ($field->loai_truong === 'file') {
                    // Xử lý file upload
                    if ($request->hasFile($fieldName)) {
                        $file = $request->file($fieldName);
                        $filename = time() . '_' . $file->getClientOriginalName();
                        // Lưu vào storage/app/public/ho-so/ với disk 'public'
                        $path = $file->storeAs('ho-so', $filename, 'public');
                        
                        \Log::info('File uploaded', [
                            'field_name' => $fieldName,
                            'filename' => $filename,
                            'path' => $path,
                            'file_exists' => Storage::disk('public')->exists($path),
                            'full_path' => storage_path('app/public/' . $path)
                        ]);
                        
                        // Cập nhật hoặc tạo hoSoField
                        $hoSoField = $hoSo->hoSoFields()->where('ten_truong', $fieldName)->first();
                        if ($hoSoField) {
                            // Xóa file cũ nếu có
                            if ($hoSoField->gia_tri && Storage::disk('public')->exists($hoSoField->gia_tri)) {
                                Storage::disk('public')->delete($hoSoField->gia_tri);
                            }
                            $hoSoField->gia_tri = $path; // $path đã là 'ho-so/filename'
                            $hoSoField->save();
                            
                            \Log::info('HoSoField updated', [
                                'ho_so_id' => $hoSo->id,
                                'ten_truong' => $fieldName,
                                'gia_tri' => $hoSoField->gia_tri
                            ]);
                        } else {
                            $newField = $hoSo->hoSoFields()->create([
                                'ten_truong' => $fieldName,
                                'gia_tri' => $path, // $path đã là 'ho-so/filename'
                            ]);
                            
                            \Log::info('HoSoField created', [
                                'ho_so_id' => $hoSo->id,
                                'ten_truong' => $fieldName,
                                'gia_tri' => $newField->gia_tri
                            ]);
                        }
                    }
                } else {
                    // Xử lý các trường text, email, number, date, textarea, select
                    $value = $request->input($fieldName);
                    
                    $hoSoField = $hoSo->hoSoFields()->where('ten_truong', $fieldName)->first();
                    if ($hoSoField) {
                        $hoSoField->gia_tri = $value;
                        $hoSoField->save();
                    } else {
                        $hoSo->hoSoFields()->create([
                            'ten_truong' => $fieldName,
                            'gia_tri' => $value,
                        ]);
                    }
                }
            }

            $hoSo->save();
            DB::commit();

            return redirect()->route('info.index', ['action' => 'tab2'])
                ->with('success', 'Cập nhật hồ sơ thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật hồ sơ: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateStatusPayment(Request $request, $slug, $id, $action)
    {
        $request->validate([
            'proof_image' => 'required|image|mimes:jpg,jpeg,png|max:2048', // Validate file ảnh
        ], [
            'proof_image.required' => 'Ảnh chứng từ thanh toán là bắt buộc.',
            'proof_image.image' => 'File phải là một hình ảnh.',
            'proof_image.mimes' => 'Ảnh phải có định dạng: jpg, jpeg, png.',
            'proof_image.max' => 'Dung lượng ảnh không được vượt quá 2MB.',
        ]);

        // Lấy bản ghi payment dựa trên slug và ID
        $payment = Payment::where('form_type', $slug)->where('record_id', $id)->first();
        if (!$payment) {
            return redirect()->back()->withErrors(['error' => 'Không tìm thấy bản ghi thanh toán.']);
        }

        // Xử lý lưu ảnh
        if ($request->hasFile('proof_image')) {
            $imagePath = $request->file('proof_image')->store('payment_proofs', 'public');
            $payment->image = $imagePath; // Lưu đường dẫn ảnh vào cột `image`
        }

        // Cập nhật trạng thái thanh toán
        $payment->payment_status = 1; // Đã thanh toán
        $payment->save();

        return redirect()->back()->with('success', 'Thanh toán đã được cập nhật thành công!');
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
        //
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
    public function update(Request $request, $id)
    {
        $request->validate([
            'ten' => 'required|string|max:255',
            'so_dien_thoai' => 'required|string|min:10|max:10',
            'cccd' => 'required|string|size:12|unique:nguoi_dung,cccd,' . $id,
            'don_vi_id' => 'required|integer|exists:don_vi,id',
            'dia_chi' => 'required|string|max:500',
        ]);

        $user = User::findOrFail($id);

        $user->ten = $request->ten;
        $user->so_dien_thoai = $request->so_dien_thoai;
        $user->cccd = $request->cccd;
        $user->don_vi_id = $request->don_vi_id;
        $user->dia_chi = $request->dia_chi;
        $user->save();

        return redirect()->back()->with('success', 'Cập nhật thông tin tài khoản thành công!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
