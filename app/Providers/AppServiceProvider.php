<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator; // <--- 1. Thêm dòng này
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
            Schema::defaultStringLength(191); // giảm từ 255 xuống 191
            Paginator::useBootstrap(); // <--- 2. Thêm dòng này
    }
}
