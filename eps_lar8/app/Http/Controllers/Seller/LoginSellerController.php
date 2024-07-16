<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Member;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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

    function getProvince() {
        $province = DB::table('province')->select('*')->get();

        if ($province) {
            return response()->json(['province' => $province], 200);
        } else {
            return response()->json(['message' => 'Failed to get province'], 500);
        }
    }

    function getCity($id_province = null) {
        $query  = DB::table('city')->select('*');

        if ($id_province != null) {
            $query->where('province_id',$id_province);
        }
        $city   = $query->get();

        if ($query) {
            return response()->json(['citys' => $city], 200);
        } else {
            return response()->json(['message' => 'Failed to get City'], 500);
        }
    }

    function getdistrict($id_city = null) {
        $query  = DB::table('subdistrict')->select('*');

        if ($id_city != null) {
            $query->where('city_id',$id_city);
        }
        $subdistrict   = $query->get();

        if ($query) {
            return response()->json(['subdistricts' => $subdistrict], 200);
        } else {
            return response()->json(['message' => 'Failed to get subdistrict'], 500);
        }
    }

}
