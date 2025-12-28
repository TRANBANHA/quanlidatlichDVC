<?php

namespace App\Http\Controllers\website;

use Auth;
use App\Models\User;
use App\Models\Region;
use App\Models\Citizen;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\BirthRegistration;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BirthRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route("login")->with('success', "Vui lòng đăng nhập tài khoản");
        }
        $action = "create";
        // Lấy danh sách cha
        $fathers = Citizen::where('user_id', Auth::user()->id)
            // ->where('residence_status', 1)
            ->where('relation', 'LIKE', '%Cha%')
            ->get();

        // Lấy danh sách mẹ
        $mothers = Citizen::where('user_id', Auth::user()->id)
            // ->where('residence_status', 1)
            ->where('relation', 'LIKE', '%Mẹ%')
            ->get();
        $regions = Region::all();
        // Truyền cả cha và mẹ vào view
        return view('website.form.birhtRegis', compact('fathers', 'mothers', 'action', 'regions'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before_or_equal:today',  // Ensures the date is not greater than today
            'gender' => 'required|in:A,M,F',
            'ethnicity' => 'required|string|max:100',
            'nationality' => 'required|string|max:100',
            'birth_place' => 'required|string|max:255',
            'residence' => 'required|string|max:255',
            // 'relation' => 'required|string|max:100',
        ], [
            'full_name.required' => 'Họ và tên là bắt buộc.',
            'full_name.string' => 'Họ và tên phải là một chuỗi ký tự.',
            'full_name.max' => 'Họ và tên không được vượt quá 255 ký tự.',

            'birth_date.required' => 'Ngày sinh là bắt buộc.',
            'birth_date.date' => 'Ngày sinh phải là một ngày hợp lệ.',
            'birth_date.before_or_equal' => 'Ngày sinh không được lớn hơn ngày hiện tại.',

            'gender.required' => 'Giới tính là bắt buộc.',
            'gender.in' => 'Giới tính phải là Nam, Nữ hoặc Chưa xác định.',

            'ethnicity.required' => 'Dân tộc là bắt buộc.',
            'ethnicity.string' => 'Dân tộc phải là một chuỗi ký tự.',
            'ethnicity.max' => 'Dân tộc không được vượt quá 100 ký tự.',

            'nationality.required' => 'Quốc tịch là bắt buộc.',
            'nationality.string' => 'Quốc tịch phải là một chuỗi ký tự.',
            'nationality.max' => 'Quốc tịch không được vượt quá 100 ký tự.',

            'birth_place.required' => 'Nơi sinh là bắt buộc.',
            'birth_place.string' => 'Nơi sinh phải là một chuỗi ký tự.',
            'birth_place.max' => 'Nơi sinh không được vượt quá 255 ký tự.',

            'residence.required' => 'Nơi cư trú là bắt buộc.',
            'residence.string' => 'Nơi cư trú phải là một chuỗi ký tự.',
            'residence.max' => 'Nơi cư trú không được vượt quá 255 ký tự.',

            // 'relation.required' => 'Mối quan hệ là bắt buộc.',
            // 'relation.string' => 'Mối quan hệ phải là một chuỗi ký tự.',
            // 'relation.max' => 'Mối quan hệ không được vượt quá 100 ký tự.',
        ]);


        // Lấy thông tin cha và mẹ từ bảng `citizens`, có thể null
        $father = $request->has('father_id') ? Citizen::find($request['father_id']) : null;
        $mother = $request->has('mother_id') ? Citizen::find($request['mother_id']) : null;

        // Gán thông tin cha và mẹ vào dữ liệu cần lưu
        $request['father_name'] = $father?->full_name;
        $request['father_cccd'] = $father?->cccd;
        $request['mother_name'] = $mother?->full_name;
        $request['mother_cccd'] = $mother?->cccd;

        // Thêm user_id và trạng thái duyệt
        $request['user_id'] = Auth::user()->id;
        $request['approval_status'] = 0;
        $request['relation'] = 'Con';

        // Tạo mới bản ghi đăng ký khai sinh
        $BirthRegistration = BirthRegistration::create($request->all());

        Payment::create([
            'user_id' => Auth::id(),
            'form_type' => 'birthRegistrations', // Loại form là công dân
            'record_id' => $BirthRegistration->id, // ID của công dân vừa tạo
            'amount' => 50000, // Số tiền thanh toán (có thể thay đổi tùy logic)
            'payment_status' => 0 // Trạng thái ban đầu
        ]);

        // Redirect to a specific route with success message
        return redirect()->back()->with('success', 'Đăng ký khai sinh thành công!');
    }

    /**
     * Edit a specific resource.
     */
    public function edit($id)
    {
        // Lấy thông tin bản ghi khai sinh
        $birthRegistration = BirthRegistration::findOrFail($id);

        // Đặt biến action để dùng trong view
        $action = "edit";

        // Lấy danh sách khai sinh đã tạo bởi user

        // Lấy danh sách cha
        $fathers = Citizen::where('user_id', Auth::user()->id)
            ->where('residence_status', 1)
            ->where('relation', 'LIKE', '%Cha%')
            ->get();

        // Lấy danh sách mẹ
        $mothers = Citizen::where('user_id', Auth::user()->id)
            ->where('residence_status', 1)
            ->where('relation', 'LIKE', '%Mẹ%')
            ->get();
        $regions = Region::all();
        // Truyền cả cha và mẹ vào view
        return view('website.form.birhtRegis', compact('fathers', 'mothers', 'action', 'birthRegistration', 'regions'));
    }

    /**
     * Update a specific resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate dữ liệu nhập
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before_or_equal:today',  // Ensures the date is not greater than today
            'gender' => 'required|in:A,M,F',
            'region_id' => 'required',
            'ethnicity' => 'required|string|max:100',
            'nationality' => 'required|string|max:100',
            'birth_place' => 'required|string|max:255',
            'residence' => 'required|string|max:255',
            // 'relation' => 'required|string|max:100',
        ], [
            'full_name.required' => 'Họ và tên là bắt buộc.',
            'full_name.string' => 'Họ và tên phải là một chuỗi ký tự.',
            'full_name.max' => 'Họ và tên không được vượt quá 255 ký tự.',

            'birth_date.required' => 'Ngày sinh là bắt buộc.',
            'birth_date.date' => 'Ngày sinh phải là một ngày hợp lệ.',
            'birth_date.before_or_equal' => 'Ngày sinh không được lớn hơn ngày hiện tại.',

            'gender.required' => 'Giới tính là bắt buộc.',
            'gender.in' => 'Giới tính phải là Nam, Nữ hoặc Chưa xác định.',

            'ethnicity.required' => 'Dân tộc là bắt buộc.',
            'ethnicity.string' => 'Dân tộc phải là một chuỗi ký tự.',
            'ethnicity.max' => 'Dân tộc không được vượt quá 100 ký tự.',

            'nationality.required' => 'Quốc tịch là bắt buộc.',
            'nationality.string' => 'Quốc tịch phải là một chuỗi ký tự.',
            'nationality.max' => 'Quốc tịch không được vượt quá 100 ký tự.',

            'birth_place.required' => 'Nơi sinh là bắt buộc.',
            'birth_place.string' => 'Nơi sinh phải là một chuỗi ký tự.',
            'birth_place.max' => 'Nơi sinh không được vượt quá 255 ký tự.',

            'residence.required' => 'Nơi cư trú là bắt buộc.',
            'residence.string' => 'Nơi cư trú phải là một chuỗi ký tự.',
            'residence.max' => 'Nơi cư trú không được vượt quá 255 ký tự.',

            // 'relation.required' => 'Mối quan hệ là bắt buộc.',
            // 'relation.string' => 'Mối quan hệ phải là một chuỗi ký tự.',
            // 'relation.max' => 'Mối quan hệ không được vượt quá 100 ký tự.',
        ]);

        try {
            // Tìm bản ghi khai sinh theo ID
            $birthRegistration = BirthRegistration::findOrFail($id);

            // Cập nhật thông tin
            $birthRegistration->update([
                'full_name' => $validatedData['full_name'],
                'birth_date' => $validatedData['birth_date'],
                'gender' => $validatedData['gender'],
                'ethnicity' => $validatedData['ethnicity'],
                'nationality' => $validatedData['nationality'],
                'birth_place' => $validatedData['birth_place'],
                'region_id' => $validatedData['region_id'],
                'residence' => $validatedData['residence'],
                'relation' => 'Con',
                'approval_status' => 0,
            ]);

            // Nếu có liên quan đến thanh toán, xử lý logic cập nhật thanh toán
            $payment = Payment::where('form_type', 'birthRegistrations')
                ->where('record_id', $id)
                ->first();

            if ($payment) {
                $payment->update([
                    'amount' => 50000, // Số tiền thanh toán, có thể thay đổi tùy logic
                    'payment_status' => $payment->payment_status, // Giữ trạng thái hiện tại
                ]);
            }

            // Redirect về trang trước với thông báo thành công
            return redirect()->back()->with('success', 'Thông tin đăng ký khai sinh đã được cập nhật thành công!');
        } catch (\Exception $e) {
            // Log lỗi
            \Log::error('Lỗi khi cập nhật đăng ký khai sinh: ' . $e->getMessage());

            // Redirect lại với thông báo lỗi
            return redirect()->back()->withErrors('Đã xảy ra lỗi trong quá trình cập nhật. Vui lòng thử lại.');
        }
    }


}
