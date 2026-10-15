<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckGioHanhChinh as CheckGioHanhChinhMiddleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckTaiKhoanHoatDong;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'gio.hanh.chinh' => CheckGioHanhChinhMiddleware::class,
            'role' => CheckRole::class,
            'tai.khoan.hoat.dong' => CheckTaiKhoanHoatDong::class,
        ]);

        // Chưa đăng nhập -> trang đăng nhập; đã đăng nhập mà vào trang khách -> dashboard
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('dashboard'));
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*'),
        );
    })->create();
