<?php

namespace App\Http\Controllers\website;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Region;
use App\Models\Citizen;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\TempResidenceRegistration;

class TempResidenceController extends Controller
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
        $regions = Region::all();
        return view("website.form.residence", compact("action", 'citizens', 'regions'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'full_name' => 'required|string|max:255',
            'cccd' => 'required|string|max:12|unique:temp_residence_registrations,cccd,',
            'start_date' => 'required|date|before:today',
            'birth_day' => 'required|date|before:today',
            'end_date' => [
                'required',
                'date',
                'after:start_date',
                function ($attribute, $value, $fail) use ($request) {
                    $startDate = \Carbon\Carbon::parse($request->start_date);
                    $endDate = \Carbon\Carbon::parse($value);

                    if ($startDate->diffInYears($endDate) > 2) {
                        $fail('Ngày kết thúc không được cách ngày bắt đầu quá 2 năm.');
                    }
                },
            ],

            'gender' => 'required',
            'phone' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'birth_place' => 'required|string|max:255',
            'current_residence' => 'required|string|max:255',
            'permanent_residence' => 'required|string|max:255',
            'region_id' => 'required',
            'nationality' => 'required|string|max:100',
            'ethnicity' => 'required|string|max:100',
            'occupation' => 'required|string|max:100',
            'relationship_with_host' => 'required|in:Chủ trọ,Gì cháu,Anh em,Bạn bè,Khác',
        ], [
            'full_name.required' => 'Họ và tên là bắt buộc.',
            'full_name.string' => 'Họ và tên phải là chuỗi ký tự.',
            'full_name.max' => 'Họ và tên không được vượt quá 255 ký tự.',

            'cccd.required' => 'Số CCCD là bắt buộc.',
            'cccd.string' => 'Số CCCD phải là chuỗi ký tự.',
            'cccd.max' => 'Số CCCD không được vượt quá 12 ký tự.',
            'cccd.unique' => 'Số CCCD đã tồn tại.',


            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'start_date.date' => 'Ngày bắt đầu phải là định dạng ngày hợp lệ.',
            'start_date.before' => 'Ngày bắt đầu không được lớn hơn ngày hiện tại.',

            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'end_date.date' => 'Ngày kết thúc phải là định dạng ngày hợp lệ.',
            'end_date.after' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu.',

            'gender.required' => 'Giới tính là bắt buộc.',

            'phone.required' => 'Số điện thoại là bắt buộc.',
            'phone.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá 15 ký tự.',

            'email.email' => 'Email phải là địa chỉ email hợp lệ.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',

            'birth_place.required' => 'Nơi sinh là bắt buộc.',
            'birth_place.string' => 'Nơi sinh phải là chuỗi ký tự.',
            'birth_place.max' => 'Nơi sinh không được vượt quá 255 ký tự.',

            'current_residence.required' => 'Nơi ở hiện tại là bắt buộc.',
            'current_residence.string' => 'Nơi ở hiện tại phải là chuỗi ký tự.',
            'current_residence.max' => 'Nơi ở hiện tại không được vượt quá 255 ký tự.',

            'permanent_residence.required' => 'Nơi ở thường trú là bắt buộc.',
            'permanent_residence.string' => 'Nơi ở thường trú phải là chuỗi ký tự.',
            'permanent_residence.max' => 'Nơi ở thường trú không được vượt quá 255 ký tự.',

            'region_id.required' => 'Nơi tạm trú là bắt buộc.',

            'nationality.required' => 'Quốc tịch là bắt buộc.',
            'nationality.string' => 'Quốc tịch phải là chuỗi ký tự.',
            'nationality.max' => 'Quốc tịch không được vượt quá 100 ký tự.',

            'ethnicity.required' => 'Dân tộc là bắt buộc.',
            'ethnicity.string' => 'Dân tộc phải là chuỗi ký tự.',
            'ethnicity.max' => 'Dân tộc không được vượt quá 100 ký tự.',

            'occupation.required' => 'Nghề nghiệp là bắt buộc.',
            'occupation.string' => 'Nghề nghiệp phải là chuỗi ký tự.',
            'occupation.max' => 'Nghề nghiệp không được vượt quá 100 ký tự.',

            'relationship_with_host.required' => 'Quan hệ với chủ hộ là bắt buộc.',
            'relationship_with_host.in' => 'Quan hệ với chủ hộ phải nằm trong danh sách: Chủ trọ, Gì cháu, Anh em, Bạn bè, Khác.',
        ]);


        // Prepare data for insertion
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['approval_status'] = 0; // Default approval status
        $data['registration_date'] = Carbon::now(); // Current registration date

        // Check gender and set default if necessary
        $data['gender'] = $request->input('gender', 'other'); // Default to 'other' if not set

        // Create a new TempResidenceRegistration entry
        $TempResidenceRegistration = TempResidenceRegistration::create($data);

        // Create a related Payment entry
        Payment::create([
            'user_id' => Auth::id(),
            'form_type' => 'tempResidenceRegistrations',
            'record_id' => $TempResidenceRegistration->id,
            'amount' => 50000, // Payment amount
            'payment_status' => 0 // Initial status
        ]);

        // Redirect to the index page with a success message
        return redirect()->route('temp-residence')->with('success', 'Đăng ký tạm trú đã được tạo thành công!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route("login")->with('success', "Vui lòng đăng nhập tài khoản");
        }
        $regions = Region::all();
        // Find the TempResidenceRegistration by ID
        $tempResidence = TempResidenceRegistration::findOrFail($id);
        $action = "edit";
        // Check if the logged-in user is the owner of the residence registration (optional)
        if ($tempResidence->user_id !== Auth::id()) {
            return redirect()->route('temp-residence')->with('error', 'Bạn không có quyền chỉnh sửa đăng ký này.');
        }

        // Return the edit view with the data to be updated
        return view('website.form.residence', compact('tempResidence', 'action', 'regions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'full_name' => 'required|string|max:255',
            'cccd' => 'required|string|max:12|unique:temp_residence_registrations,cccd,' . $id,
            'start_date' => 'required|date|before:today',
            'birth_day' => 'required|date|before:today',
            'end_date' => [
                'required',
                'date',
                'after:start_date',
                function ($attribute, $value, $fail) use ($request) {
                    $startDate = \Carbon\Carbon::parse($request->start_date);
                    $endDate = \Carbon\Carbon::parse($value);

                    if ($startDate->diffInYears($endDate) > 2) {
                        $fail('Ngày kết thúc không được cách ngày bắt đầu quá 2 năm.');
                    }
                },
            ],

            'gender' => 'required',
            'phone' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'birth_place' => 'required|string|max:255',
            'current_residence' => 'required|string|max:255',
            'permanent_residence' => 'required|string|max:255',
            'region_id' => 'required',
            'nationality' => 'required|string|max:100',
            'ethnicity' => 'required|string|max:100',
            'occupation' => 'required|string|max:100',

            'relationship_with_host' => 'required|in:Chủ trọ,Gì cháu,Anh em,Bạn bè,Khác',
        ], [
            'full_name.required' => 'Họ và tên là bắt buộc.',
            'full_name.string' => 'Họ và tên phải là chuỗi ký tự.',
            'full_name.max' => 'Họ và tên không được vượt quá 255 ký tự.',

            'cccd.required' => 'Số CCCD là bắt buộc.',
            'cccd.string' => 'Số CCCD phải là chuỗi ký tự.',
            'cccd.max' => 'Số CCCD không được vượt quá 12 ký tự.',
            'cccd.unique' => 'Số CCCD đã tồn tại.',

            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'start_date.date' => 'Ngày bắt đầu phải là định dạng ngày hợp lệ.',
            'start_date.before' => 'Ngày bắt đầu không được lớn hơn ngày hiện tại.',

            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'end_date.date' => 'Ngày kết thúc phải là định dạng ngày hợp lệ.',
            'end_date.after' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu.',

            'gender.required' => 'Giới tính là bắt buộc.',

            'phone.required' => 'Số điện thoại là bắt buộc.',
            'phone.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá 15 ký tự.',

            'email.email' => 'Email phải là địa chỉ email hợp lệ.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',

            'birth_place.required' => 'Nơi sinh là bắt buộc.',
            'birth_place.string' => 'Nơi sinh phải là chuỗi ký tự.',
            'birth_place.max' => 'Nơi sinh không được vượt quá 255 ký tự.',

            'current_residence.required' => 'Nơi ở hiện tại là bắt buộc.',
            'current_residence.string' => 'Nơi ở hiện tại phải là chuỗi ký tự.',
            'current_residence.max' => 'Nơi ở hiện tại không được vượt quá 255 ký tự.',

            'permanent_residence.required' => 'Nơi ở thường trú là bắt buộc.',
            'permanent_residence.string' => 'Nơi ở thường trú phải là chuỗi ký tự.',
            'permanent_residence.max' => 'Nơi ở thường trú không được vượt quá 255 ký tự.',

            'region_id.required' => 'Nơi tạm trú là bắt buộc.',

            'nationality.required' => 'Quốc tịch là bắt buộc.',
            'nationality.string' => 'Quốc tịch phải là chuỗi ký tự.',
            'nationality.max' => 'Quốc tịch không được vượt quá 100 ký tự.',

            'ethnicity.required' => 'Dân tộc là bắt buộc.',
            'ethnicity.string' => 'Dân tộc phải là chuỗi ký tự.',
            'ethnicity.max' => 'Dân tộc không được vượt quá 100 ký tự.',

            'occupation.required' => 'Nghề nghiệp là bắt buộc.',
            'occupation.string' => 'Nghề nghiệp phải là chuỗi ký tự.',
            'occupation.max' => 'Nghề nghiệp không được vượt quá 100 ký tự.',

            'relationship_with_host.required' => 'Quan hệ với chủ hộ là bắt buộc.',
            'relationship_with_host.in' => 'Quan hệ với chủ hộ phải nằm trong danh sách: Chủ trọ, Gì cháu, Anh em, Bạn bè, Khác.',
        ]);


        // Find the TempResidenceRegistration by ID
        $tempResidence = TempResidenceRegistration::findOrFail($id);

        // Check if the logged-in user is the owner of the residence registration (optional)
        if ($tempResidence->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền chỉnh sửa đăng ký này.');
        }
        // Prepare data for update
        $data = $request->all();
        $data['registration_date'] = Carbon::now(); // Update registration date to current time (optional)
        $data['approval_status'] = 0; // Update registration date to current time (optional)
        // Update the TempResidenceRegistration entry
        $tempResidence->update($data);

        // Check if a new payment is required, or if it should stay the same (optional)
        // If you need to update payment details, you can update the Payment model here.

        // Redirect back to the listing page with a success message
        return redirect()->back()->with('success', 'Đăng ký tạm trú đã được cập nhật thành công!');
    }

}
