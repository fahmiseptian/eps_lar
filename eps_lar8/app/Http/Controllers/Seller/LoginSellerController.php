<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Libraries\Encryption;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoginSellerController extends Controller
{
    protected $data;
    protected $library;
    protected $model;

    public function __construct()
    {
        // Membuat $this->data
        $this->data['title'] = 'LOGIN';

        // Membuat Library
        $this->library['Encryption'] = new Encryption();

        // Model
        $this->model['Shop'] = new Shop();
        $this->model['Member'] = new Member();
    }

    public function showLoginForm()
    {
        return view('seller.auth.login', $this->data);
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

    function getProvince()
    {
        $province = DB::table('province')->select('*')->get();

        if ($province) {
            return response()->json(['province' => $province], 200);
        } else {
            return response()->json(['message' => 'Failed to get province'], 500);
        }
    }

    function getCity($id_province = null)
    {
        $query  = DB::table('city')->select('*');

        if ($id_province != null) {
            $query->where('province_id', $id_province);
        }
        $city   = $query->get();

        if ($query) {
            return response()->json(['citys' => $city], 200);
        } else {
            return response()->json(['message' => 'Failed to get City'], 500);
        }
    }

    function getdistrict($id_city = null)
    {
        $query  = DB::table('subdistrict')->select('*');

        if ($id_city != null) {
            $query->where('city_id', $id_city);
        }
        $subdistrict   = $query->get();

        if ($query) {
            return response()->json(['subdistricts' => $subdistrict], 200);
        } else {
            return response()->json(['message' => 'Failed to get subdistrict'], 500);
        }
    }

    function getKategori()
    {
        $kategori = DB::table('shop_category')->select('*')->get();

        if ($kategori) {
            return response()->json($kategori, 200);
        } else {
            return response()->json(['message' => 'Failed to get category'], 500);
        }
    }

    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_pemilik' => 'required|string',
            'email' => 'required|email',
            'no_telpon' => 'required|string',
            'password' => 'required|string',
            'npwp' => 'required|string',
            'nik' => 'required|string',
            'alamat_npwp' => 'required|string',
            'kategori_toko' => 'required|string',
            'provinsi' => 'required|string',
            'kota' => 'required|string',
            'kecamatan' => 'required|string',
            'kode_pos' => 'required|string',
            'alamat_detail' => 'required|string',
            'akta_pendirian-file' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
            'akta_perubahan-file' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
            'NIB-file' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
            'ktp-file' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
            'npwp-file' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
            'pkp-file' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
        ]);


        // Pengecekan Email Address
        $check = $this->model['Member']->checkUser($request->email);
        if ($check) {
            return response()->json(['errors' => 'Email Sudah Terdaftar'], 422);
        }

        // Encyrpt password
        $password = $this->library['Encryption']->encrypt($request->password);

        // Data Member
        $data_member = [
            'email' => $request->email,
            'password' => $password,
            'nama' => $request->nama_perusahaan,
            'no_hp' => $request->no_telpon,
            'npwp_address' => $request->alamat_npwp,
            'npwp' => $request->npwp,
            'username' => $request->nama_pemilik,
            'activation_key' => Str::random(32),
        ];

        $id_member = DB::table('member')->insertGetId($data_member);

        if (!$id_member) {
            return response()->json(['errors' => 'Gagal Mendaftar Member'], 422);
        }

        // Data Shop
        $data_shop = [
            'id_user' => $id_member,
            'password' => $password,
            'name' => $request->nama_perusahaan,
            'nama_pt' => $request->nama_perusahaan,
            'phone' => $request->no_telpon,
            'shop_category' => $request->kategori_toko,
            'npwp' => $request->npwp,
            'avatar' => asset('/img/app/default_seller.jpg'),
            'nik_pemilik' => $request->nik,
            'nama_pemilik' => $request->nama_pemilik,
            'autoreply_standar_text' => '',
            'autoreply_offline_text' => '',
            'created_date' => Carbon::now()
        ];

        $id_shop = $this->model['Shop']->insertGetId($data_shop);

        if (!$id_shop) {
            return response()->json(['errors' => 'Gagal Mendaftar Seller'], 422);
        }

        // data Addresses
        $data_address = [
            'member_id' => $id_member,
            'address_name' => $request->nama_perusahaan,
            'phone' => $request->no_telpon,
            'province_id' => $request->provinsi,
            'city_id' => $request->kota,
            'subdistrict_id' => $request->kecamatan,
            'address' => $request->alamat_detail,
            'postal_code' => $request->kode_pos,
            'is_shop_address' => 'yes',
            'is_default_shipping' => 'yes',
            'created_dt' => Carbon::now(),
            'last_updated_dt' => Carbon::now(),
        ];

        DB::table('member_address')->insert($data_address);

        // Proses Documentation
        $files = [
            'akta_pendirian-file' => 'akta',
            'akta_perubahan-file' => 'akta_pendirian',
            'NIB-file' => 'nib',
            'ktp-file' => 'ktp',
            'npwp-file' => 'npwp',
            'pkp-file' => 'pkp',
        ];

        $lampiran = [
            'id_shop'=> $id_shop,
            'alamat_npwp' => $request->alamat_npwp,
        ];

        $shop = $this->model['Shop']->where('id',$id_shop)->first();

        foreach ($files as $fileInputName => $path) {
            if ($request->hasFile($fileInputName)) {
                $file = $request->file($fileInputName);
                $fileName = time() . '.' . $file->getClientOriginalExtension();

                $media = $shop->addMedia($file)
                    ->usingFileName($fileName)
                    ->toMediaCollection($path, $path);

                $lampiran[$path] = $media->getUrl();
            } else {
                $lampiran[$path] = null;
            }
        }
        $id_lampiran = DB::table('lampiran')->insert($lampiran);
        DB::table('shop_config')->insert([
            'id_shop' => $id_shop,
        ]);

        return response()->json(['message' => 'Seller Berhasil Terdaftar'], 200);
    }
}
