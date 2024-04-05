<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

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
            if ($user->decryptPassword($user->password) == $password) {
                $request->session()->put('is_admin', true);
                $request->session()->put('user_id', $user_id);
                $request->session()->put('username', $username);
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

    public function test()
    {
        $this->data['test'] = 'User';
        $users = User::all(); 
        foreach ($users as $user) {
            $user->decrypted_password = $user->decryptPassword($user->password);
            $user->ecrpyt = $user->encryptPassword( $user->decrypted_password);
        }
        $this->data['users'] = $users;

        return view('admin.test', $this->data);
    }
}
