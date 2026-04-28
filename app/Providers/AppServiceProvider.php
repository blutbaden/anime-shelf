<?php

namespace App\Providers;

use App\Models\Subscriber;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));

        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Share subscription status with main layout
        View::composer('layouts.app', function ($view) {
            $isSubscribed = false;
            if ($user = auth()->user()) {
                $isSubscribed = Subscriber::where('email', $user->email)->exists();
            }
            $view->with('isSubscribed', $isSubscribed);
        });
    }
}
