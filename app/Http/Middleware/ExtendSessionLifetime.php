<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ExtendSessionLifetime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated in any guard
        $isAuthenticated = Auth::guard('admin')->check() || 
                          Auth::guard('guru')->check() || 
                          Auth::guard('siswa')->check() || 
                          Auth::guard('orangtua')->check();

        if ($isAuthenticated) {
            // Extend session lifetime for authenticated users
            config(['session.lifetime' => 480]); // 8 hours
            
            // Update session timestamp to prevent expiration
            Session::put('last_activity', time());
            
            // Store user activity timestamp
            Session::put('user_last_activity', now()->timestamp);
        }

        return $next($request);
    }
}
