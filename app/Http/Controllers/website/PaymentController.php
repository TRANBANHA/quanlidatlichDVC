<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\HoSo;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Danh sách thanh toán
     */
    public function index()
    {
        $payments = Payment::where('nguoi_dung_id', Auth::guard('web')->id())
            ->with(['user', 'hoSo.dichVu'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('website.payment.index', compact('payments'));
    }

    /**
     * Tạo thanh toán cho hồ sơ
     */
    public function create($hoSoId)
    {
        $hoSo = HoSo::with(['dichVu', 'donVi'])->findOrFail($hoSoId);

        // Kiểm tra quyền
        if ($hoSo->nguoi_dung_id != Auth::guard('web')->id()) {
            abort(403);
        }

        // Lấy phí dịch vụ từ ServicePhuong
        $servicePhuong = $hoSo->dichVu->getServiceForPhuong($hoSo->don_vi_id);
        $phiDichVu = $servicePhuong ? $servicePhuong->phi_dich_vu : 0;

        // Kiểm tra đã có thanh toán chưa
        $existingPayment = Payment::where('ho_so_id', $hoSoId)
            ->where('trang_thai_thanh_toan', '!=', 'that_bai')
            ->first();

        if ($existingPayment && $existingPayment->trang_thai_thanh_toan == 'da_thanh_toan') {
            return redirect()->route('payment.show', $existingPayment->id)
                ->with('info', 'Hồ sơ này đã được thanh toán.');
        }

        return view('website.payment.create', compact('hoSo', 'phiDichVu', 'existingPayment'));
    }

    /**
     * Tạo thanh toán VNPay
     */
    public function createVNPayPayment(Request $request, $hoSoId)
    {
        $hoSo = HoSo::with(['dichVu', 'donVi'])->findOrFail($hoSoId);

        // Kiểm tra quyền
        if ($hoSo->nguoi_dung_id != Auth::guard('web')->id()) {
            abort(403);
        }

        // Lấy phí dịch vụ từ ServicePhuong
        $servicePhuong = $hoSo->dichVu->getServiceForPhuong($hoSo->don_vi_id);
        $phiDichVu = $servicePhuong ? $servicePhuong->phi_dich_vu : 0;

        if ($phiDichVu <= 0) {
            return redirect()->back()->with('error', 'Phí dịch vụ không hợp lệ.');
        }

        // Tạo mã giao dịch duy nhất
        $maGiaoDich = 'VNPAY_' . time() . '_' . Str::random(8);

        // Tạo hoặc cập nhật payment
        $payment = Payment::updateOrCreate(
            [
                'ho_so_id' => $hoSoId,
                'nguoi_dung_id' => Auth::guard('web')->id(),
            ],
            [
                'so_tien' => $phiDichVu,
                'trang_thai_thanh_toan' => 'cho_thanh_toan',
                'phuong_thuc_thanh_toan' => 'vnpay',
                'ma_giao_dich' => $maGiaoDich,
            ]
        );

        // Tạo URL thanh toán VNPay
        $vnpayService = new VNPayService();
        $orderDescription = 'Thanh toán phí dịch vụ: ' . ($hoSo->dichVu->ten_dich_vu ?? 'N/A') . ' - Mã hồ sơ: ' . $hoSo->ma_ho_so;
        
        $bankCode = $request->input('bank_code', '');
        $paymentUrl = $vnpayService->createPaymentUrl(
            $phiDichVu,
            $maGiaoDich,
            $orderDescription,
            'other',
            $bankCode
        );

        return redirect($paymentUrl);
    }

    /**
     * Xử lý kết quả trả về từ VNPay (Return URL)
     */
    public function vnpayReturn(Request $request)
    {
        $inputData = $request->all();
        
        $vnpayService = new VNPayService();
        $result = $vnpayService->processPaymentResult($inputData);

        $vnp_TxnRef = $inputData['vnp_TxnRef'] ?? '';
        $payment = Payment::where('ma_giao_dich', $vnp_TxnRef)->first();

        if (!$payment) {
            return redirect()->route('payment.index')
                ->with('error', 'Không tìm thấy giao dịch thanh toán.');
        }

        if ($result['success']) {
            // Cập nhật trạng thái thanh toán thành công
            $payment->update([
                'trang_thai_thanh_toan' => 'da_thanh_toan',
                'ngay_thanh_toan' => now(),
                'du_lieu_vnpay' => $result['data'],
            ]);

            return redirect()->route('payment.show', $payment->id)
                ->with('success', 'Thanh toán thành công!');
        } else {
            // Cập nhật trạng thái thanh toán thất bại
            $payment->update([
                'trang_thai_thanh_toan' => 'that_bai',
                'du_lieu_vnpay' => $result['data'],
            ]);

            return redirect()->route('payment.create', $payment->ho_so_id)
                ->with('error', $result['message']);
        }
    }

    /**
     * Xử lý callback từ VNPay (IPN - Instant Payment Notification)
     */
    public function vnpayCallback(Request $request)
    {
        $inputData = $request->all();
        
        $vnpayService = new VNPayService();
        $result = $vnpayService->processPaymentResult($inputData);

        $vnp_TxnRef = $inputData['vnp_TxnRef'] ?? '';
        $payment = Payment::where('ma_giao_dich', $vnp_TxnRef)->first();

        if (!$payment) {
            return response()->json([
                'RspCode' => '01',
                'Message' => 'Không tìm thấy giao dịch'
            ], 200);
        }

        if ($result['success']) {
            // Chỉ cập nhật nếu chưa thanh toán
            if ($payment->trang_thai_thanh_toan != 'da_thanh_toan') {
                $payment->update([
                    'trang_thai_thanh_toan' => 'da_thanh_toan',
                    'ngay_thanh_toan' => now(),
                    'du_lieu_vnpay' => $result['data'],
                ]);
            }

            return response()->json([
                'RspCode' => '00',
                'Message' => 'Success'
            ], 200);
        } else {
            return response()->json([
                'RspCode' => '99',
                'Message' => $result['message']
            ], 200);
        }
    }

    /**
     * Upload ảnh chứng từ thanh toán và xác nhận
     */
    public function uploadProof(Request $request, $paymentId)
    {
        $request->validate([
            'proof_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'note' => 'nullable|string|max:500',
        ], [
            'proof_image.required' => 'Vui lòng upload ảnh chứng từ thanh toán.',
            'proof_image.image' => 'File phải là hình ảnh.',
            'proof_image.mimes' => 'Ảnh phải có định dạng: jpg, jpeg, png.',
            'proof_image.max' => 'Dung lượng ảnh không được vượt quá 2MB.',
        ]);

        $payment = Payment::findOrFail($paymentId);

        if ($payment->nguoi_dung_id != Auth::guard('web')->id()) {
            abort(403);
        }

        // Lưu ảnh chứng từ
        if ($request->hasFile('proof_image')) {
            $imagePath = $request->file('proof_image')->store('payment_proofs', 'public');
            $payment->hinh_anh = $imagePath;
        }

        // Lưu ghi chú
        if ($request->note) {
            $payment->giai_trinh = $request->note;
        }

        // Cập nhật trạng thái - vẫn giữ "cho_thanh_toan" để admin có thể xác nhận
        // Admin sẽ xem ảnh và xác nhận thủ công
        $payment->save();

        return redirect()->route('payment.show', $payment->id)
            ->with('success', 'Đã upload ảnh chứng từ. Vui lòng chờ admin xác nhận thanh toán.');
    }


    /**
     * Xem chi tiết thanh toán
     */
    public function show($id)
    {
        $payment = Payment::with(['hoSo.dichVu', 'user'])->findOrFail($id);

        if ($payment->nguoi_dung_id != Auth::guard('web')->id()) {
            abort(403);
        }

        return view('website.payment.show', compact('payment'));
    }
}

