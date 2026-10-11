<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\Menu;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Menu động lấy từ bảng menus (quản lý tại trang /menu)
        View::composer('partials.sidebar', function ($view) {
            $view->with('sidebarNhoms', Menu::sidebarTheoNhom());
        });

        View::composer('partials.header', function ($view) {
            $view->with('headerMenus', Menu::dangHienThi('header'));
        });
    }
}
