<?php

use App\Events\RealTimeMessage;
use App\Http\Controllers\RegisterServiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\website\AIController;
use App\Http\Controllers\website\RoomChatController;
use App\Http\Controllers\website\AuthController;
use App\Http\Controllers\website\HomeController;
use App\Http\Controllers\website\InfoController;
use App\Http\Controllers\website\DeathController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\website\ContactController;
use App\Http\Controllers\Auth\ResetPasswordController;

//login - register - verify
Route::get('/login', [AuthController::class, 'index'])->name('login.form');
Route::get('/registers', [AuthController::class, 'register'])->name('register');
Route::post('/registers/store', [AuthController::class, 'postRegister'])->name('registers.store');
Route::get('/', [HomeController::class, "index"])->name('index');
Route::post('/login', [AuthController::class, "login"])->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/verify-code', [AuthController::class, 'showVerifyForm'])->name('web.verifyCode');
Route::post('/verify-code', [AuthController::class, 'verifyCode'])->name('web.verifyCode.post');

////get service

Route::get('/get-dates-by-service', [RegisterServiceController::class, 'getDatesByService'])->name('get.dates.by.service');
Route::get('/get-services-by-date', [RegisterServiceController::class, 'getServicesByDate'])->name('get.services.by.date');
/////////

Route::get('/verification/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');

Route::get('/register-services', [RegisterServiceController::class, 'index'])->name('register-services.index');
Route::post('/register-services/store', [RegisterServiceController::class, 'store'])->name('register-services.store');

// Rating routes - Đã chuyển sang group rating ở dưới


Route::get('/death', [DeathController::class, "index"])->name('death');
Route::post('/death/store', [DeathController::class, "store"])->name('website.death.store');
Route::get('/death/{id}', [DeathController::class, "edit"])->name('death.edit');
Route::put('/death/update/{id}', [DeathController::class, "update"])->name('website.death.update');

Route::post('/comment', [HomeController::class, "postComment"])->name('website.postComment');

Route::get("/news/{slug}", [HomeController::class, "postsDetail"])->name("post.detail");

