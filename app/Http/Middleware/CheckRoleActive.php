<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRoleActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->id == 1) {
                return $next($request);
            }
            if (!$user->role || $user->role->status !== 'Active') {
                Auth::logout();
                return redirect('/')
                    ->with('danger', 'Your role has been deactivated.');
            }
        }
        return $next($request);
    }
}
