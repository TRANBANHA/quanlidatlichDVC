<?php

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\FileController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DeathController;
use App\Http\Controllers\Admin\CitizenController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\StaffRatingReportController;
use App\Http\Controllers\Admin\TempResidenceController;
use App\Http\Controllers\Admin\ServiceScheduleController;
use App\Http\Controllers\Admin\BirthRegistrationController;
use App\Http\Controllers\Admin\ServiceAssignmentController;
use App\Http\Controllers\Admin\RoomChatAdminController;
use App\Http\Controllers\Admin\AbsenceRegistrationController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DonViController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServicePhuongController;
use App\Http\Controllers\Admin\AdminPhuongController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\SettingController;
// use App\Http\Controllers\Admin\NotificationAdminController; // File không tồn tại
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\CanBoNghiController;

// Route với middleware 'auth:admin'
Route::middleware(['auth:admin', 'role.access'])->group(function () {
    // Dashboard routes
    Route::get('/', [DashboardController::class, 'index'])->name('admin.index');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('admin.dashboard.chart-data');
    Route::get('/dashboard/export-excel', [DashboardController::class, 'exportExcel'])->name('admin.dashboard.export-excel');
    Route::get('/dashboard/export-pdf', [DashboardController::class, 'exportPDF'])->name('admin.dashboard.export-pdf');
    
    // Staff Rating Reports
    Route::get('/reports/staff-rating', [StaffRatingReportController::class, 'index'])->name('admin.reports.staff-rating');
    Route::get('/reports/staff-rating/chart', [StaffRatingReportController::class, 'getChartData'])->name('admin.reports.staff-rating.chart');
    Route::get('/reports/staff-rating/{staffId}', [StaffRatingReportController::class, 'show'])->name('admin.reports.staff-rating.show');
    
    Route::resource('/quantri', AdminController::class);
    Route::post('/quantri/import', [AdminController::class, 'import'])->name('quantri.import');
    Route::get('/quantri/download-template', [AdminController::class, 'downloadTemplate'])->name('quantri.download-template');
    Route::resource('/don-vi', DonViController::class);
    Route::resource('/users', UserController::class);
    Route::resource('/services', ServiceController::class);
    // Service Fields Management
    Route::get('/services/{serviceId}/fields/create', [\App\Http\Controllers\Admin\ServiceFieldController::class, 'create'])->name('services.fields.create');
    Route::post('/services/{serviceId}/fields', [\App\Http\Controllers\Admin\ServiceFieldController::class, 'store'])->name('services.fields.store');
    Route::get('/services/{serviceId}/fields/{fieldId}/edit', [\App\Http\Controllers\Admin\ServiceFieldController::class, 'edit'])->name('services.fields.edit');
    Route::put('/services/{serviceId}/fields/{fieldId}', [\App\Http\Controllers\Admin\ServiceFieldController::class, 'update'])->name('services.fields.update');
    Route::delete('/services/{serviceId}/fields/{fieldId}', [\App\Http\Controllers\Admin\ServiceFieldController::class, 'destroy'])->name('services.fields.destroy');
    Route::resource('/services-schedules', ServiceScheduleController::class);
    Route::resource('/service-assignments', ServiceAssignmentController::class);
    
    // Posts management (Admin)
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class)->except(['show'])->names([
        'index' => 'admin.posts.index',
        'create' => 'admin.posts.create',
        'store' => 'admin.posts.store',
        'edit' => 'admin.posts.edit',
        'update' => 'admin.posts.update',
        'destroy' => 'admin.posts.destroy',
    ]);
    
    // About management
    Route::get('/about', [\App\Http\Controllers\Admin\AboutController::class, 'index'])->name('admin.about.index');
    Route::put('/about', [\App\Http\Controllers\Admin\AboutController::class, 'update'])->name('admin.about.update');
    
    // Contacts management
    Route::get('/contacts', [\App\Http\Controllers\Admin\ContactController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{id}', [\App\Http\Controllers\Admin\ContactController::class, 'show'])->name('contacts.show');
    Route::post('/contacts/{id}/reply', [\App\Http\Controllers\Admin\ContactController::class, 'reply'])->name('contacts.reply');
    Route::delete('/contacts/{id}', [\App\Http\Controllers\Admin\ContactController::class, 'destroy'])->name('contacts.destroy');
    Route::get('service-assignment', [ServiceAssignmentController::class, 'index'])->name('service-assignment.index');
    Route::post('service-assignment', [ServiceAssignmentController::class, 'store'])->name('service-assignment.store');

    // Room chat (admin)
    Route::get('room-chats', [RoomChatAdminController::class, 'index'])->name('admin.room-chats.index');
    Route::get('room-chats/{room}', [RoomChatAdminController::class, 'show'])->name('admin.room-chats.show');
    Route::post('room-chats/{room}/claim', [RoomChatAdminController::class, 'claim'])->name('admin.room-chats.claim');
    Route::post('room-chats/random-claim', [RoomChatAdminController::class, 'randomClaim'])->name('admin.room-chats.random-claim');
    Route::get('room-chats/{room}/messages', [RoomChatAdminController::class, 'messages'])->name('admin.room-chats.messages');
    Route::post('room-chats/{room}/messages', [RoomChatAdminController::class, 'send'])->name('admin.room-chats.send');


    Route::resource('/file', FileController::class);
    Route::post('/send-info', [FileController::class, 'sendInfo'])->name('file.sendInfo');
    Route::post('/send-info/success/{id}', [FileController::class, 'sendInfoSuccess'])->name('file.sendInfo.success');
    
    // Quản lý hồ sơ (Cán bộ xử lý)
    Route::prefix('ho-so')->name('admin.ho-so.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Admin\HoSoController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\HoSoController::class, 'show'])->name('show');
        Route::post('/{id}/update-status', [\App\Http\Controllers\Admin\HoSoController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/assign', [\App\Http\Controllers\Admin\HoSoController::class, 'assign'])->name('assign');
        Route::post('/{id}/cancel', [\App\Http\Controllers\Admin\HoSoController::class, 'cancel'])->name('cancel');
    });
    
    // Quản lý tài khoản
    Route::prefix('account')->group(function() {
        Route::get('/profile', [AccountController::class, 'profile'])->name('admin.account.profile');
        Route::put('/profile', [AccountController::class, 'updateProfile'])->name('admin.account.update-profile');
        Route::get('/change-password', [AccountController::class, 'changePassword'])->name('admin.account.change-password');
        Route::put('/change-password', [AccountController::class, 'updatePassword'])->name('admin.account.update-password');
    });

    // Báo cáo tổng hợp (Admin tổng)
    Route::prefix('reports')->name('reports.')->group(function() {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/phuong/{donViId}', [ReportController::class, 'showPhuongDetail'])->name('phuong.detail');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [ReportController::class, 'exportPDF'])->name('export-pdf');
    });

    // Cấu hình website (Admin tổng)
    Route::prefix('settings')->name('admin.settings.')->group(function() {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/', [SettingController::class, 'update'])->name('update');
    });

    // Quản lý thông báo (Admin tổng) - Tạm thời comment vì controller chưa tồn tại
    // Route::prefix('notifications')->name('admin.notifications.')->group(function() {
    //     Route::get('/', [NotificationAdminController::class, 'index'])->name('index');
    //     Route::get('/create', [NotificationAdminController::class, 'create'])->name('create');
    //     Route::post('/', [NotificationAdminController::class, 'store'])->name('store');
    //     Route::get('/{id}/edit', [NotificationAdminController::class, 'edit'])->name('edit');
    //     Route::put('/{id}', [NotificationAdminController::class, 'update'])->name('update');
    //     Route::delete('/{id}', [NotificationAdminController::class, 'destroy'])->name('destroy');
    // });

    // Quản lý dịch vụ phường (Admin phường)
    Route::prefix('service-phuong')->name('service-phuong.')->group(function() {
        Route::get('/', [ServicePhuongController::class, 'index'])->name('index');
        Route::get('/create', [ServicePhuongController::class, 'create'])->name('create');
        Route::post('/', [ServicePhuongController::class, 'store'])->name('store');
        Route::get('/{serviceId}/edit', [ServicePhuongController::class, 'edit'])->name('edit');
        Route::put('/{serviceId}', [ServicePhuongController::class, 'updateService'])->name('update-service');
        Route::get('/schedule', [ServicePhuongController::class, 'schedule'])->name('schedule');
        Route::post('/schedule', [ServicePhuongController::class, 'storeSchedule'])->name('schedule.store');
        Route::post('/copy/{serviceId}', [ServicePhuongController::class, 'copyFromTotal'])->name('copy');
        Route::put('/{id}', [ServicePhuongController::class, 'update'])->name('update');
        Route::delete('/{id}', [ServicePhuongController::class, 'destroy'])->name('destroy');
        
        // Form Fields Management
        Route::get('/{serviceId}/fields/create', [ServicePhuongController::class, 'createField'])->name('fields.create');
        Route::post('/{serviceId}/fields', [ServicePhuongController::class, 'storeField'])->name('fields.store');
        Route::get('/{serviceId}/fields/{fieldId}/edit', [ServicePhuongController::class, 'editField'])->name('fields.edit');
        Route::put('/{serviceId}/fields/{fieldId}', [ServicePhuongController::class, 'updateField'])->name('fields.update');
        Route::delete('/{serviceId}/fields/{fieldId}', [ServicePhuongController::class, 'destroyField'])->name('fields.destroy');
    });

    // Quản lý Admin phường
    Route::prefix('admin-phuong')->name('admin-phuong.')->group(function() {
        // Quản lý cán bộ
        Route::get('/staff', [AdminPhuongController::class, 'staffIndex'])->name('staff.index');
        Route::get('/staff/performance', [AdminPhuongController::class, 'staffPerformance'])->name('staff.performance');
        
        // Quản lý người dân
        Route::get('/users', [AdminPhuongController::class, 'usersIndex'])->name('users.index');
        Route::get('/users/{userId}/history', [AdminPhuongController::class, 'userHistory'])->name('users.history');
    });

    // Thống kê (Admin phường)
    Route::prefix('statistics')->name('statistics.')->group(function() {
        Route::get('/', [StatisticsController::class, 'index'])->name('index');
    });

    // Đánh giá cán bộ (Cán bộ xem đánh giá của mình)
    Route::prefix('staff-ratings')->name('staff-ratings.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Admin\StaffRatingController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\StaffRatingController::class, 'show'])->name('show');
    });

    // Quản lý cán bộ báo nghỉ
    Route::prefix('can-bo-nghi')->name('admin.can-bo-nghi.')->group(function() {
        Route::get('/', [CanBoNghiController::class, 'index'])->name('index');
        Route::get('/create', [CanBoNghiController::class, 'create'])->name('create');
        Route::post('/', [CanBoNghiController::class, 'store'])->name('store');
        Route::post('/admin-bao-nghi', [CanBoNghiController::class, 'storeByAdmin'])->name('store-by-admin');
        Route::post('/{id}/duyet', [CanBoNghiController::class, 'duyet'])->name('duyet');
        Route::post('/{id}/tu-choi', [CanBoNghiController::class, 'tuChoi'])->name('tu-choi');
        Route::delete('/{id}', [CanBoNghiController::class, 'destroy'])->name('destroy');
    });

    // Quản lý thanh toán (Admin tổng và Admin phường)
    Route::prefix('payments')->name('admin.payments.')->group(function() {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [PaymentController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [PaymentController::class, 'reject'])->name('reject');
    });

    // Cấu hình QR code (Admin phường)
    Route::prefix('qr-code')->name('admin.qr-code.')->group(function() {
        Route::get('/', [QrCodeController::class, 'index'])->name('index');
        Route::put('/', [QrCodeController::class, 'update'])->name('update');
    });

});

// Login routes (should be accessible without authentication)
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [AuthController::class, "login"])->name("admin.login");
    Route::post('/login', [AuthController::class, "postLogin"])->name("admin.post.login");
});

Route::get('/logout', [AuthController::class, "logout"])->name("admin.logout");
Route::post('/comments/update-status', [CommentController::class, 'updateStatus']);

Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('admin.password.email');
Route::post('/admin/password/update', [AuthController::class, 'resetPassword'])->name('admin.password.update');

Route::get('/home_count', [HomeController::class, 'countRegistrationsPerMonth'])->name("home_count");
Route::get('/update-publish-status', [AdminController::class, 'updatePublishStatus'])->name('admin.update.status');
