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
        $this->data['title'] = 'LOGIN';
    }

    public function showLoginForm()
    {
        return view('admin.auth.login',$this->data);
    }

    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        // Ambil pengguna berdasarkan email
        $user = User::where('username', $username)->first();

        if ($user) {
            $user_id = $user->id;
            $access_id = $user->access_id;
            $access = Access::where('id', $access_id)->first();
            $access_name = $access->name;
            if ($user->decryptPassword($user->password) == $password) {
                $request->session()->put('is_admin', true);
                $request->session()->put('user_id', $user_id);
                $request->session()->put('username', $username);
                $request->session()->put('access_name', $access_name);
                return redirect()->intended('/admin');
            }
        }
        return redirect()->back()->withErrors(['error' => 'Username atau password salah']);
    }

     public function logout(Request $request)
    {
        $request->session()->forget('is_admin');
        $request->session()->forget('user_id');
        $request->session()->forget('username');

        // Redirect ke halaman login
        return redirect()->route('admin.login');
    }
}
