<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Share pending registration count with the navigation view
        // so the admin badge updates without touching every controller.
        View::composer('layouts.navigation', function ($view) {
            if (Auth::check() && Auth::user()->role === 'admin') {
                $view->with('pendingUserCount', User::where('role', 'citizen')
                    ->where('account_status', 'pending')
                    ->count());
            } else {
                $view->with('pendingUserCount', 0);
            }
        });
    }
}