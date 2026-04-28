<?php

namespace App\Providers;

use App\Models\Subscriber;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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

        RateLimiter::for('browse', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

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
