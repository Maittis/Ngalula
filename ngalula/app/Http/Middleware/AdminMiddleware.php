<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // Store the intended URL for redirect after login
            Session::put('url.intended', $request->fullUrl());
            
            // Redirect to login page
            return redirect()->route('login')
                ->with('error', 'You must be logged in to access the admin panel.');
        }
        
        // Check if user is admin (using user_type field)
        $user = Auth::user();
        if (!$user || !in_array($user->user_type, ['admin', 'super_admin'])) {
            // Redirect non-admin users to appropriate dashboard
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Access denied. Admin privileges required.');
        }
        
        // Store admin session flag
        Session::put('is_admin', true);
        
        return $next($request);
    }
}
