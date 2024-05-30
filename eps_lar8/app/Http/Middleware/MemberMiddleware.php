<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MemberMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->get('is_member')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
