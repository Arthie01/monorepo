<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('usuario')) {
            return redirect('/login');
        }
        return $next($request);
    }
}