// Route gửi email yêu cầu đặt lại mật khẩu
Route::post('password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

// Route để xử lý yêu cầu reset mật khẩu
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('info', [InfoController::class, 'index'])->name('info.index');
Route::get('tra-cuu-ho-so', [InfoController::class, 'lookup'])->name('ho-so.lookup');
// web.php
Route::post('/profile/change-password', [InfoController::class, 'changePassword'])->name('profile.change-password');
Route::put('/profile/update/{id}', [InfoController::class, 'update'])->name('profile.update');
Route::get('/ho-so/{hoSo}/cancel', [InfoController::class, 'showCancelForm'])->name('website.ho-so.cancel.form')->where('hoSo', '[0-9]+');
Route::post('/ho-so/{hoSo}/cancel', [InfoController::class, 'cancel'])->name('website.ho-so.cancel')->where('hoSo', '[0-9]+');
Route::get('/ho-so/{id}/edit', [InfoController::class, 'editHoSo'])->name('ho-so.edit');
Route::put('/ho-so/{id}', [InfoController::class, 'updateHoSo'])->name('ho-so.update');
// Posts routes
Route::get('/posts', [\App\Http\Controllers\website\PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{slug}', [\App\Http\Controllers\website\PostController::class, 'show'])->name('posts.show');

// About route
Route::get('/gioi-thieu', [\App\Http\Controllers\website\AboutController::class, 'index'])->name('about.index');

// Contact routes
Route::get('/contact', [\App\Http\Controllers\website\ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [\App\Http\Controllers\website\ContactController::class, 'store'])->name('contact.store');
Route::post('/chat', [HomeController::class, 'chat']);


Route::post('/register/updateStatus/{slug}/{id}/{action}', [InfoController::class, 'updateStatusPayment'])->name('info.update.status');


Route::post('/generate-text', [AIController::class, 'generateText'])->name('generate.text');

// Booking routes (Đặt lịch dịch vụ)
Route::prefix('dat-lich')->name('booking.')->group(function () {
    Route::get('chon-phuong', [\App\Http\Controllers\Website\BookingController::class, 'selectPhuong'])->name('select-phuong');
    Route::post('chon-dich-vu', [\App\Http\Controllers\Website\BookingController::class, 'selectService'])->name('select-service');
    Route::match(['get', 'post'], 'chon-ngay', [\App\Http\Controllers\Website\BookingController::class, 'selectDate'])->name('select-date');
    Route::match(['get', 'post'], 'upload-ho-so', [\App\Http\Controllers\Website\BookingController::class, 'uploadForm'])->middleware('auth:web')->name('upload-form');
    Route::post('xac-nhan', [\App\Http\Controllers\Website\BookingController::class, 'store'])->middleware('auth:web')->name('store');
    Route::get('thanh-cong/{hoSoId}', [\App\Http\Controllers\Website\BookingController::class, 'success'])->middleware('auth:web')->name('success');
});

// Room chat (user)
// QUAN TRỌNG: Đặt available-officers TRƯỚC {room} để tránh route conflict
Route::get('room-chats/available-officers', [RoomChatController::class, 'getAvailableOfficers']);

Route::prefix('room-chats')->group(function () {
    Route::post('start', [RoomChatController::class, 'start']);
    Route::get('{room}/messages', [RoomChatController::class, 'messages']);
    Route::post('{room}/messages', [RoomChatController::class, 'send']);
    Route::post('{room}/leave', [RoomChatController::class, 'leave']);
});

// Rasa Chatbot
Route::prefix('rasa-chat')->middleware('auth:web')->group(function () {
    Route::post('send', [\App\Http\Controllers\website\RasaChatController::class, 'sendMessage'])->name('rasa-chat.send');
    Route::get('status', [\App\Http\Controllers\website\RasaChatController::class, 'checkStatus'])->name('rasa-chat.status');
});

// My Bookings (Lịch hẹn của tôi)
Route::prefix('my-bookings')->middleware('auth:web')->name('my-bookings.')->group(function () {
    Route::get('/', [\App\Http\Controllers\website\MyBookingsController::class, 'index'])->name('index');
    Route::get('/{id}', [\App\Http\Controllers\website\MyBookingsController::class, 'show'])->name('show');
    Route::post('/{id}/cancel', [\App\Http\Controllers\website\MyBookingsController::class, 'cancel'])->name('cancel');
});

// Tracking (Tra cứu hồ sơ)
Route::prefix('tra-cuu')->name('tracking.')->group(function () {
    Route::get('/', [\App\Http\Controllers\website\TrackingController::class, 'index'])->name('index');
    Route::post('/search', [\App\Http\Controllers\website\TrackingController::class, 'search'])->name('search');
    Route::get('/{maHoSo}', [\App\Http\Controllers\website\TrackingController::class, 'show'])->name('show');
});

// Notifications (Thông báo)
Route::prefix('notifications')->middleware('auth:web')->name('notifications.')->group(function () {
    Route::get('/', [\App\Http\Controllers\website\NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/read', [\App\Http\Controllers\website\NotificationController::class, 'markAsRead'])->name('read');
    Route::post('/mark-all-read', [\App\Http\Controllers\website\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/{id}', [\App\Http\Controllers\website\NotificationController::class, 'destroy'])->name('destroy');
});

// Rating (Đánh giá)
Route::prefix('rating')->middleware('auth:web')->name('rating.')->group(function () {
    Route::get('/create/{hoSoId}', [\App\Http\Controllers\website\RatingController::class, 'create'])->name('create');
    Route::post('/store/{hoSoId}', [\App\Http\Controllers\website\RatingController::class, 'store'])->name('store');
    Route::get('/edit/{ratingId}', [\App\Http\Controllers\website\RatingController::class, 'edit'])->name('edit');
    Route::put('/update/{ratingId}', [\App\Http\Controllers\website\RatingController::class, 'update'])->name('update');
});

// Payment (Thanh toán)
Route::prefix('payment')->name('payment.')->group(function () {
    Route::middleware('auth:web')->group(function () {
        Route::get('/', [\App\Http\Controllers\website\PaymentController::class, 'index'])->name('index');
        Route::get('/create/{hoSoId}', [\App\Http\Controllers\website\PaymentController::class, 'create'])->name('create');
        Route::post('/vnpay/create/{hoSoId}', [\App\Http\Controllers\website\PaymentController::class, 'createVNPayPayment'])->name('vnpay.create');
        Route::post('/upload-proof/{paymentId}', [\App\Http\Controllers\website\PaymentController::class, 'uploadProof'])->name('upload-proof');
        Route::get('/{id}', [\App\Http\Controllers\website\PaymentController::class, 'show'])->name('show');
    });
    // VNPay callback và return
    Route::get('/vnpay/return', [\App\Http\Controllers\website\PaymentController::class, 'vnpayReturn'])->name('vnpay.return');
    Route::post('/vnpay/callback', [\App\Http\Controllers\website\PaymentController::class, 'vnpayCallback'])->name('vnpay.callback');
});