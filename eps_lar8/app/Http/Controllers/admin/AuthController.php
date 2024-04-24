<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Access;

class AuthController extends Controller
{
    protected $data;

    public function __construct()
    {
        // Membuat $this->data
        $this->data['title'] = 'Login';
    }

    public function showLoginForm(Request $request)
    {
        // Cek apakah pengguna sudah login
        if ($request->session()->has('is_admin') && $request->session()->has('access_code')) {
            // Jika sudah login, arahkan ke halaman dashboard
            return redirect()->route('admin');
        }

        return view('admin.auth.login', $this->data);
    }

    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        // Ambil pengguna berdasarkan email
        // Ambil semua pengguna dengan akses-akses yang terkait
        $users = User::where('username', $username)
        ->where('active', '!=', 2) // Menghindari pengguna dengan active = 2
        ->get();

        foreach ($users as $user) {
            // Cek setiap pengguna yang ditemukan
            $user_id = $user->id;
            $access_id = $user->access_id;
            $access = Access::where('id', $access_id)->first();
            $access_name = $access->name;
            $access_code = $access->code;
            if ($user->active == 1) {
                if ($user->decryptPassword($user->password) == $password) {
                    $request->session()->put('is_admin', true);
                    $request->session()->put('user_id', $user_id);
                    $request->session()->put('username', $username);
                    $request->session()->put('access_name', $access_name);
                    $request->session()->put('access_code', $access_code);
                    return redirect()->intended('/admin');
                }
            } elseif ($user->active == 0) {
                $request->session()->put('error_admin', 'Status akun tidak aktif. Hubungi developer untuk lebih lanjut.');
                return redirect()->back()->withErrors(['error' => 'Status akun tidak aktif. Hubungi developer untuk lebih lanjut.']);
            }
        }
        // Jika tidak berhasil, kembalikan ke halaman login dengan pesan error
        $request->session()->put('error_admin', 'Username atau password salah');
        return redirect()->back()->withErrors(['error' => 'Username atau password salah']);
    }

     public function logout(Request $request)
    {
        $request->session()->forget('is_admin');
        $request->session()->forget('user_id');
        $request->session()->forget('username');
        $request->session()->forget('access_name');
        $request->session()->forget('access_code');

        // Redirect ke halaman login
        return redirect()->route('admin.login');
    }
}
