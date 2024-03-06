<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\User;
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    // Memproses permintaan login
    public function login(Request $request)
    {
        // Validasi data input
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Ambil data pengguna berdasarkan username
        $user = User::where('username', $credentials['username'])->first();

        // Memeriksa apakah pengguna ditemukan
        if ($user) {
            // Memeriksa apakah password cocok
            if (Hash::check($credentials['password'], $user->password)) {
                // Otentikasi berhasil
                Auth::login($user);
                return redirect()->intended('/admin/dashboard'); // Redirect ke halaman setelah login berhasil
            }
            else {
                return back()->withErrors(['message' => 'password salah.'])->withInput();
            }
        }

        // Otentikasi gagal
        return back()->withErrors(['message' => 'Username atau password salah.'])->withInput();
    }

    // Logout pengguna
    public function logout()
    {
        Auth::logout();
        return redirect('/admin/login');
    }
}
