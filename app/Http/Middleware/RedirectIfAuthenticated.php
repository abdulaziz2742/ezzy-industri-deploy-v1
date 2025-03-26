<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                if ($user->role === 'manajerial') {
                    return redirect('/manajerial/dashboard');
                } elseif ($user->role === 'karyawan') {
                    return redirect('/karyawan/dashboard');
                }
            }
        }

        return $next($request);
    }
}