<?php

namespace App\Http\Controllers\website;

use Carbon\Carbon;
use App\Models\Citizen;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\AbsenceRegistration;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AbsenceRegistrationController extends Controller
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
        $citizens = Citizen::where('user_id', Auth::user()->id)
            ->where('residence_status', 1)
            ->get();
        return view('website.form.absence', compact('citizens', "action"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'destination' => 'required|string|max:255',
            'reason' => 'required|string|max:255',
            'departure_date' => 'required|date|before_or_equal:today',
            'return_date' => 'required|date|after:departure_date',
        ], [
            'destination.required' => 'Vui lòng nhập điểm đến.',
            'destination.string' => 'Điểm đến phải là chuỗi ký tự.',
            'destination.max' => 'Điểm đến không được vượt quá 255 ký tự.',

            'reason.required' => 'Vui lòng nhập lý do.',
            'reason.string' => 'Lý do phải là chuỗi ký tự.',
            'reason.max' => 'Lý do không được vượt quá 255 ký tự.',

            'departure_date.required' => 'Vui lòng nhập ngày khởi hành.',
            'departure_date.date' => 'Ngày khởi hành phải là một ngày hợp lệ.',
            'departure_date.before_or_equal' => 'Ngày khởi hành không được lớn hơn ngày hiện tại.',

            'return_date.required' => 'Vui lòng nhập ngày trở về.',
            'return_date.date' => 'Ngày trở về phải là một ngày hợp lệ.',
            'return_date.after' => 'Ngày trở về phải lớn hơn ngày khởi hành.',
        ]);


        $request['user_id'] = Auth::user()->id;
        $request['registration_date'] = Carbon::now();
        $request['approval_status'] = 0;
        // Create a new absence registration record
        $AbsenceRegistration = AbsenceRegistration::create($request->all());

        Payment::create([
            'user_id' => Auth::id(),
            'form_type' => 'absenceRegistrations', // Loại form là công dân
            'record_id' => $AbsenceRegistration->id, // ID của công dân vừa tạo
            'amount' => 50000, // Số tiền thanh toán (có thể thay đổi tùy logic)
            'payment_status' => 0 // Trạng thái ban đầu
        ]);
        // Redirect to the index page with a success message
        return redirect()->route('absence')->with('success', 'Đăng ký tạm vắng đã được tạo thành công!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (!Auth::check()) {
            return redirect()->route("login")->with('success', "Vui lòng đăng nhập tài khoản");
        }

        $absence = AbsenceRegistration::findOrFail($id);

        // Kiểm tra quyền sở hữu bản ghi
        if ($absence->user_id !== Auth::id()) {
            return redirect()->route('absence')->with('error', 'Bạn không có quyền chỉnh sửa bản ghi này.');
        }

        $citizens = Citizen::where('user_id', Auth::user()->id)
            ->where('residence_status', 1)
            ->get();

        return view('website.form.absence', compact('absence', 'citizens'))->with('action', 'edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'destination' => 'required|string|max:255',
            'reason' => 'required|string|max:255',
            'departure_date' => 'required|date|before_or_equal:today',
            'return_date' => 'required|date|after:departure_date',
        ], [
            'destination.required' => 'Vui lòng nhập điểm đến.',
            'destination.string' => 'Điểm đến phải là chuỗi ký tự.',
            'destination.max' => 'Điểm đến không được vượt quá 255 ký tự.',

            'reason.required' => 'Vui lòng nhập lý do.',
            'reason.string' => 'Lý do phải là chuỗi ký tự.',
            'reason.max' => 'Lý do không được vượt quá 255 ký tự.',

            'departure_date.required' => 'Vui lòng nhập ngày khởi hành.',
            'departure_date.date' => 'Ngày khởi hành phải là một ngày hợp lệ.',
            'departure_date.before_or_equal' => 'Ngày khởi hành không được lớn hơn ngày hiện tại.',

            'return_date.required' => 'Vui lòng nhập ngày trở về.',
            'return_date.date' => 'Ngày trở về phải là một ngày hợp lệ.',
            'return_date.after' => 'Ngày trở về phải lớn hơn ngày khởi hành.',
        ]);



        $absence = AbsenceRegistration::findOrFail($id);

        // Kiểm tra quyền sở hữu bản ghi
        if ($absence->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền chỉnh sửa bản ghi này.');
        }

        // Cập nhật thông tin
        $absence->update([
            'destination' => $request->destination,
            'reason' => $request->reason,
            'departure_date' => $request->departure_date,
            'return_date' => $request->return_date,
            'citizen_id' => $request->citizen_id,
            'approval_status' => 0
        ]);

        return redirect()->back()->with('success', 'Đăng ký tạm vắng đã được cập nhật thành công!');
    }

}
