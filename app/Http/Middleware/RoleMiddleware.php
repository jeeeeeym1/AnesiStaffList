<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!in_array(Auth::user()->role, $roles)) {
            // Redirect each role to their own home instead of showing 403
            return match (Auth::user()->role) {
                'staff' => redirect()->route('profile.show'),
                'admin' => redirect()->route('dashboard'),
                default => redirect()->route('login'),
            };
        }

        return $next($request);
    }
}
