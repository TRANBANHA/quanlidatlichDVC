<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\HoSo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

        // Tạo payment mới nếu chưa có
        if (!$existingPayment) {
            $existingPayment = Payment::create([
                'nguoi_dung_id' => Auth::guard('web')->id(),
                'ho_so_id' => $hoSoId,
                'so_tien' => $phiDichVu,
                'trang_thai_thanh_toan' => 'cho_thanh_toan',
                'phuong_thuc_thanh_toan' => 'qr_code',
                'ma_giao_dich' => 'QR_' . time() . '_' . Str::random(8),
            ]);
        }

        // Lấy cấu hình QR code từ phường của hồ sơ
        $donVi = $hoSo->donVi;
        $qrBankName = $donVi->qr_bank_name ?? '';
        $qrAccountNumber = $donVi->qr_account_number ?? '';
        $qrAccountName = $donVi->qr_account_name ?? '';
        $qrImage = $donVi->qr_image ?? '';

        // Tạo nội dung QR code theo chuẩn VietQR (EMV QR Code) - chỉ tạo nếu không có ảnh QR
        $qrContent = '';
        $useQRImage = false;
        if ($qrImage) {
            // Nếu có ảnh QR code, sử dụng ảnh đó
            $useQRImage = true;
        } elseif ($qrAccountNumber && $qrAccountName) {
            // Nếu không có ảnh, tạo QR code tự động
            $qrContent = \App\Helpers\VietQRHelper::generateVietQR(
                $qrAccountNumber,
                $qrAccountName,
                $phiDichVu,
                $existingPayment->ma_giao_dich
            );
        }

        return view('website.payment.create', compact('hoSo', 'phiDichVu', 'existingPayment', 'qrBankName', 'qrAccountNumber', 'qrAccountName', 'qrContent', 'qrImage', 'useQRImage'));
    }

    /**
     * Tạo thanh toán QR code
     */
    public function createQRPayment($hoSoId)
    {
        $hoSo = HoSo::findOrFail($hoSoId);

        if ($hoSo->nguoi_dung_id != Auth::guard('web')->id()) {
            abort(403);
        }

        $servicePhuong = $hoSo->dichVu->getServiceForPhuong($hoSo->don_vi_id);
        $amount = $servicePhuong ? $servicePhuong->phi_dich_vu : 0;

        // Tạo mã thanh toán duy nhất
        $maThanhToan = 'QR_' . time() . '_' . Str::random(8);

        // Tạo hoặc cập nhật payment
        $payment = Payment::updateOrCreate(
            ['ho_so_id' => $hoSoId, 'nguoi_dung_id' => Auth::guard('web')->id()],
            [
                'so_tien' => $amount,
                'trang_thai_thanh_toan' => 'cho_thanh_toan',
                'phuong_thuc_thanh_toan' => 'qr_code',
                'ma_giao_dich' => $maThanhToan,
            ]
        );

        return redirect()->route('payment.create', $hoSoId)
            ->with('payment', $payment);
    }

    /**
     * Kiểm tra trạng thái thanh toán QR code (AJAX)
     */
    public function checkQRPaymentStatus($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        if ($payment->nguoi_dung_id != Auth::guard('web')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => $payment->trang_thai_thanh_toan,
            'ma_giao_dich' => $payment->ma_giao_dich,
            'so_tien' => $payment->so_tien,
            'ngay_thanh_toan' => $payment->ngay_thanh_toan ? $payment->ngay_thanh_toan->format('d/m/Y H:i:s') : null,
        ]);
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
     * Xác nhận thanh toán QR code (tự động khi quét QR hoặc được gọi từ webhook)
     */
    public function confirmQRPayment(Request $request, $maGiaoDich)
    {
        $payment = Payment::where('ma_giao_dich', $maGiaoDich)->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy giao dịch'
            ], 404);
        }

        // Kiểm tra nếu đã thanh toán rồi
        if ($payment->trang_thai_thanh_toan == 'da_thanh_toan') {
            return response()->json([
                'success' => true,
                'message' => 'Giao dịch đã được thanh toán',
                'already_paid' => true
            ]);
        }

        // Tự động xác nhận thanh toán
        $payment->update([
            'trang_thai_thanh_toan' => 'da_thanh_toan',
            'ngay_thanh_toan' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thanh toán thành công',
            'payment_id' => $payment->id
        ]);
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

