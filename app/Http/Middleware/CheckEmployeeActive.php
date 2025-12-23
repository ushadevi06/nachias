<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckEmployeeActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // ✅ Super Admin bypass
            if ($user->id != 1) {

                if ($user->status !== 'Active') {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect('/')
                        ->with('danger', 'Your account has been deactivated. Contact admin.');
                }
            }
        }

        return $next($request);
    }
}
