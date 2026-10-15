<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LopHocController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\UserController;


// đăng nhập (chỉ dành cho khách)
Route::middleware('guest')->group(function () {
    Route::get('/dangnhap', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/dangnhap', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
});


// các trang cần đăng nhập — dùng chung cho admin và giảng viên
Route::middleware(['auth', 'tai.khoan.hoat.dong'])->group(function () {
    Route::post('/dangxuat', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // tài khoản của bản thân
    Route::get('/taikhoan', [ProfileController::class, 'edit'])->name('taikhoan.edit');
    Route::put('/taikhoan', [ProfileController::class, 'update'])->name('taikhoan.update');

    // sinh viên
    Route::get('/sinhvien', [SinhVienController::class, 'index'])->name('sinhvien.index');
    Route::get('/sinhvien/them', [SinhVienController::class, 'create'])->name('sinhvien.create');
    Route::post('/sinhvien', [SinhVienController::class, 'store'])->name('sinhvien.store');
    Route::get('/sinhvien/{id}', [SinhVienController::class, 'show'])->name('sinhvien.show');
    Route::get('/sinhvien/{id}/sua', [SinhVienController::class, 'edit'])->name('sinhvien.edit');
    Route::put('/sinhvien/{id}', [SinhVienController::class, 'update'])->name('sinhvien.update');
    Route::delete('/sinhvien/{id}', [SinhVienController::class, 'destroy'])->name('sinhvien.destroy');

    // lớp học
    Route::get('/lophoc', [LopHocController::class, 'index'])->name('lophoc.index');
    Route::get('/lophoc/them', [LopHocController::class, 'create'])->name('lophoc.create');
    Route::post('/lophoc', [LopHocController::class, 'store'])->name('lophoc.store');
    Route::get('/lophoc/{id}', [LopHocController::class, 'show'])->name('lophoc.show');
    Route::get('/lophoc/{id}/sua', [LopHocController::class, 'edit'])->name('lophoc.edit');
    Route::put('/lophoc/{id}', [LopHocController::class, 'update'])->name('lophoc.update');
    Route::delete('/lophoc/{id}', [LopHocController::class, 'destroy'])->name('lophoc.destroy');


    // chỉ admin
    Route::middleware('role:admin')->group(function () {
        // menu
        Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
        Route::get('/menu/them', [MenuController::class, 'create'])->name('menu.create');
        Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
        Route::get('/menu/{id}/sua', [MenuController::class, 'edit'])->name('menu.edit');
        Route::put('/menu/{id}', [MenuController::class, 'update'])->name('menu.update');
        Route::delete('/menu/{id}', [MenuController::class, 'destroy'])->name('menu.destroy');

        // người dùng
        Route::get('/nguoidung', [UserController::class, 'index'])->name('nguoidung.index');
        Route::get('/nguoidung/them', [UserController::class, 'create'])->name('nguoidung.create');
        Route::post('/nguoidung', [UserController::class, 'store'])->name('nguoidung.store');
        Route::get('/nguoidung/{id}/sua', [UserController::class, 'edit'])->name('nguoidung.edit');
        Route::put('/nguoidung/{id}', [UserController::class, 'update'])->name('nguoidung.update');
        Route::delete('/nguoidung/{id}', [UserController::class, 'destroy'])->name('nguoidung.destroy');
    });
});
