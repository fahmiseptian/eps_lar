<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Store;

class CheckUserActivity
{
    protected $session;

    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    public function handle($request, Closure $next, $guard = null)
    {
        $userLastActive = $this->session->get('last_activity');

        if ($userLastActive && (time() - $userLastActive > 1800)) {
            // Jika tidak aktif selama 30 menit, logout
            auth()->logout();
            $this->session->flush(); // Bersihkan sesi
            return redirect()->route('admin.logout')->withErrors(['error' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
        }

        // Perbarui waktu aktivitas pengguna
        $this->session->put('last_activity', time());

        return $next($request);
    }
}