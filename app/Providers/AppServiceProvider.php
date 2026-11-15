<?php

namespace App\Providers;

use App\Models\CartItem;
use App\Models\PasswordResetRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Badge jumlah barang di ikon keranjang (navbar)
        View::composer('components.navbar', function ($view) {
            $count = 0;

            if (Auth::check() && Auth::user()->role === 'user') {
                $count = (int) CartItem::where('user_id', Auth::id())->sum('quantity');
            }

            $view->with('cartCount', $count);
        });

        // Badge permintaan reset password di sidebar admin
        View::composer('layouts.admin', function ($view) {
            $view->with(
                'pendingResetCount',
                PasswordResetRequest::where('status', 'pending')->count()
            );
        });

        if (app()->environment('local')) {
            URL::forceScheme('https');
        }
    }
}