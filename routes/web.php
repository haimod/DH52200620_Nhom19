<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeviceController; // Nhớ import ở đầu file
use App\Http\Controllers\Admin\BorrowController as AdminBorrowController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SupportController as AdminSupportController; // Import Controller Hỗ trợ của Admin
use App\Http\Controllers\NotificationController; // <--- 1. IMPORT CONTROLLER NÀY

// --- Đăng nhập / Đăng xuất ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- 2. ROUTE XỬ LÝ THÔNG BÁO (MỚI THÊM) ---
    Route::get('/notifications/mark-read/{id}', [NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::get('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    // ------------------------------------------


// --- Các route yêu cầu đăng nhập ---
Route::middleware(['auth','check.status'])->group(function () {
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

// --- 2. Khu vực dành riêng cho ADMIN (MỚI THÊM) ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Trang Dashboard: 
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        // Các route quản lý khác sẽ viết tiếp ở đây...

         // --- QUẢN LÝ THIẾT BỊ (MỚI) ---
    Route::get('/thiet-bi', [DeviceController::class, 'index'])->name('devices.index'); // Xem danh sách
    Route::post('/thiet-bi/them', [DeviceController::class, 'store'])->name('devices.store'); // Thêm mới
    Route::delete('/thiet-bi/xoa/{id}', [DeviceController::class, 'destroy'])->name('devices.destroy');
    // ROUTE UPDATE (QUAN TRỌNG)
    Route::put('/thiet-bi/cap-nhat/{id}', [DeviceController::class, 'update'])->name('devices.update');





    
    // ====================================================
    // QUẢN LÝ MƯỢN TRẢ (Thêm mới đoạn này)
    // ====================================================
    
    // 1. Trang danh sách (Hiển thị giao diện Tabs)
        // 1. Trang danh sách
   // Sử dụng AdminBorrowController thay vì BorrowController
    Route::get('/borrow', [AdminBorrowController::class, 'index'])->name('borrow.index');
    Route::post('/borrow/approve/{id}', [AdminBorrowController::class, 'approve'])->name('borrow.approve');
    Route::post('/borrow/reject/{id}', [AdminBorrowController::class, 'reject'])->name('borrow.reject');
    Route::post('/borrow/return/{id}', [AdminBorrowController::class, 'returnDevice'])->name('borrow.return');

    // Route Quản lý nhân sự
    Route::resource('users', UserController::class);

    
    // [BƯỚC 3] Route chức năng Khóa/Mở khóa tài khoản (Toggle Status)
    Route::post('users/{id}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
        
    
    // --- TRUNG TÂM HỖ TRỢ (ADMIN) ---
    // [FIX LỖI]: Trỏ đúng về AdminSupportController thay vì AdminBorrowController
    // Đây là phần bạn đang cần: Giao diện Admin để xem và trả lời ticket
    Route::get('/support', [AdminSupportController::class, 'index'])->name('support.index');     // Danh sách yêu cầu
    Route::get('/support/{id}', [AdminSupportController::class, 'show'])->name('support.show');  // Xem chi tiết & Chat
    Route::post('/support/{id}/reply', [AdminSupportController::class, 'reply'])->name('support.reply'); // Gửi phản hồi
    Route::post('/support/{id}/close', [AdminSupportController::class, 'close'])->name('support.close'); // Đóng yêu cầu
    Route::delete('/support/{id}', [AdminSupportController::class, 'destroy'])->name('support.destroy'); // Xóa yêu cầu

// --- GỬI THÔNG BÁO TỪ ADMIN ---
Route::post(
    '/notifications/send', [DashboardController::class, 'sendNotification'])->name('notify.send');
});