<?php

namespace App\Http\Middleware;

use Closure;

class SellerMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('is_seller') || !$request->session()->get('is_seller')) {
            return redirect()->route('seller.login')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
