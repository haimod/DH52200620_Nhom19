<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupportController;

// --- Đăng nhập / Đăng xuất ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Các route yêu cầu đăng nhập ---
Route::middleware(['auth'])->group(function () {
    // 1. Trang chủ
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // 2. Chức năng Mượn & Đặt lịch
    Route::get('/borrow', [BorrowController::class, 'create'])->name('borrow.create');
    Route::post('/borrow', [BorrowController::class, 'store'])->name('borrow.store');

    // 3. API Lấy lịch bận (QUAN TRỌNG: Bạn đang thiếu dòng này)
    Route::get('/api/get-schedule/{id}', [BorrowController::class, 'getSchedule']);

    // 4. Danh sách lịch sử mượn
    Route::get('/danh-sach-muon', [BorrowController::class, 'index'])->name('borrow.index');

    // 5. Trang Trả thiết bị
    Route::get('/tra-thiet-bi', [BorrowController::class, 'returnIndex'])->name('return.index');

    // 6. Xử lý Trả / Hủy / Nhận máy
    Route::post('/return-action/{id}', [BorrowController::class, 'action'])->name('return.action');


    
    // Hiển thị trang cài đặt (gọi hàm index)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    // Xử lý cập nhật thông tin cá nhân (gọi hàm updateProfile)
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.updateProfile');
    // Xử lý đổi mật khẩu (gọi hàm changePassword)
    Route::post('/settings/password', [SettingsController::class, 'changePassword'])->name('settings.changePassword');


        

       Route::get('/ho-tro', [SupportController::class, 'index'])->name('support.index');
Route::post('/ho-tro/gui', [SupportController::class, 'sendRequest'])->name('support.send');
Route::post('/ho-tro/tra-loi', [SupportController::class, 'sendMessage'])->name('support.reply');



});