<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Libraries\Encryption;
use App\Libraries\Lkpp;
use App\Libraries\Lpse;
use App\Models\Member;
use App\Services\JWTService;
use Illuminate\Http\Request;

class LoginmemberController extends Controller
{
    protected $data;
    public function __construct(Request $request)
    {
        // Ambil semua data sesi
        $sessionData = $request->session()->all();
        $this->data['id_user'] = $sessionData['id'] ?? null;
        error_reporting(0);
    }

    public function showLoginForm()
    {
        return view('member.auth.index', $this->data);
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
                    } elseif ($login_as == 'buyer') {
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

    public function login_lpse(Request $request)
    {
        // 1. GET HEADER PARAM & VALUE THEN CHECK AUTHORIZE FOR USER LPSE
        $head               = $request->headers->all();
        $post_value         = $request->getContent();
        $n_data             = json_decode($post_value, true);
        $now                = now();
        $exp                = $now->addDay();

        $category = $head['x-vertical-type'][0];
        $token = $n_data['token'];

        // INSERT LOG_PAYLOAD (HEADER, BODY, TOKEN LPSE)
        $data_log       = array(
            'header'            => json_encode($head),
            'body'              => json_encode($n_data),
            'token_lpse'        => str_replace('"', "", json_encode($token)),
            'category'          => $category,
            'expired_dt_token'  => $exp,
        );

        // Save payload
        $lpse = new Lpse();
        $lkpp = new Lkpp();
        $id_log = $lpse->insertLogPayload($data_log);

        // 2. GET MEMBER ID, UPDATE LOG PAYLOAD
        $id_instansi_lpse = str_replace('"', "", json_encode($n_data['payload']['idInstansi']) ?? null ?: null);
        $id_satker_lpse = str_replace('"', "", json_encode($n_data['payload']['idSatker']) ?? null ?: null);
        $id_bidang_lpse = str_replace('"', "", json_encode($n_data['payload']['id_bidang']) ?? null ?: null);

        //GET MEMBER ID

        $data_member = array(
            'email'    => str_replace('"', "", json_encode($n_data['payload']['email'])),
            'nama'     => str_replace('"', "", json_encode($n_data['payload']['realName'])),
            'username' => str_replace('"', "", json_encode($n_data['payload']['userName'])),
            'no_hp'    => str_replace('"', "", json_encode($n_data['payload']['phone'])),

            'instansi' => str_replace('"', "", json_encode($n_data['payload']['namaInstansi'])),
            'satker'   => str_replace('"', "", json_encode($n_data['payload']['namaSatker'])),
            'bidang'   => str_replace('"', "", json_encode($n_data['payload']['bidang'])),

            'id_instansi_lpse'   => $id_instansi_lpse,
            'id_satker_lpse'   => $id_satker_lpse,
            'id_bidang_lpse'   => $id_bidang_lpse,
        );

        $id_member = $lpse->getIdMember($data_member);


        $user = $lpse->getDataById($id_member);

        foreach ($user as $data) {

            $wish       = Member::getWishlist($id_member);
            $newdata = array(
                'id' => $data->id,
                'nama' => $data->nama,
                'email' => $data->email,
                'id_member_type' => $data->id_member_type,
                'wishlist' => $wish,
                'foto' => $data->foto,
                'logged_in' => true,
                'login_as' => 'buyer',
            );
            $request->session()->put($newdata);
            $login_as = $request->session()->get('login_as');


            // 3. AUTHORIZED LOGIN
            $client_secret  = $head['X-Client-Secret'];
            $client_id      = $head['X-Client-Id'];
            $data_auth      = array('client_id' => $client_id, 'client_secret' => $client_secret);

            $check_auth     = $lpse->checkUserAPI($data_auth);



            if ($id_member) { // && $check_auth > 0
                $code   = '200';      // CODE 200: USER & MEMBER AUTHORIZED
                $message = null;
            }
            if ($check_auth = 0) {
                $code   = '400';      // CODE 400: USER UNAUTHORIZED
                $message = 'USER UNAUTHORIZED';
            }
            if (!$id_member) {
                $code   = '402';      // CODE 402: ID MEMBER NOT FOUND
                $message = 'MEMBER NOT FOUND';
            }

            $data_token = array(
                'id_member'     => $id_member,
                'email'         => str_replace('"', "", json_encode($n_data['payload']['email'])),
                'nama'          => str_replace('"', "", json_encode($n_data['payload']['userName'])),
                'default_cat'   => $category,
                'sess_start'    => $now,
                'sess_expired'  => $exp
            );

            $token_eps = JWTService::generateToken($data_token);

            $data_update     = array(
                'token_eps' => $token_eps,
                'id_member' => $id_member,
                'response'  => $code,
            );

            $data_log2 = array(
                'id_member' => $id_member,
                'category' => $category,
                'header' => json_encode($head),
                'body' => json_encode($n_data),
                'token' => $token_eps,
                'token_lpse' => $token,
                'expired_date' => $exp,
            );

            // NOTE save log
            $update_log     = $lpse->updateLogPayload($data_update, $id_log);
            $lkpp->save_token_lpse($data_log2, $id_member);

            if ($code == '200') {
                return response()->json([
                    'code'   => 200,
                    'data' => ['token' => $token_eps],
                    'message' => null,
                    'status' => true
                ]);
            } else {
                return response()->json([
                    'code'   => $code,
                    'data' => null,
                    'message' => $message,
                    'status' => false,
                ]);
            }
        }
    }
}
