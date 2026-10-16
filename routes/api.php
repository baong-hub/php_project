<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LopHocController;
use App\Http\Controllers\Api\SinhVienController;

// Lấy token: POST /api/login {email, password}
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

// Các API cần token: header "Authorization: Bearer <token>"
Route::middleware(['auth:sanctum', 'tai.khoan.hoat.dong'])->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin và giảng viên đều quản lý được lớp học, sinh viên (chỉ trong giờ hành chính)
    Route::middleware(['role:admin,giang_vien', 'gio.hanh.chinh'])->group(function () {
        Route::apiResource('lop-hoc', LopHocController::class)->parameters(['lop-hoc' => 'id'])->except('destroy');
        Route::apiResource('sinh-vien', SinhVienController::class)->parameters(['sinh-vien' => 'id']);

        // Chỉ admin được xóa lớp
        Route::delete('/lop-hoc/{id}', [LopHocController::class, 'destroy'])->middleware('role:admin');
    });
});
