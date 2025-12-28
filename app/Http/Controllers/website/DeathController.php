<?php

namespace App\Http\Controllers\website;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Citizen;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\DeathRegistration;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DeathController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route("login")->with('success', "Vui lòng đăng nhập tài khoản");
        }
        $citizens = Citizen::where('user_id', Auth::user()->id)
            // ->where('residence_status', 1)
            ->get();
        $action = 'create'; // Mặc định là create
        return view('website.form.death', compact('citizens', 'action'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'deceased_birth_date' => 'required|date|before_or_equal:today', // Ngày sinh không được lớn hơn hôm nay
            'deceased_death_date' => 'required|date|before_or_equal:today', // Ngày mất không được vượt quá hôm nay
            'deceased_death_place' => 'required|string|max:255', // Nơi mất
            'cause_of_death' => 'nullable|string|max:500', // Nguyên nhân không bắt buộc
            'death_certification' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // File chứng nhận
        ], [
            'deceased_birth_date.required' => 'Ngày sinh là bắt buộc.',
            'deceased_birth_date.date' => 'Ngày sinh phải là định dạng ngày hợp lệ.',
            'deceased_birth_date.before_or_equal' => 'Ngày sinh không được lớn hơn ngày hôm nay.',

            'deceased_death_date.required' => 'Ngày mất là bắt buộc.',
            'deceased_death_date.date' => 'Ngày mất phải là định dạng ngày hợp lệ.',
            'deceased_death_date.before_or_equal' => 'Ngày mất không được vượt quá ngày hôm nay.',

            'deceased_death_place.required' => 'Nơi mất là bắt buộc.',
            'deceased_death_place.string' => 'Nơi mất phải là chuỗi ký tự.',
            'deceased_death_place.max' => 'Nơi mất không được vượt quá 255 ký tự.',

            'cause_of_death.string' => 'Nguyên nhân mất phải là chuỗi ký tự.',
            'cause_of_death.max' => 'Nguyên nhân mất không được vượt quá 500 ký tự.',

            'death_certification.required' => 'File chứng nhận khai tử là bắt buộc.',
            'death_certification.file' => 'File chứng nhận phải là một tệp hợp lệ.',
            'death_certification.mimes' => 'File chứng nhận phải có định dạng: pdf, jpg, jpeg, png.',
            'death_certification.max' => 'File chứng nhận không được vượt quá 2MB.',
        ]);

        // try {
            // Tạo bản ghi đăng ký khai tử
            $deathRegistration = new DeathRegistration();
            $deathRegistration->user_id = Auth::id(); // Người dùng hiện tại
            $deathRegistration->citizen_id = $request['citizen_id'];
            // $deathRegistration->deceased_full_name = $request->deceased_full_name;
            $deathRegistration->deceased_cccd = $request->deceased_cccd;
            $deathRegistration->deceased_birth_date = $request->deceased_birth_date;
            $deathRegistration->deceased_death_date = $request['deceased_death_date'];
            $deathRegistration->deceased_birth_place = $request->deceased_birth_place;
            $deathRegistration->deceased_death_place = $request['deceased_birth_place'];
            $deathRegistration->deceased_residence = $request->deceased_residence;
            $deathRegistration->cause_of_death = $request['cause_of_death'];
            $deathRegistration->applicant_full_name = Auth::user()->name;
            $deathRegistration->applicant_relationship = $request['applicant_relationship']; // Quan hệ mặc định
            $deathRegistration->applicant_cccd = Auth::user()->citizens->first()->cccd ?? 'Không có CCCD';
            $deathRegistration->applicant_phone = Auth::user()->phone ?? '0989449675';
            $deathRegistration->approval_status = 0; // Trạng thái chưa duyệt
            $deathRegistration->registration_date = Carbon::now();

            // Lưu file chứng nhận khai tử
            if ($request->hasFile('death_certification')) {
                $deathRegistration->death_certification = $request->file('death_certification')->store('certifications', 'public');
            }

            $deathRegistration->save();

            // Tạo bản ghi thanh toán
            Payment::create([
                'user_id' => Auth::id(), // Người thực hiện
                'form_type' => 'deathRegistrations', // Loại biểu mẫu
                'record_id' => $deathRegistration->id, // ID bản ghi khai tử
                'amount' => 100000, // Số tiền thanh toán
                'payment_status' => 0, // Chưa thanh toán
            ]);
            return redirect()->back()->with('success', 'Đăng ký khai tử và tạo thanh toán thành công!');
        // } catch (\Exception $e) {
        //     // Xử lý lỗi và ghi log
        //     \Log::error('Error registering death: ' . $e->getMessage());
        //     return redirect()->back()->withErrors(['error' => 'Có lỗi xảy ra, vui lòng thử lại sau.']);
        // }
    }
    public function edit($id)
    {
        if (!Auth::check()) {
            return redirect()->route("login")->with('success', "Vui lòng đăng nhập tài khoản");
        }

        $deathRegistration = DeathRegistration::findOrFail($id); // Tìm bản ghi khai tử
        $citizens = Citizen::where('user_id', Auth::user()->id)
            ->where('residence_status', 1)
            ->get();
        $action = 'edit'; // Chỉ định action là edit
        return view('website.form.death', compact('deathRegistration', 'citizens', 'action'));
    }
    public function update(Request $request, $id)
    {
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'deceased_birth_date' => 'required|date|before_or_equal:today', // Ngày sinh không được lớn hơn hôm nay
            'deceased_death_date' => 'required|date|before_or_equal:today', // Ngày mất không được vượt quá hôm nay
            'deceased_death_place' => 'required|string|max:255', // Nơi mất
            'cause_of_death' => 'nullable|string|max:500', // Nguyên nhân không bắt buộc
            'death_certification' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // File chứng nhận
        ], [
            'deceased_birth_date.required' => 'Ngày sinh là bắt buộc.',
            'deceased_birth_date.date' => 'Ngày sinh phải là định dạng ngày hợp lệ.',
            'deceased_birth_date.before_or_equal' => 'Ngày sinh không được lớn hơn ngày hôm nay.',

            'deceased_death_date.required' => 'Ngày mất là bắt buộc.',
            'deceased_death_date.date' => 'Ngày mất phải là định dạng ngày hợp lệ.',
            'deceased_death_date.before_or_equal' => 'Ngày mất không được vượt quá ngày hôm nay.',

            'deceased_death_place.required' => 'Nơi mất là bắt buộc.',
            'deceased_death_place.string' => 'Nơi mất phải là chuỗi ký tự.',
            'deceased_death_place.max' => 'Nơi mất không được vượt quá 255 ký tự.',

            'cause_of_death.string' => 'Nguyên nhân mất phải là chuỗi ký tự.',
            'cause_of_death.max' => 'Nguyên nhân mất không được vượt quá 500 ký tự.',

            'death_certification.required' => 'File chứng nhận khai tử là bắt buộc.',
            'death_certification.file' => 'File chứng nhận phải là một tệp hợp lệ.',
            'death_certification.mimes' => 'File chứng nhận phải có định dạng: pdf, jpg, jpeg, png.',
            'death_certification.max' => 'File chứng nhận không được vượt quá 2MB.',
        ]);


        try {
            // Tìm và cập nhật bản ghi khai tử
            $deathRegistration = DeathRegistration::findOrFail($id);
            $deathRegistration->citizen_id = $request['citizen_id'];
            // $deathRegistration->deceased_full_name = $request->deceased_full_name;
            $deathRegistration->deceased_cccd = $request->deceased_cccd;
            $deathRegistration->deceased_birth_date = $request->deceased_birth_date;
            $deathRegistration->deceased_death_date = $request['deceased_death_date'];
            $deathRegistration->deceased_birth_place = $request->deceased_birth_place;
            $deathRegistration->deceased_death_place = $request['deceased_death_place'];
            $deathRegistration->deceased_residence = $request->deceased_residence;
            $deathRegistration->cause_of_death = $request['cause_of_death'];
            $deathRegistration->approval_status = 0;

            // Cập nhật file chứng nhận nếu có
            if ($request->hasFile('death_certification')) {
                $deathRegistration->death_certification = $request->file('death_certification')->store('certifications', 'public');
            }

            $deathRegistration->save();
            return redirect()->back()->with('success', 'Cập nhật thông tin khai tử thành công!');
        } catch (\Exception $e) {
            \Log::error('Error updating death registration: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Có lỗi xảy ra, vui lòng thử lại sau.']);
        }
    }

}