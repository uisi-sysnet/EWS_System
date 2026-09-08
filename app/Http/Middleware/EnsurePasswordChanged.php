<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->first_login) {
            if (! $request->routeIs('password.change') && ! $request->routeIs('password.update')) {
                return redirect()->route('password.change');
            }
        }

        return $next($request);
    }
}