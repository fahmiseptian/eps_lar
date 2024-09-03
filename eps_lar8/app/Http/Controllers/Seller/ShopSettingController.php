<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Libraries\Encryption;
use App\Models\CompleteCartShop;
use Illuminate\Support\Carbon;
use App\Models\Shop;
use App\Models\Saldo;
use App\Models\Courier;
use App\Models\Etalase;
use App\Models\Shop_courier;
use App\Models\Province;
use App\Models\FreeOngkir;
use App\Models\Products;
use App\Models\ShopBanner;
use App\Models\ShopOperational;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShopSettingController extends Controller
{
    protected $seller;
    protected $data;
    protected $Model;
    protected $Libraries;

    public function __construct(Request $request)
    {
        $this->seller     = $request->session()->get('seller_id');

        // Model
        $this->Model['Shop'] = new Shop();
        $this->Model['Products'] = new Products();
        $this->Model['ShopOperational'] = new ShopOperational();
        $this->Model['CompleteCartShop'] = new CompleteCartShop();
        $this->Model['ShopBanner'] = new ShopBanner();
        $this->Model['Etalase'] = new Etalase();

        // Libraries
        $this->Libraries['Encryption'] = new Encryption();
    }

    public function index()
    {
        return view('seller.toko.index');
    }

    public function v_aasistent_chat()
    {
        return view('seller.toko.asistent-chat');
    }

    public function v_profile()
    {
        return view('seller.toko.profile');
    }

    public function v_etalase()
    {
        return view('seller.toko.etalase');
    }

    function getRatesProduk()
    {
        return response()->json(200);
    }

    function get_etalase()
    {
        $etalases = $this->Model['Etalase']->getEtalasse($this->seller);
        return response()->json($etalases);
    }

    function UpdateEtalase(Request $request)
    {
        $etalase = Etalase::find($request->id);
        if ($etalase) {
            $etalase->display_status = $request->display_status;
            $etalase->save();
            return response()->json(['message' => 'Display status updated successfully'], 200);
        }

        return response()->json(['message' => 'Etalase not found'], 404);
    }
    function get_profile()
    {
        $data = [];

        $id_member = $this->Model['Shop']->getIdMember($this->seller);
        $banner = $this->Model['Shop']->getShopBanner($this->seller);
        $lampiran = $this->Model['Shop']->getLampiranShop($this->seller);
        $shop = $this->Model['Shop']->getShop($this->seller);
        $banner_image = isset($banner[0]) && $banner[0]->image ? $banner[0]->image : null;
        $produk = $this->Model['Products']->countProductByIdShop($this->seller);
        $rate =  $this->Model['Products']->getCountRatingShop($this->seller);
        $count_order = $this->Model['CompleteCartShop']->countAllorder($this->seller);
        $tidak_terselesaikan = $this->Model['CompleteCartShop']->get_count_order($this->seller, 'cancel_by_time');
        if ($count_order > 0) {
            $persen_pesanan_tidak = round(($tidak_terselesaikan / $count_order) * 100);
        } else {
            $persen_pesanan_tidak = 0;
        }
        $follower = $this->Model['Shop']->getFollow($this->seller, $id_member);

        $data = [
            'banner' => $banner,
            'lampiran' => $lampiran,
            'data' => $shop,
            'image_banner' => $banner_image,
            'produk' => $produk,
            'rate' => $rate,
            'count' => $persen_pesanan_tidak,
            'follower' => $follower
        ];
        return response()->json($data);
    }

    function getRate($rating)
    {
        if ($rating == 'semua') {
            $data = $this->Model['Products']->getReviewsByIdshop($this->seller);
        } else {
            $data = $this->Model['Products']->getReviewsByIdshop($this->seller, $rating);
        }
        return response()->json($data);
    }

    function a_chat()
    {
        $data = $this->Model['Shop']->get_chatautoReply($this->seller);
        return response()->json($data);
    }

    function updateReplyChat(Request $request)
    {
        $tipe = $request->tipe;
        $status = $request->status;
        $attr = $request->update;

        $kondisi = $status == true ? 'Y' : 'N';

        $collom = $tipe == 'online' ?
            ($attr == 'change' ? 'autoreply_standar' : 'autoreply_standar_text') : ($attr == 'change' ? 'autoreply_offline' : 'autoreply_offline_text');

        // Siapkan data untuk update
        $data = [$collom => $attr == 'updateText' ? $status : $kondisi];

        // Lakukan update data
        $update = $this->Model['Shop']->updateData($data, $this->seller);

        // Kirimkan respons berdasarkan hasil update
        return $update
            ? response()->json(['message' => 'Berhasil update autoreply chat'], 200)
            : response()->json(['message' => 'Gagal update autoreply chat'], 500);
    }

    function updateProfile(Request $request)
    {
        $jenis = $request->field;
        $value = $request->value;
        $id_shop =  $this->seller;
        $id_member = $this->Model['Shop']->getIdMember($id_shop);

        if ($jenis == 'nama_pt') {
            $update = $this->Model['Shop']->updateProfile($id_shop, 'nama_pt', $value);
        } else if ($jenis == 'npwp') {
            $update = $this->Model['Shop']->updateProfile($id_shop, 'npwp', $value);
            // for table member
            $updateMember = DB::table('member')->where('id', $id_member)->update(['npwp' => $value]);
        } elseif ($jenis == 'nama_ktp') {
            $update = $this->Model['Shop']->updateProfile($id_shop, 'nama_pemilik', $value);
        } elseif ($jenis == 'nik') {
            $update = $this->Model['Shop']->updateProfile($id_shop, 'nik_pemilik', $value);
        } elseif ($jenis == 'address_npwp') {
            $update = DB::table('member')->where('id', $id_member)->update(['npwp_address' => $value]);
            // for table Lampiran
            $id_lampiran = DB::table('lampiran')->where('id_shop', $this->seller)->first();

            if (!$id_lampiran) {
                $id_lampiran = DB::table('lampiran')->insertGetId([
                    'id_shop' => $this->seller,
                    'last_update' => now(),
                ]);
            }

            $updateLampiran = DB::table('lampiran')->where('id_shop', $id_shop)->update(['alamat_npwp' => $value]);
        } elseif ($jenis == 'deskripsi') {
            $update = $this->Model['Shop']->updateProfile($id_shop, 'description', $value);
        }

        return $update
            ? response()->json(['message' => 'Berhasil update Profile'], 200)
            : response()->json(['message' => 'Gagal update Profile'], 500);
    }

    public function updatePassword(Request $request)
    {
        $id_member = $this->Model['Shop']->getIdMember($this->seller);

        $real_password_enc = DB::table('shop')->where('id', $this->seller)->value('password');
        $real_password_dec = $this->Libraries['Encryption']->decrypt($real_password_enc);

        if ($real_password_dec != $request->old_password) {
            return response()->json([
                'status' => 'error',
                'message' => $request->old_password,
            ], 400);
        }

        $new_password_enc = $this->Libraries['Encryption']->encrypt($request->new_password);
        DB::table('shop')->where('id', $this->seller)->update(['password' => $new_password_enc]);
        DB::table('member')->where('id', $id_member)->update(['password' => $new_password_enc]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully.',
        ]);
    }

    public function UploadFile(Request $request)
    {
        $jenis = $request->jenis;
        $file = $request->file('file');

        // Ambil data shop berdasarkan ID penjual
        $shop = $this->Model['Shop']->where('id', $this->seller)->first();

        // Tambahkan file ke media collection dan simpan objek media
        $media = $shop->addMedia($file)
            ->usingFileName(time() . '.' . $file->getClientOriginalExtension())
            ->toMediaCollection($jenis, $jenis);

        $url = $media->getUrl();

        if ($jenis == 'akta_perubahan') {
            $jenis = 'akta';
        }

        $id_lampiran = DB::table('lampiran')->where('id_shop', $this->seller)->first();

        if (!$id_lampiran) {
            // Melakukan insert dan mendapatkan ID dari lampiran yang baru disisipkan
            $id_lampiran = DB::table('lampiran')->insertGetId([
                'id_shop' => $this->seller,
                $jenis => $url,
                'last_update' => now(),
            ]);
        } else {
            DB::table('lampiran')->where('id', $id_lampiran->id)->update([
                $jenis => $url,
                'last_update' => now(),
            ]);
        }
        return response()->json(['filename' => $media->file_name,]);
    }

    public function UplaodBanner(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $file = $request->file('file');

        $latestBanner = $this->Model['ShopBanner']
            ->where('id_shop', $this->seller)
            ->orderBy('urutan', 'desc')
            ->first();

        $latestOrder = $latestBanner ? $latestBanner->urutan + 1 : 0;

        $id_shop_banner = $this->Model['ShopBanner']->insertGetId([
            'id_shop' => $this->seller,
            'urutan' => $latestOrder,
            'image' => '', // Mulai dengan URL kosong
        ]);

        $shop_banner = $this->Model['ShopBanner']->find($id_shop_banner);

        $media = $shop_banner->addMedia($file)
            ->usingFileName(time() . '.' . $file->getClientOriginalExtension())
            ->toMediaCollection('shop_banner', 'shop_banner');

        $url = $media->getUrl();

        $this->Model['ShopBanner']->where('id', $id_shop_banner)->update(['image' => $url]);

        return response()->json(['latestOrder' => $latestOrder]);
    }

    public function deleteBanner(Request $request)
    {
        // Validasi permintaan
        $request->validate([
            'urutan' => 'required|integer',
        ]);

        $urutan = $request->input('urutan');
        $shopId = $this->seller;

        $banner = ShopBanner::where('id_shop', $shopId)
            ->where('urutan', $urutan)
            ->first();

        if (!$banner) {
            return response()->json(['message' => 'Banner tidak ditemukan.'], 404);
        }

        if ($banner->hasMedia('shop_banner')) {
            $banner->clearMediaCollection('shop_banner');
        }
        $banner->delete();

        ShopBanner::where('id_shop', $shopId)
            ->where('urutan', '>', $urutan)
            ->decrement('urutan');

        return response()->json(['message' => 'Banner berhasil dihapus.'], 200);
    }

    function deleteEtalase(Request $request)
    {
        $request->validate([
            'etalaseId' => 'required|integer',
        ]);

        $etalaseId = $request->input('etalaseId');
        $shopId = $this->seller;

        $etalase = Etalase::where('id_shop', $shopId)
            ->where('id', $etalaseId)
            ->first();

        if (!$etalase) {
            return response()->json(['message' => 'Etalase tidak ditemukan.'], 404);
        }
        $etalase->delete();
        return response()->json(['message' => 'Etalase berhasil dihapus.'], 200);
    }

    function addEtalase(Request $request)
    {
        $namaEtalase = $request->input('namaEtalase');

        if (!empty($namaEtalase)) {
            $data = array(
                'name' => $namaEtalase,
                'id_shop' => $this->seller,
                'created_dt' => now(),
            );
            Etalase::create($data);

            return response()->json(['status' => 'success', 'message' => 'Etalase berhasil ditambahkan!'], 200);
        } else {
            return response()->json(['status' => 'Gagal', 'message' => 'Nama Etalase tidak Boleh Kosong!'], 404);
        }
    }

    function updateProfileSeller(Request $request)
    {
        $nama = $request->name;
        $file = $request->file('avatar');
        $shop = $this->Model['Shop']->find($this->seller);

        if ($file != null) {
            $media = $shop->addMedia($file)
                ->usingFileName(time() . '.' . $file->getClientOriginalExtension())
                ->toMediaCollection('avatar', 'avatar');

            $url = $media->getUrl();
            $shop->avatar = $url;
        }
        $shop->name = $nama;
        $shop->save();

        return response()->json(['status' => 'success']);
    }
}
