<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class DetectDevice
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
{
    $agent = new Agent();

    // Menganggap tablet sebagai desktop
    $isMobile = $agent->isMobile() && !$agent->isTablet();
    $request->attributes->set('isMobile', $isMobile);

    // Tambahkan log untuk debugging
    Log::info('Device detected: ', [
        'isMobile' => $isMobile,
        'isTablet' => $agent->isTablet(),
        'userAgent' => $request->header('User-Agent'),
    ]);

    return $next($request);
}


}
