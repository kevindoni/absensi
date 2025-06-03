<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class SessionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Listen for login events to extend session
        Event::listen(Login::class, function (Login $event) {
            $guard = $event->guard;
            $user = $event->user;
            
            // Extend session lifetime for authenticated users
            session(['auth_guard' => $guard]);
            session(['user_login_time' => now()->timestamp]);
            session(['user_id' => $user->id]);
        });

        // Listen for logout events
        Event::listen(Logout::class, function (Logout $event) {
            $guard = $event->guard;
            $user = $event->user;
        });
    }
}
