<?php

namespace App\Providers;

use App\Support\MessageState;
use App\Support\WishlistState;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            $view->with('wishlistCount', WishlistState::count());
            $view->with('messageUnreadCount', MessageState::unreadCount());
        });
    }
}
