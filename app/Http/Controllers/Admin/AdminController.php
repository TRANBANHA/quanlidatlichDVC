<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\DonVi;
use App\Mail\AdminAccountMail;
use Illuminate\Http\Request;
use App\Services\AdminService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportStaffRequest;
use App\Exports\StaffTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        $query = Admin::with('donVi');

        // Phân quyền xem danh sách
        if ($currentUser->isAdminPhuong()) {
            // Admin phường: Chỉ xem cán bộ của phường mình
            $query->where('don_vi_id', $currentUser->don_vi_id);
        } elseif ($currentUser->isCanBo()) {
            // Cán bộ: Không có quyền xem danh sách admin
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }
        // Admin tổng: Xem tất cả

        // Tìm kiếm theo tên người dùng
        if ($request->filled('name')) {
            $query->where(function($q) use ($request) {
                $q->where('ho_ten', 'like', '%' . $request->name . '%')
                  ->orWhere('ten_dang_nhap', 'like', '%' . $request->name . '%');
            });
        }

        // Lọc theo đơn vị
        if ($request->filled('don_vi_id')) {
            $query->where('don_vi_id', $request->don_vi_id);
        }

        // Lọc theo quyền (0 = cán bộ, 1 = admin tổng, 2 = admin phường)
        if ($request->filled('quyen')) {
            $query->where('quyen', $request->quyen);
        }

        $query->orderBy('id', 'desc');
        $listAdmin = $query->paginate(10)->withQueryString();
        
        // Lấy danh sách đơn vị để filter
        if ($currentUser->isAdmin()) {
            $donVis = DonVi::all();
        } elseif ($currentUser->isAdminPhuong()) {
            $donVis = DonVi::where('id', $currentUser->don_vi_id)->get();
        } else {
            $donVis = collect();
        }
        
        // Lấy don_vi_id từ request nếu có (khi click "Xem cán bộ" từ trang phường)
        $selectedDonViId = $request->get('don_vi_id');
        
        return view("backend.admin.index", compact("listAdmin", "donVis", "selectedDonViId"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Phân quyền
        if ($currentUser->isCanBo()) {
            abort(403, 'Bạn không có quyền tạo tài khoản.');
        }
        
        if ($currentUser->isAdminPhuong()) {
            // Admin phường: Chỉ tạo cán bộ cho phường mình
            $donVis = DonVi::where('id', $currentUser->don_vi_id)->get();
        } else {
            // Admin tổng: Tạo được tất cả
            $donVis = DonVi::all();
        }
        
        return view("backend.admin.create", compact('donVis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Phân quyền
        if ($currentUser->isCanBo()) {
            abort(403, 'Bạn không có quyền tạo tài khoản.');
        }
        
        // Admin phường chỉ được tạo cán bộ (quyen = 0) cho phường mình
        if ($currentUser->isAdminPhuong()) {
            if ($request->quyen != Admin::CAN_BO) {
                return redirect()->back()
                    ->withErrors(['quyen' => 'Admin phường chỉ được tạo cán bộ phường.'])
                    ->withInput();
            }
            if ($request->don_vi_id != $currentUser->don_vi_id) {
                return redirect()->back()
                    ->withErrors(['don_vi_id' => 'Bạn chỉ được tạo cán bộ cho phường của mình.'])
                    ->withInput();
            }
        }
        
        // Validation rules - mật khẩu sẽ được tự động tạo, không cần nhập
        $request->validate([
            'ho_ten' => 'required|string|max:255',
            'ten_dang_nhap' => 'required|string|max:255|unique:quan_tri_vien,ten_dang_nhap',
            'email' => 'nullable|email|unique:quan_tri_vien,email',
            'so_dien_thoai' => 'nullable|string|max:20',
            'quyen' => 'required|in:0,1,2',
            'don_vi_id' => 'nullable|exists:don_vi,id',
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'ten_dang_nhap.required' => 'Vui lòng nhập tên đăng nhập.',
            'ten_dang_nhap.unique' => 'Tên đăng nhập đã tồn tại.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại.',
            'quyen.required' => 'Vui lòng chọn quyền.',
            'don_vi_id.exists' => 'Đơn vị/phường không hợp lệ.',
        ]);

        // Kiểm tra: Admin phường và Cán bộ phường phải có don_vi_id
        if (in_array($request->quyen, [Admin::ADMIN_PHUONG, Admin::CAN_BO]) && empty($request->don_vi_id)) {
            return redirect()->back()
                ->withErrors(['don_vi_id' => 'Admin phường và Cán bộ phường phải chọn đơn vị/phường.'])
                ->withInput();
        }

        // Tự động tạo mật khẩu random cho tất cả tài khoản
        // Tạo mật khẩu random 8 ký tự (chữ hoa, chữ thường và số)
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        $randomPassword = '';
        for ($i = 0; $i < 8; $i++) {
            $randomPassword .= $characters[rand(0, strlen($characters) - 1)];
        }

        $admin = Admin::create([
            'ho_ten' => $request->ho_ten,
            'ten_dang_nhap' => $request->ten_dang_nhap,
            'mat_khau' => bcrypt($randomPassword),
            'email' => $request->email,
            'so_dien_thoai' => $request->so_dien_thoai,
            'quyen' => $request->quyen,
            'don_vi_id' => $request->don_vi_id,
        ]);

        // Lấy tên đơn vị/phường nếu có
        $donViName = null;
        if ($request->don_vi_id) {
            $donVi = DonVi::find($request->don_vi_id);
            $donViName = $donVi ? $donVi->ten_don_vi : null;
        }

        // Gửi email thông tin tài khoản nếu có email
        if (!empty($request->email)) {
            try {
                Mail::to($request->email)->send(new AdminAccountMail(
                    $request->ho_ten,
                    $request->ten_dang_nhap,
                    $randomPassword,
                    $request->email,
                    $donViName
                ));
            } catch (\Exception $e) {
                Log::error('Lỗi gửi email thông tin tài khoản: ' . $e->getMessage());
                // Vẫn tiếp tục tạo tài khoản dù gửi email lỗi
            }
        }

        // Thông báo kết quả
        $message = 'Tạo tài khoản thành công!';
        if (!empty($request->email)) {
            $message .= ' Thông tin đăng nhập đã được gửi đến email: <strong>' . $request->email . '</strong>';
        } else {
            $message .= ' Mật khẩu: <strong>' . $randomPassword . '</strong> - Vui lòng lưu lại và cung cấp cho người dùng.';
        }
        return redirect()->route('quantri.index')->with('success', $message);
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
    public function edit($id)
    {
        $currentUser = Auth::guard('admin')->user();
        $admin = Admin::findOrFail($id);
        
        // Phân quyền
        if ($currentUser->isCanBo()) {
            abort(403, 'Bạn không có quyền chỉnh sửa tài khoản.');
        }
        
        if ($currentUser->isAdminPhuong()) {
            // Admin phường: Chỉ sửa được cán bộ của phường mình
            if ($admin->don_vi_id != $currentUser->don_vi_id || $admin->quyen != Admin::CAN_BO) {
                abort(403, 'Bạn chỉ được sửa cán bộ của phường mình.');
            }
            $donVis = DonVi::where('id', $currentUser->don_vi_id)->get();
        } else {
            // Admin tổng: Sửa được tất cả
            $donVis = DonVi::all();
        }
        
        return view('backend.admin.edit', compact('admin', 'donVis'));
    }
    public function updatePublishStatus(Request $request)
    {
        try {
            // Lấy dữ liệu từ request
            $itemId = $request->input('id');
            $publish = $request->input('publish');
            $service = $request->input('service');

            // Tìm item theo ID và cập nhật trạng thái
            $item = Admin::find($itemId);
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy item!',
                ], 404);
            }

            $item->publish = $publish;
            $item->save();

            // Trả về phản hồi JSON
            return response()->json([
                'success' => true,
                'id' => $item->id,
                'publish' => $item->publish,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            // Bắt lỗi và trả về phản hồi lỗi
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $currentUser = Auth::guard('admin')->user();
        $admin = Admin::findOrFail($id);
        
        // Phân quyền
        if ($currentUser->isCanBo()) {
            abort(403, 'Bạn không có quyền cập nhật tài khoản.');
        }
        
        // Admin phường chỉ được sửa cán bộ của phường mình
        if ($currentUser->isAdminPhuong()) {
            if ($admin->don_vi_id != $currentUser->don_vi_id || $admin->quyen != Admin::CAN_BO) {
                abort(403, 'Bạn chỉ được sửa cán bộ của phường mình.');
            }
            // Không cho phép đổi quyền hoặc đơn vị
            if ($request->quyen != Admin::CAN_BO || $request->don_vi_id != $currentUser->don_vi_id) {
                return redirect()->back()
                    ->withErrors(['quyen' => 'Bạn không được thay đổi quyền hoặc đơn vị.'])
                    ->withInput();
            }
        }
        
        $request->validate([
            'ho_ten' => 'required|string|max:255',
            'ten_dang_nhap' => 'required|string|max:255|unique:quan_tri_vien,ten_dang_nhap,' . $id,
            'mat_khau' => 'nullable|string|min:6',
            'email' => 'nullable|email|unique:quan_tri_vien,email,' . $id,
            'so_dien_thoai' => 'nullable|string|max:20',
            'quyen' => 'required|in:0,1,2',
            'don_vi_id' => 'nullable|exists:don_vi,id',
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'ten_dang_nhap.required' => 'Vui lòng nhập tên đăng nhập.',
            'ten_dang_nhap.unique' => 'Tên đăng nhập đã tồn tại.',
            'mat_khau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại.',
            'quyen.required' => 'Vui lòng chọn quyền.',
            'don_vi_id.exists' => 'Đơn vị/phường không hợp lệ.',
        ]);

        // Kiểm tra: Admin phường và Cán bộ phường phải có don_vi_id
        if (in_array($request->quyen, [Admin::ADMIN_PHUONG, Admin::CAN_BO]) && empty($request->don_vi_id)) {
            return redirect()->back()
                ->withErrors(['don_vi_id' => 'Admin phường và Cán bộ phường phải chọn đơn vị/phường.'])
                ->withInput();
        }

        $admin = Admin::findOrFail($id);
        $admin->ho_ten = $request->ho_ten;
        $admin->ten_dang_nhap = $request->ten_dang_nhap;
        $admin->email = $request->email;
        $admin->so_dien_thoai = $request->so_dien_thoai;
        $admin->quyen = $request->quyen;
        $admin->don_vi_id = $request->don_vi_id;

        if ($request->filled('mat_khau')) {
            $admin->mat_khau = bcrypt($request->mat_khau);
        }

        $admin->save();

        return redirect()->route('quantri.index')->with('success', 'Cập nhật tài khoản thành công!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $currentUser = Auth::guard('admin')->user();
            $admin = Admin::findOrFail($id);
            
            // Phân quyền
            if ($currentUser->isCanBo()) {
                abort(403, 'Bạn không có quyền xóa tài khoản.');
            }
            
            // Admin phường chỉ được xóa cán bộ của phường mình
            if ($currentUser->isAdminPhuong()) {
                if ($admin->don_vi_id != $currentUser->don_vi_id || $admin->quyen != Admin::CAN_BO) {
                    abort(403, 'Bạn chỉ được xóa cán bộ của phường mình.');
                }
            }
            
            // Không cho phép xóa chính mình
            if ($admin->id == $currentUser->id) {
                return redirect()->back()->with('error', 'Bạn không thể xóa chính tài khoản của mình.');
            }

            // Xóa admin khỏi database
            $admin->delete();

            // Trả về JSON response nếu yêu cầu là AJAX
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Admin deleted successfully!']);
            }

            // Chuyển hướng về danh sách admin nếu yêu cầu không phải AJAX
            return redirect()->route('quantri.index')->with('success', 'Admin deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Import cán bộ từ file Excel
     */
    public function import(ImportStaffRequest $request)
    {
        try {
            $currentUser = Auth::guard('admin')->user();
            
            // Phân quyền
            if ($currentUser->isCanBo()) {
                abort(403, 'Bạn không có quyền import cán bộ.');
            }
            
            $file = $request->file('file');
            $donViId = $request->don_vi_id;
            
            // Admin phường chỉ được import cho phường mình
            if ($currentUser->isAdminPhuong() && $donViId != $currentUser->don_vi_id) {
                return redirect()->back()
                    ->withErrors(['don_vi_id' => 'Bạn chỉ được import cán bộ cho phường của mình.'])
                    ->withInput();
            }

            $data = Excel::toArray(new \stdClass(), $file);
            
            if (empty($data) || empty($data[0])) {
                return redirect()->back()->with('error', 'File không có dữ liệu.');
            }

            $rows = $data[0];
            $header = array_shift($rows); // Bỏ dòng header

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            // Log để debug
            Log::info('Import started. Total rows: ' . count($rows));

            foreach ($rows as $index => $row) {
                try {
                    // Giả sử format: Họ tên | Tên đăng nhập | Mật khẩu | Email | Số điện thoại
                    if (count($row) < 3) {
                        $errors[] = "Dòng " . ($index + 2) . ": Không đủ cột dữ liệu (cần ít nhất 3 cột)";
                        $errorCount++;
                        continue;
                    }

                    // Trim và xử lý dữ liệu
                    $hoTen = trim($row[0] ?? '');
                    $tenDangNhap = trim($row[1] ?? '');
                    $matKhau = trim($row[2] ?? '');
                    $email = !empty($row[3]) ? trim($row[3]) : null;
                    $soDienThoai = !empty($row[4]) ? trim($row[4]) : null;

                    if (empty($hoTen) || empty($tenDangNhap) || empty($matKhau)) {
                        $errors[] = "Dòng " . ($index + 2) . ": Thiếu thông tin bắt buộc";
                        $errorCount++;
                        continue;
                    }

                    // Kiểm tra tên đăng nhập đã tồn tại
                    if (Admin::where('ten_dang_nhap', $tenDangNhap)->exists()) {
                        $errors[] = "Dòng " . ($index + 2) . ": Tên đăng nhập '$tenDangNhap' đã tồn tại";
                        $errorCount++;
                        continue;
                    }

                    Admin::create([
                        'ho_ten' => $hoTen,
                        'ten_dang_nhap' => $tenDangNhap,
                        'mat_khau' => bcrypt($matKhau),
                        'email' => $email,
                        'so_dien_thoai' => $soDienThoai,
                        'quyen' => Admin::CAN_BO, // Cán bộ
                        'don_vi_id' => $donViId,
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Dòng " . ($index + 2) . ": " . $e->getMessage();
                    $errorCount++;
                }
            }

            // Tạo message chi tiết
            if ($successCount > 0 && $errorCount == 0) {
                $message = "✅ Import thành công! Đã thêm $successCount cán bộ vào hệ thống.";
            } elseif ($successCount > 0 && $errorCount > 0) {
                $message = "⚠️ Import hoàn tất: $successCount thành công, $errorCount lỗi.";
                if (!empty($errors)) {
                    $message .= "<br><strong>Chi tiết lỗi:</strong><br>" . implode("<br>", array_slice($errors, 0, 10));
                    if (count($errors) > 10) {
                        $message .= "<br>... và " . (count($errors) - 10) . " lỗi khác.";
                    }
                }
            } else {
                $message = "❌ Import thất bại! Không có dữ liệu nào được thêm vào.";
                if (!empty($errors)) {
                    $message .= "<br><strong>Chi tiết lỗi:</strong><br>" . implode("<br>", array_slice($errors, 0, 10));
                    if (count($errors) > 10) {
                        $message .= "<br>... và " . (count($errors) - 10) . " lỗi khác.";
                    }
                }
            }

            $sessionKey = ($errorCount > 0 && $successCount == 0) ? 'error' : (($errorCount > 0) ? 'warning' : 'success');
            
            // Giữ lại don_vi_id trong redirect nếu có (khi import từ trang xem cán bộ phường)
            $redirect = redirect()->back();
            if ($request->has('redirect_don_vi_id')) {
                $redirect = redirect()->route('quantri.index', ['don_vi_id' => $request->redirect_don_vi_id]);
            }
            
            return $redirect->with($sessionKey, $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi import: ' . $e->getMessage());
        }
    }

    /**
     * Tải file Excel mẫu để import cán bộ
     */
    public function downloadTemplate(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        // Phân quyền: Chỉ admin tổng và admin phường mới được tải
        if ($currentUser->isCanBo()) {
            abort(403, 'Bạn không có quyền tải file mẫu.');
        }
        
        $format = $request->get('format', 'xlsx'); // Mặc định là xlsx
        
        try {
            if ($format === 'csv') {
                return Excel::download(new StaffTemplateExport(), 'mau-import-can-bo-phuong.csv', \Maatwebsite\Excel\Excel::CSV, [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                ]);
            } else {
                return Excel::download(new StaffTemplateExport(), 'mau-import-can-bo-phuong.xlsx', \Maatwebsite\Excel\Excel::XLSX, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi download template: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            abort(500, 'Lỗi khi tải file: ' . $e->getMessage());
        }
    }
}
