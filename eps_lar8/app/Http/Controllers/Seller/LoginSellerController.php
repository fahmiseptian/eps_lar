<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Member;

use Illuminate\Http\Response;

class LoginSellerController extends Controller
{
    protected $data;

    public function __construct()
    {
        // Membuat $this->data
        $this->data['title'] = 'LOGIN';
    }

    public function showLoginForm()
    {
        return view('seller.auth.login',$this->data);
    }


public function login(Request $request)
{
    $email = $request->input('email');
    $password = $request->input('password');

    $member = Member::where('email', $email)->first();

    if ($member) {
        $seller = Shop::where('id_user', $member->id)->first();

        if ($seller) {
            if ($seller->decryptPassword($seller->password) == $password) {
                $request->session()->put('is_seller', true);
                $request->session()->put('seller_id', $seller->id);
                $request->session()->put('seller', $seller->name);
                return redirect()->intended('/seller');
            }
        }
    }
    // Jika tidak berhasil, kembalikan ke halaman login dengan pesan error
    $request->session()->put('error_seller', 'Email atau password salah');
    return redirect()->route('seller.login')->with('error', 'Email atau password salah');
}


    public function logout(Request $request)
    {
        $request->session()->forget('is_seller');
        $request->session()->forget('seller_id');
        $request->session()->forget('seller');

        // Redirect ke halaman login
        return redirect()->route('seller.login');
    }

}
