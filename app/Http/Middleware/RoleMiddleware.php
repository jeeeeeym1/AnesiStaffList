<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    // Check if user has the required role to access the route
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Get the current user
        $currentUser = Auth::user();

        // Get the user's role
        $userRole = $currentUser->role;

        // Check if user's role is in the allowed roles
        $hasRequiredRole = in_array($userRole, $roles);

        // If user doesn't have required role, redirect based on their role
        if (!$hasRequiredRole) {
            // Check user's role and redirect them to their home page
            if ($userRole === 'staff') {
                return redirect()->route('profile.show');
            } elseif ($userRole === 'admin') {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('login');
            }
        }

        // User has required role, let them continue
        return $next($request);
    }
}
