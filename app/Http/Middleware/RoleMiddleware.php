<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (Auth::check()) {
            $loginUserRole = loginUser()->role;

            if ($loginUserRole == $role) {
                return $next($request);
            }

            if ($loginUserRole == 1) {
                return redirect()->route("admin.index");
            }

            if ($loginUserRole == 2) {
                return redirect()->route("location.index");
            }

            // If not authorized, return 403 or redirect
            abort(403, 'Unauthorized action based on role.');
        }
        return redirect()->route("login");
    }
}
