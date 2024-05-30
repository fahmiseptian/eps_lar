<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Libraries\Encryption;
use App\Models\Member;
use Illuminate\Http\Request;

class LoginmemberController extends Controller
{
    protected $data;
    public function __construct(Request $request)
    {
        // Ambil semua data sesi
        $sessionData = $request->session()->all();
        $this->data['id_user'] = $sessionData['id'] ?? null;
    }

    public function showLoginForm()
    {
        return view('member.auth.index',$this->data);
    }

    public function authmember(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        $memberStatus = Member::checkemail($email);
        $passwords = Member::checkpassword($email);

        if ($memberStatus !== null && !empty($passwords)) {
            $passwordMatch = false;
            foreach ($passwords as $storedPassword) {
                $encryption = new Encryption();
                $decryptedPassword = $encryption->decrypt($storedPassword);

                if ($password == $decryptedPassword) {
                    $passwordMatch = true;
                    break;
                }
            }

            if ($passwordMatch) {
                $result = Member::getInstansiBYeamil($email);
                $request->session()->put('is_member', true);
                $request->session()->put([
                    'email' => $email,
                    'password' => $password,
                    'member_status' => $memberStatus,
                ]);

                return response()->json(['status' => true, 'instansi' => $result]);
            } else {
                return response()->json(['status' => false, 'message' => 'Password Salah']);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Akun atau Password Salah']);
        }
    }

    public function submitStep2(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $type = $request->login_type;
        $instansi = $request->instansi ?? null ?: null;
        $satker = $request->satker ?? null ?: null;
        $bidang = $request->bidang ?? null ?: null;

        if ($type == 'login' && !empty($password) && !empty($email)) {
            $checkAccount = Member::checkAccount($email, $instansi, $satker, $bidang);
            $encryption = new Encryption();
            $checkDecPass = $encryption->decrypt(Member::checkDecPass($email, $instansi, $satker, $bidang));

            if ($checkAccount == 'active' && $checkDecPass == $password) {
                $update = Member::updateDate($email, $instansi, $satker, $bidang);
                $user = Member::getDataByEmail($email, $instansi, $satker, $bidang);

                if (empty($user)) {
                    return response()->json(['status' => 'error', 'message' => 'Data pengguna tidak ditemukan.']);
                }

                $data = $user[0];
                $wish = Member::getWishlist($data->id);

                $newdata = [
                    'id' => $data->id,
                    'nama' => $data->nama,
                    'email' => $data->email,
                    'id_member_type' => $data->id_member_type,
                    'wishlist' => $wish,
                    'foto' => $data->foto,
                    'logged_in' => true,
                    'login_as' => 'buyer',
                ];

                $request->session()->put($newdata);
                $login_as = $request->session()->get('login_as');

                $last_url = $request->session()->get('last_url', '');

                if (empty($last_url)) {
                    if ($login_as == 'seller') {
                        $last_url = url('seller_center');
                    } elseif ($login_as == 'user') {
                        $last_url = url('/');
                    } else {
                        $last_url = url('/');
                    }
                }

                return response()->json(['status' => 'success', 'redirectUrl' => $last_url]);
            } else {
                if (empty($checkAccount)) {
                    return response()->json(['status' => 'error', 'message' => 'Email belum terdaftar.']);
                } elseif ($checkAccount == 'active' && $checkDecPass != $password) {
                    return response()->json(['status' => 'error', 'message' => 'Password salah.']);
                } elseif ($checkAccount == 'pending') {
                    return response()->json(['status' => 'error', 'message' => 'Silakan klik tautan aktivasi pada email yang telah kami kirim sebelumnya.']);
                } elseif ($checkAccount != 'active' && !empty($checkAccount)) {
                    return response()->json(['status' => 'error', 'message' => 'Akun Anda berstatus ' . $checkAccount . ', silakan hubungi layanan pelanggan.']);
                }
                return response()->json(['status' => 'error', 'message' => "3"]);
            }
        }

        return response()->json(['status' => 'error', 'message' => $type]);
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login');
    }

    public function get_instansi(Request $request)
    {
        $instansi = $request->instansi;
        $result = Member::get_instansi($instansi);
        return response()->json($result);
    }

    public function get_satker(Request $request)
    {
        $instansi = $request->instansi;
        $result = Member::get_satker($instansi);
        return response()->json($result);
    }

    public function get_bidang(Request $request)
    {
        $satker = $request->satker;
        $result = Member::get_bidang($satker);
        return response()->json($result);
    }
}
