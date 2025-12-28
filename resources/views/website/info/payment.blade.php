<div class="modal fade" id="qrModal{{ $action }}{{ $slug }}-{{ $key->id }}" tabindex="-1"
    aria-labelledby="qrModalLabel-{{ $key->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel-{{ $key->id }}">Thanh toán bằng QR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- QR Code -->
                <div class="text-center">
                    <p>Giá tiền thanh toán: 50.000 VNĐ</p>
                </div>
                <div class="text-center">
                    <p>Quét mã QR để thanh toán:</p>
                    <img src="{{ asset('storage') }}/{{ get('qr') ?? '' }}" alt="QR Code {{ $key->full_name }}"
                        class="img-fluid mb-3">
                </div>
                <!-- Form gửi ảnh và giải thích -->
                <form action="{{ route('info.update.status', ['slug' => $slug, 'id' => $id, 'action' => $action]) }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="proof_image-{{ $key->id }}" class="form-label">Ảnh chứng cứ thanh toán (VUI
                            LÒNG THANH TOÁN ĐỂ ĐƯỢC DUYỆT BÀI NHÉ)</label>
                        <input type="file" class="form-control" id="proof_image-{{ $key->id }}"
                            name="proof_image" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label for="explanation-{{ $key->id }}" class="form-label">Giải thích (tùy chọn)</label>
                        <textarea class="form-control" id="explanation-{{ $key->id }}" name="explanation" rows="3"
                            placeholder="Thêm giải thích nếu cần"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Gửi chứng cứ</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
