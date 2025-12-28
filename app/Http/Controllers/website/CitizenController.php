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

class CitizenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route("login")->with('success', "Vui lòng đăng nhập tài khoản");
        }
        $regions = Region::all();
        $action = "create";
        $birth_registrations = BirthRegistration::where('user_id', '=', Auth::user()->id)->get();
        return view('website.form.citizens', compact("birth_registrations", 'action', 'regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu từ request
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'cccd' => 'required|string|size:12|unique:citizens,cccd,',
            'region_id' => 'required',
            'birth_date' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(6)->format('Y-m-d'),
            ],
            'gender' => 'required|in:A,N,K',
            'residence' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'relation' => [
                'required',
                'string',
                'max:255',
                // Kiểm tra nếu mối quan hệ đã có 'cha' hoặc 'mẹ' trong bảng citizens
                function ($attribute, $value, $fail) {
                    // Chuyển đổi giá trị thành chữ hoa để đảm bảo không phân biệt chữ hoa chữ thường
                    $existingRelation = DB::table('citizens')
                        ->where("user_id", Auth::user()->id)
                        ->whereRaw('LOWER(relation) = ?', [strtolower($value)]) // So sánh không phân biệt chữ hoa chữ thường
                        ->exists();

                    if ($existingRelation && in_array(strtolower($value), ['cha', 'mẹ'])) {
                        $fail('Mối quan hệ "' . $value . '" đã tồn tại trong hệ thống.');
                    }
                },
            ],
            'nationality' => 'required|string|max:100',
            'ethnicity' => 'required|string|max:100',
            'occupation' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'email' => 'nullable|string',
            'type' => 'nullable|in:0,1',
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên.',
            'full_name.string' => 'Họ và tên phải là chuỗi ký tự.',
            'full_name.max' => 'Họ và tên không được vượt quá 255 ký tự.',

            'cccd.required' => 'Vui lòng nhập số CCCD.',
            'cccd.size' => 'Số CCCD phải đúng 12 ký tự.',
            'cccd.unique' => 'Số CCCD đã tồn tại.',

            'region_id.required' => 'Vui lòng nhập địa chỉ.',


            'birth_date.required' => 'Vui lòng nhập ngày sinh.',
            'birth_date.date' => 'Ngày sinh phải là một ngày hợp lệ.',
            'birth_date.before_or_equal' => 'Ngày sinh phải trước ngày cách đây 6 năm.',

            'gender.required' => 'Vui lòng chọn giới tính.',
            'gender.in' => 'Giới tính phải là A (Nam), N (Nữ) hoặc K (Khác).',

            'residence.required' => 'Vui lòng nhập nơi cư trú.',
            'residence.string' => 'Nơi cư trú phải là chuỗi ký tự.',
            'residence.max' => 'Nơi cư trú không được vượt quá 255 ký tự.',

            'birth_place.required' => 'Vui lòng nhập nơi sinh.',
            'birth_place.string' => 'Nơi sinh phải là chuỗi ký tự.',
            'birth_place.max' => 'Nơi sinh không được vượt quá 255 ký tự.',

            'relation.required' => 'Vui lòng nhập mối quan hệ.',
            'relation.string' => 'Mối quan hệ phải là chuỗi ký tự.',
            'relation.max' => 'Mối quan hệ không được vượt quá 255 ký tự.',

            'nationality.required' => 'Vui lòng nhập quốc tịch.',
            'nationality.string' => 'Quốc tịch phải là chuỗi ký tự.',
            'nationality.max' => 'Quốc tịch không được vượt quá 100 ký tự.',

            'ethnicity.required' => 'Vui lòng nhập dân tộc.',
            'ethnicity.string' => 'Dân tộc phải là chuỗi ký tự.',
            'ethnicity.max' => 'Dân tộc không được vượt quá 100 ký tự.',

            'occupation.required' => 'Vui lòng nhập nghề nghiệp.',
            'occupation.string' => 'Nghề nghiệp phải là chuỗi ký tự.',
            'occupation.max' => 'Nghề nghiệp không được vượt quá 100 ký tự.',

            'notes.string' => 'Ghi chú phải là chuỗi ký tự.',

            'type.in' => 'Loại phải là 0 hoặc 1.',
        ]);



        try {
            // Tạo bản ghi công dân mới trong bảng citizens
            // Tạo bản ghi công dân mới trong bảng citizens
            $citizen = Citizen::create([
                'full_name' => $validatedData['full_name'],
                'cccd' => $validatedData['cccd'],
                'region_id' => $validatedData['region_id'],
                'birth_date' => $validatedData['birth_date'],
                'gender' => $validatedData['gender'],
                'residence' => $validatedData['residence'],
                'birth_place' => $validatedData['birth_place'],
                'relation' => $validatedData['relation'],
                'nationality' => $validatedData['nationality'],
                'ethnicity' => $validatedData['ethnicity'],
                'email' => $validatedData['email'],
                'occupation' => $validatedData['occupation'],
                'notes' => json_encode($validatedData['notes']) ?? null,
                'user_id' => Auth::id(),
                'birth_registration_id' => $validatedData['birth_registration_id'] ?? null,
                'type' => $validatedData['type'] ?? 0,
                'residence_status' => 0,
            ]);

            // Xử lý upload ảnh CCCD
            if ($request->hasFile('cccd_image')) {
                $cccdImagePath = $request->file('cccd_image')->store('citizens', 'public');
                $citizen->cccd_image = $cccdImagePath;
                $citizen->save(); // Lưu bản ghi sau khi cập nhật
            }
            // Tạo bản ghi thanh toán mới trong bảng payments
            Payment::create([
                'user_id' => Auth::id(),
                'form_type' => 'citizen', // Loại form là công dân
                'record_id' => $citizen->id, // ID của công dân vừa tạo
                'amount' => 50000, // Số tiền thanh toán (có thể thay đổi tùy logic)
                'payment_status' => 0 // Trạng thái ban đầu
            ]);

            // Redirect lại với thông báo thành công
            return redirect()->route('citizens')->with('success', 'Công dân đã được thêm và tạo thanh toán thành công!');
        } catch (\Exception $e) {
            // Ghi log lỗi nếu có
            \Log::error('Lỗi khi thêm công dân hoặc tạo thanh toán: ' . $e->getMessage());

            // Redirect lại với thông báo lỗi
            return redirect()->back()->withErrors('Đã xảy ra lỗi trong quá trình thêm công dân hoặc thanh toán. Vui lòng thử lại.');
        }
    }

    public function edit($id)
    {
        $citizen = Citizen::findOrFail($id);
        $action = "edit";
        $regions = Region::all();
        $birth_registrations = BirthRegistration::where('user_id', '=', Auth::user()->id)->get();
        return view("website.form.citizens", compact("citizen", "action", "birth_registrations", 'regions'));
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'cccd' => 'required|string|size:12|unique:citizens,cccd,' . $id,
            'region_id' => 'required',
            'birth_date' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(6)->format('Y-m-d'),
            ],
            'gender' => 'required|in:A,N,K',
            'residence' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'relation' => [
                'required',
                'string',
                'max:255',
                // Kiểm tra nếu mối quan hệ đã có 'cha' hoặc 'mẹ' trong bảng citizens
                function ($attribute, $value, $fail) use ($id) {
                    // Lấy mối quan hệ hiện tại của citizen
                    $currentRelation = DB::table('citizens')->where('id', $id)->where("user_id", Auth::user()->id)->value('relation');

                    // Nếu mối quan hệ hiện tại là 'cha', không kiểm tra khi có sự thay đổi
                    if ($currentRelation != 'Cha' && in_array(strtolower($value), ['Cha', 'Mẹ'])) {
                        // Kiểm tra nếu mối quan hệ đã tồn tại trong hệ thống
                        $existingRelation = DB::table('citizens')
                            ->whereRaw('LOWER(relation) = ?', [strtolower($value)])
                            ->exists();

                        if ($existingRelation) {
                            $fail('Mối quan hệ "' . $value . '" đã tồn tại trong hệ thống.');
                        }
                    }
                },
            ],
            'nationality' => 'required|string|max:100',
            'ethnicity' => 'required|string|max:100',
            'occupation' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'email' => 'nullable|string',
            'type' => 'nullable|in:0,1',
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên.',
            'full_name.string' => 'Họ và tên phải là chuỗi ký tự.',
            'full_name.max' => 'Họ và tên không được vượt quá 255 ký tự.',

            'cccd.required' => 'Vui lòng nhập số CCCD.',
            'cccd.size' => 'Số CCCD phải đúng 12 ký tự.',
            'cccd.unique' => 'Số CCCD đã tồn tại.',

            'region_id.required' => 'Vui lòng nhập địa chỉ.',


            'birth_date.required' => 'Vui lòng nhập ngày sinh.',
            'birth_date.date' => 'Ngày sinh phải là một ngày hợp lệ.',
            'birth_date.before_or_equal' => 'Ngày sinh phải trước ngày cách đây 6 năm.',

            'gender.required' => 'Vui lòng chọn giới tính.',
            'gender.in' => 'Giới tính phải là A (Nam), N (Nữ) hoặc K (Khác).',

            'residence.required' => 'Vui lòng nhập nơi cư trú.',
            'residence.string' => 'Nơi cư trú phải là chuỗi ký tự.',
            'residence.max' => 'Nơi cư trú không được vượt quá 255 ký tự.',

            'birth_place.required' => 'Vui lòng nhập nơi sinh.',
            'birth_place.string' => 'Nơi sinh phải là chuỗi ký tự.',
            'birth_place.max' => 'Nơi sinh không được vượt quá 255 ký tự.',

            'relation.required' => 'Vui lòng nhập mối quan hệ.',
            'relation.string' => 'Mối quan hệ phải là chuỗi ký tự.',
            'relation.max' => 'Mối quan hệ không được vượt quá 255 ký tự.',

            'nationality.required' => 'Vui lòng nhập quốc tịch.',
            'nationality.string' => 'Quốc tịch phải là chuỗi ký tự.',
            'nationality.max' => 'Quốc tịch không được vượt quá 100 ký tự.',

            'ethnicity.required' => 'Vui lòng nhập dân tộc.',
            'ethnicity.string' => 'Dân tộc phải là chuỗi ký tự.',
            'ethnicity.max' => 'Dân tộc không được vượt quá 100 ký tự.',

            'occupation.required' => 'Vui lòng nhập nghề nghiệp.',
            'occupation.string' => 'Nghề nghiệp phải là chuỗi ký tự.',
            'occupation.max' => 'Nghề nghiệp không được vượt quá 100 ký tự.',

            'notes.string' => 'Ghi chú phải là chuỗi ký tự.',

            'type.in' => 'Loại phải là 0 hoặc 1.',
        ]);




        // Tìm bản ghi công dân dựa trên ID
        $citizen = Citizen::findOrFail($id);

        // Cập nhật thông tin công dân
        $citizen->update([
            'full_name' => $validatedData['full_name'],
            'cccd' => $validatedData['cccd'],
            'region_id' => $validatedData['region_id'],
            'birth_date' => $validatedData['birth_date'],
            'gender' => $validatedData['gender'],
            'residence' => $validatedData['residence'],
            'birth_place' => $validatedData['birth_place'],
            'relation' => $validatedData['relation'],
            'nationality' => $validatedData['nationality'],
            'ethnicity' => $validatedData['ethnicity'],
            'occupation' => $validatedData['occupation'],
            'notes' => $validatedData['notes'] ? json_encode($validatedData['notes']) : null,
            'type' => $validatedData['type'] ?? 0,
            'email' => $validatedData['email'],
            'residence_status' => 0
        ]);

        // Xử lý upload ảnh CCCD mới
        if ($request->hasFile('cccd_image')) {
            // Xóa ảnh cũ nếu tồn tại   
            if ($citizen->cccd_image && \Storage::disk('public')->exists($citizen->cccd_image)) {
                \Storage::disk('public')->delete($citizen->cccd_image);
            }

            // Lưu ảnh mới
            $cccdImagePath = $request->file('cccd_image')->store('citizens', 'public');
            $citizen->cccd_image = $cccdImagePath;
            $citizen->save(); // Lưu lại sau khi cập nhật
        }

        // Cập nhật thanh toán nếu cần
        $payment = Payment::where('form_type', 'citizen')
            ->where('record_id', $id)
            ->first();

        if ($payment) {
            $payment->update([
                'amount' => 50000, // Có thể thay đổi tùy logic
                'payment_status' => $payment->payment_status, // Giữ trạng thái hiện tại
            ]);
        }

        // Redirect lại với thông báo thành công
        return redirect()->back()->with('success', 'Công dân đã được cập nhật thành công!');

    }



}
