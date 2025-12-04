
<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// --- Đăng nhập / Đăng xuất ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Các route yêu cầu đăng nhập ---
Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/borrow', [BorrowController::class, 'create'])->name('borrow.create');
    Route::post('/borrow', [BorrowController::class, 'store'])->name('borrow.store');

    Route::get('/danh-sach-muon', [BorrowController::class, 'index'])->name('borrow.index');


    // 1. Trang danh sách các món đang mượn để trả
Route::get('/tra-thiet-bi', [BorrowController::class, 'returnIndex'])->name('return.index');

// 2. Hành động xử lý trả (đã làm ở bước trước, chỉ cần đảm bảo route name khớp)
Route::post('/tra-thiet-bi/{id}', [BorrowController::class, 'returnDevice'])->name('return.action');
});
