@extends('website.components.layout');
@section('title')
    Đăng Ký Dịch Vụ
@endsection
@section('content')
    <section class="register-service-hero py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <!-- Header -->
                    <div class="text-center mb-5 animate-fade-in">
                        <div class="header-icon mb-3">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h2 class="fw-bold mb-3 text-gradient">Đăng Ký Dịch Vụ</h2>
                        <p class="text-muted lead">Vui lòng điền đầy đủ thông tin để đăng ký dịch vụ của bạn</p>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('register-services.store') }}" method="POST" enctype="multipart/form-data" id="registerForm">
                        @csrf
                        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                            <div class="card-header-custom">
                                <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Thông tin đăng ký</h5>
                            </div>
                            <div class="card-body p-4 p-md-5">
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <div>{{ session('success') }}</div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Vui lòng kiểm tra lại thông tin:</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <!-- Chọn phường -->
                                <div class="form-group-custom mb-4">
                                    <label for="don_vi_id" class="form-label-custom">
                                        <i class="fas fa-building me-2"></i>Chọn phường/đơn vị <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control-custom" name="don_vi_id" id="don_vi_id" required>
                                        <option value="" disabled selected>-- Vui lòng chọn phường/đơn vị --</option>
                                        @foreach ($listDonvi as $donvi)
                                            <option value="{{ $donvi->id }}">{{ $donvi->ten_don_vi }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Chọn dịch vụ -->
                                <div class="form-group-custom mb-4">
                                    <label for="service" class="form-label-custom">
                                        <i class="fas fa-concierge-bell me-2"></i>Chọn dịch vụ <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control-custom" name="service" id="service" required disabled>
                                        <option value="" disabled selected>-- Vui lòng chọn phường trước --</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->ten_dich_vu }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text-custom">Vui lòng chọn phường/đơn vị trước để chọn dịch vụ</small>
                                </div>

                                <!-- Chọn ngày hẹn -->
                                <div class="form-group-custom mb-4">
                                    <label class="form-label-custom mb-3">
                                        <i class="fas fa-calendar-alt me-2"></i>Chọn ngày hẹn <span class="text-danger">*</span>
                                    </label>
                                    <div id="available-dates" class="dates-container">
                                        <div class="empty-state">
                                            <i class="fas fa-info-circle"></i>
                                            <p class="mb-0">Vui lòng chọn dịch vụ để xem các ngày khả dụng</p>
                                        </div>
                                    </div>
                                    <input type="hidden" id="date" name="date" required>
                                </div>

                                <!-- Chọn giờ hẹn -->
                                <div class="form-group-custom mb-4">
                                    <label for="time" class="form-label-custom mb-3">
                                        <i class="fas fa-clock me-2"></i>Chọn giờ hẹn <span class="text-danger">*</span>
                                    </label>
                                    <div id="available-times" class="times-container">
                                        <div class="empty-state">
                                            <i class="fas fa-info-circle"></i>
                                            <p class="mb-0">Vui lòng chọn ngày để xem các giờ khả dụng</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload file -->
                                <div class="form-group-custom mb-4">
                                    <label class="form-label-custom mb-3">
                                        <i class="fas fa-file-upload me-2"></i>Tải lên hồ sơ <span class="text-danger">*</span>
                                    </label>
                                    <div class="file-upload-wrapper">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <a href="" class="btn btn-outline-primary btn-download" id="path_file_mau" download>
                                                <i class="fas fa-download me-2"></i>Tải file mẫu
                                            </a>
                                            <small class="text-muted">Định dạng: PDF, DOC, DOCX, XLSX (Tối đa 5MB)</small>
                                        </div>
                                        <div class="file-input-wrapper">
                                            <input type="file" id="file_path" name="file_path" class="form-control-custom file-input" required>
                                            <label for="file_path" class="file-input-label">
                                                <i class="fas fa-cloud-upload-alt me-2"></i>
                                                <span>Chọn file hoặc kéo thả vào đây</span>
                                            </label>
                                        </div>
                                        <div id="file-name" class="file-name-display mt-2"></div>
                                    </div>
                                </div>

                                <!-- Ghi chú -->
                                <div class="form-group-custom mb-4">
                                    <label for="ghi_chu" class="form-label-custom">
                                        <i class="fas fa-sticky-note me-2"></i>Ghi chú
                                    </label>
                                    <textarea name="ghi_chu" id="ghi_chu" rows="4" class="form-control-custom" placeholder="Nhập ghi chú (nếu có)..."></textarea>
                                </div>

                                <!-- Submit Button -->
                                <div class="text-center mt-5">
                                    <button type="submit" class="btn-submit-custom">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        <span>Đăng ký dịch vụ</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Custom CSS -->
    <style>
        .register-service-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 60px 0;
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card {
            background: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            border: none;
        }

        .card-header-custom h5 {
            color: white;
            font-weight: 600;
        }

        .form-group-custom {
            position: relative;
        }

        .form-label-custom {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            display: block;
            font-size: 1rem;
        }

        .form-control-custom,
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .form-control-custom:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }

        .form-control-custom:disabled {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }

        .form-text-custom {
            color: #6c757d;
            font-size: 0.875rem;
            margin-top: 5px;
            display: block;
        }

        .dates-container,
        .times-container {
            min-height: 80px;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px dashed #dee2e6;
        }

        .empty-state i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
            color: #adb5bd;
        }

        .date-btn {
            transition: all 0.3s ease;
            padding: 15px 20px;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            background: white;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
        }

        .date-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(102, 126, 234, 0.2);
            border-color: #667eea;
        }

        .date-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
            box-shadow: 0 8px 15px rgba(102, 126, 234, 0.3);
        }

        .time-slot {
            display: block;
            padding: 12px 20px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            background: white;
            text-align: center;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            margin: 0;
        }

        .time-slot:hover:not(.disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.2);
            border-color: #667eea;
        }

        .time-slot.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f5f5f5;
        }

        .time-slot.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
        }

        .file-upload-wrapper {
            position: relative;
        }

        .file-input-wrapper {
            position: relative;
        }

        .file-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
            z-index: 2;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            border: 2px dashed #667eea;
            border-radius: 12px;
            background: #f8f9ff;
            color: #667eea;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            background: #f0f2ff;
            border-color: #764ba2;
        }

        .file-name-display {
            padding: 10px 15px;
            background: #e7f3ff;
            border-radius: 8px;
            color: #0066cc;
            font-weight: 500;
            display: none;
        }

        .file-name-display.show {
            display: block;
        }

        .btn-download {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }

        .btn-submit-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 50px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .btn-submit-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
        }

        .btn-submit-custom:active {
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .register-service-hero {
                padding: 40px 0;
            }

            .card-body {
                padding: 20px !important;
            }

            .header-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }

            .btn-submit-custom {
                width: 100%;
                padding: 15px 30px;
            }
        }
    </style>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let gioHenTheoDonVi = {}; // Lưu danh sách giờ hẹn theo từng đơn vị
    let selectedServiceId = null; // Lưu dịch vụ đã chọn
    let selectedDonViId = null; // Lưu phường đã chọn
    let availableDates = []; // Lưu danh sách ngày khả dụng

    $(document).ready(function() {
        // Hiển thị empty state cho times
        $('#available-times').html('<div class="empty-state"><i class="fas fa-info-circle"></i><p class="mb-0">Vui lòng chọn ngày để xem các giờ khả dụng</p></div>');

        // Hiển thị tên file khi chọn file
        $('#file_path').on('change', function() {
            const fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $('#file-name').html('<i class="fas fa-file me-2"></i>' + fileName).addClass('show');
            } else {
                $('#file-name').removeClass('show');
            }
        });

        // Khi chọn phường
        $('#don_vi_id').on('change', function() {
            selectedDonViId = $(this).val();

            // Reset các lựa chọn phụ thuộc
            selectedServiceId = null;
            $('#service').prop('disabled', !selectedDonViId).val('');
            $('#date').val('');
            $('#available-dates').html('<div class="empty-state"><i class="fas fa-info-circle"></i><p class="mb-0">Vui lòng chọn dịch vụ để xem các ngày khả dụng</p></div>');
            $('#available-times').html('<div class="empty-state"><i class="fas fa-info-circle"></i><p class="mb-0">Vui lòng chọn ngày để xem các giờ khả dụng</p></div>');
        });
        
        // Khi chọn dịch vụ
        $('#service').on('change', function() {
            selectedServiceId = $(this).val();
            $('#date').val('');
            $('#available-times').html('<div class="empty-state"><i class="fas fa-info-circle"></i><p class="mb-0">Vui lòng chọn ngày để xem các giờ khả dụng</p></div>');
            
            if (!selectedServiceId || !selectedDonViId) {
                // Nếu chưa chọn dịch vụ, clear các field
                $('#available-dates').html('<div class="empty-state"><i class="fas fa-info-circle"></i><p class="mb-0">Vui lòng chọn dịch vụ để xem các ngày khả dụng</p></div>');
                $('#date').val('');
                $('#available-times').html('<div class="empty-state"><i class="fas fa-info-circle"></i><p class="mb-0">Vui lòng chọn ngày để xem các giờ khả dụng</p></div>');
                return;
            }
            
            // Load danh sách ngày khả dụng
            $.ajax({
                url: "/get-dates-by-service",
                type: "GET",
                data: { service_id: selectedServiceId },
                beforeSend: function() {
                    $('#available-dates').html('<div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p class="mb-0">Đang tải...</p></div>');
                },
                success: function(response) {
                    console.log('Dates response:', response);
                    
                    availableDates = response.dates || [];
                    
                    // Hiển thị danh sách ngày dưới dạng buttons
                    if (availableDates.length > 0) {
                        let datesHtml = '<div class="row g-3">';
                        availableDates.forEach(function(dateInfo) {
                            datesHtml += `
                                <div class="col-md-4 col-sm-6">
                                    <button type="button" class="btn date-btn w-100" data-date="${dateInfo.date}">
                                        <strong>${dateInfo.day_name}</strong><br>
                                        <small>${dateInfo.formatted_date}</small>
                                    </button>
                                </div>
                            `;
                        });
                        datesHtml += '</div>';
                        $('#available-dates').html(datesHtml);
                    } else {
                        $('#available-dates').html('<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p class="mb-0 text-danger">Không có ngày khả dụng cho dịch vụ này</p></div>');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading dates:', xhr.responseText);
                    $('#available-dates').html('<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p class="mb-0 text-danger">Không thể tải danh sách ngày. Vui lòng thử lại.</p></div>');
                }
            });
        });

        // Khi click vào button ngày
        $(document).on('click', '.date-btn', function() {
            const selectedDate = $(this).data('date');
            // Active button được chọn
            $('.date-btn').removeClass('active');
            $(this).addClass('active');
            
            // Set giá trị cho input hidden
            $('#date').val(selectedDate);
            
            // Load giờ hẹn nếu đã chọn phường
            if (selectedDonViId) {
                loadTimeSlots(selectedDate, selectedDonViId);
            } else {
                $('#available-times').html('<div class="empty-state"><i class="fas fa-info-circle"></i><p class="mb-0">Vui lòng chọn phường để xem các giờ khả dụng</p></div>');
            }
        });

        // Khi chọn phường, load giờ hẹn
        $('select[name="don_vi_id"]').on('change', function() {
            const selectedDate = $('#date').val();

            if (!selectedDonViId || !selectedDate) {
                $('#available-times').html('<div class="empty-state"><i class="fas fa-info-circle"></i><p class="mb-0">Vui lòng chọn ngày và phường để xem các giờ khả dụng</p></div>');
                return;
            }
            
            loadTimeSlots(selectedDate, selectedDonViId);
        });
        
        // Function để load time slots
        function loadTimeSlots(date, donViId) {
            console.log('Loading time slots for date:', date, 'donViId:', donViId);
            
            $.ajax({
                url: "/get-services-by-date",
                type: "GET",
                data: { date: date },
                beforeSend: function() {
                    $('#available-times').html('<div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p class="mb-0">Đang tải...</p></div>');
                },
                success: function(response) {
                    console.log('Time slots response:', response);
                    
                    // Lấy danh sách giờ đã đăng ký
                    gioHenTheoDonVi = response.gioHenList || {};
                    let gioHenList = gioHenTheoDonVi[donViId] || [];
                    console.log('Giờ đã đăng ký cho phường:', gioHenList);
                    
                    // Hiển thị tất cả giờ hành chính (8h - 17h, loại bỏ 12h và 13h)
                    let timesHtml = '<div class="row g-3">';
                    for (let i = 8; i <= 17; i++) {
                        if (i === 12 || i === 13) continue; // Loại bỏ 12h và 13h
                        
                        const timeValue = i + ':00';
                        const timeLabel = String(i).padStart(2, '0') + ':00';
                        const isDisabled = gioHenList.includes(timeValue);
                        const disabledClass = isDisabled ? 'disabled' : '';
                        
                        timesHtml += `
                            <div class="col-md-3 col-sm-4 col-6">
                                <label class="time-slot ${disabledClass}" style="margin: 0;">
                                    <input type="radio" name="time" value="${timeValue}" ${isDisabled ? 'disabled' : ''} style="display: none;">
                                    <span>${timeLabel}</span>
                                </label>
                            </div>
                        `;
                    }
                    timesHtml += '</div>';
                    $('#available-times').html(timesHtml);

                    // Xử lý khi click vào time slot
                    $('.time-slot:not(.disabled)').on('click', function() {
                        $('.time-slot').removeClass('active');
                        $(this).addClass('active');
                        $(this).find('input[type="radio"]').prop('checked', true);
                    });
                },
                error: function(xhr) {
                    console.error('Error loading time slots:', xhr.responseText);
                    // Vẫn hiển thị giờ hành chính ngay cả khi có lỗi
                    let timesHtml = '<div class="row g-3">';
                    for (let i = 8; i <= 17; i++) {
                        if (i === 12 || i === 13) continue;
                        const timeValue = i + ':00';
                        const timeLabel = String(i).padStart(2, '0') + ':00';
                        timesHtml += `
                            <div class="col-md-3 col-sm-4 col-6">
                                <label class="time-slot" style="margin: 0;">
                                    <input type="radio" name="time" value="${timeValue}" style="display: none;">
                                    <span>${timeLabel}</span>
                                </label>
                            </div>
                        `;
                    }
                    timesHtml += '</div>';
                    $('#available-times').html(timesHtml);

                    // Xử lý khi click vào time slot
                    $('.time-slot').on('click', function() {
                        $('.time-slot').removeClass('active');
                        $(this).addClass('active');
                        $(this).find('input[type="radio"]').prop('checked', true);
                    });
                }
            });
        }
    });
</script>
