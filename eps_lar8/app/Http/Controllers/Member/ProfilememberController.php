<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Libraries\Calculation;
use App\Libraries\Encryption;
use App\Libraries\Terbilang;
use App\Models\Cart;
use App\Models\CompleteCartShop;
use App\Models\Invoice;
use App\Models\Lpse_config;
use App\Models\Member;
use App\Models\Nego;
use App\Models\ProductCategory;
use App\Models\Products;
use App\Models\Shop;
use App\Models\ShopCategory;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\Log;

class ProfilememberController extends Controller
{
    protected $data;
    protected $model;
    protected $Liberies;

    public function __construct(Request $request)
    {
        // Ambil semua data sesi
        $sessionData = $request->session()->all();
        $this->data['id_user'] = $sessionData['id'] ?? null;
        $this->model['member'] = new Member();
        $this->model['nego'] = new Nego();
        $this->model['products'] = new Products();
        $this->model['ProductCategory'] = new ProductCategory();
        $this->model['invoice'] = new Invoice();
        $this->model['ShopCategory'] = new ShopCategory();
        $this->model['cart'] = new Cart();
        $this->model['Shop'] = new Shop();
        $this->model['CompleteCartShop'] = new CompleteCartShop();
        $this->Liberies['terbilang'] = new Terbilang();
        $this->Liberies['encryption'] = new Encryption();

        $this->data['nama_user'] = '';

        if ($this->data['id_user'] != null) {
            $this->data['member'] = $this->model['member']->find($this->data['id_user']);
            $this->data['nama_user'] = $this->data['member']->nama;
        }
    }

    // Metode lain dalam controller
    public function index()
    {
        $this->data['user'] = $this->model['member']->find($this->data['id_user']);
        return view('member.profile.v_profile', $this->data);
    }

    public function dashboard()
    {
        return view('member.profile.dashboard', $this->data);
    }

    public function transaksi()
    {
        $transactions = $this->model['invoice']->getTransaction($this->data['id_user']);

        // Mengelompokkan transaksi berdasarkan id_cart
        $groupedTransactions = $transactions->groupBy('invoice');

        // Mengambil detail item untuk setiap transaksi
        foreach ($groupedTransactions as $invoice => $group) {
            foreach ($group as $transaction) {
                if ($transaction->status_invoice == 'waiting_approve_by_ppk') {
                    $transaction->status_invoice = 'Menunggu Konfirmasi PPK';
                } elseif ($transaction->status_invoice == 'pending') {
                    $transaction->status_invoice = 'Menunggu Konfirmasi Penjual';
                } elseif ($transaction->status_invoice == 'on_delivery') {
                    $transaction->status_invoice = 'Dalam Pengiriman';
                } elseif ($transaction->status_invoice == 'cancel' || $transaction->status_invoice == 'cancel_part' || $transaction->status_invoice == 'expired') {
                    $transaction->status_invoice = 'Pesanan Dibatalkan';
                } elseif ($transaction->status_invoice == 'complete_payment' && $transaction->payment == '0') {
                    $transaction->status_invoice = 'Menunggu Konfirmasi Pembayaran';
                } elseif ($transaction->status_invoice == 'complete_payment' && $transaction->payment == '1') {
                    $transaction->status_invoice = 'Sudah Di Bayar';
                } elseif ($transaction->status_invoice == 'complete' && $transaction->payment == '0') {
                    $transaction->status_invoice = 'Belum Di Bayar';
                } elseif ($transaction->status_invoice == 'complete' && $transaction->payment == '1') {
                    $transaction->status_invoice = 'Pesanan Selesai';
                }

                if ($transaction->status == 'waiting_approve_by_ppk') {
                    $transaction->status = 'Menunggu_Konfirmasi_PPK';
                } elseif ($transaction->status == 'waiting_accept_order') {
                    $transaction->status = 'Menunggu_Konfirmasi_Penjual';
                } elseif ($transaction->status == 'send_by_seller') {
                    $transaction->status = 'Dalam_Pengiriman';
                } elseif ($transaction->status == 'cancel_by_seller' || $transaction->status == 'cancel_by_time' || $transaction->status == 'cancel_by_marketplace' || $transaction->status == 'cancel_time_by_user' || $transaction->status == 'cancel_manual_by_user') {
                    $transaction->status = 'Pesanan_Dibatalkan';
                } elseif ($transaction->status == 'on_packing_process') {
                    $transaction->status = 'Packing';
                } elseif ($transaction->status == 'complete') {
                    $transaction->status = 'Selesai';
                } elseif ($transaction->status == 'refund') {
                    $transaction->status = 'Pesanan_Dikembalikan';
                }

                $transaction->items = DB::table('complete_cart_shop_detail')
                    ->where('id_cart', $transaction->id)
                    ->where('id_shop', $transaction->id_shop)
                    ->get();
            }
        }

        $this->data['transactions'] = $groupedTransactions;

        // return response()->json($groupedTransactions);

        return view('member.profile.transaksi', $this->data);
    }

    public function getNegos(Request $request)
    {
        $status = $request->input('status', 'all'); // Mendapatkan status dari request (default 'all')

        $negos = $this->model['nego']->getNegoByid_member($this->data['id_user']);

        // Proses status untuk setiap nego
        foreach ($negos as $nego) {
            if ($nego->status_ulang == 0 && $nego->status == 0) {
                $nego->status = 'diajukan';
            } elseif ($nego->status_ulang == 2 && $nego->status == 0) {
                $nego->status = 'nego_ulang';
            } elseif ($nego->status_ulang == 2 && $nego->status == 1) {
                $nego->status = 'nego_diterima';
            } elseif ($nego->status_ulang == 2 && $nego->status == 2) {
                $nego->status = 'nego_ditolak';
            } elseif ($nego->status_ulang == 1 && $nego->is_checkout == 1) {
                $nego->status = 'selesai';
            } else {
                $nego->status = 'Status_Tidak_Diketahui';
            }
        }

        // Filter negos berdasarkan status yang dipilih
        if ($status !== 'all') {
            $negos = $negos->filter(function ($nego) use ($status) {
                return $nego->status === $status;
            });
        }

        $this->data['negos'] = $negos;
        $this->data['selected_status'] = $status; // Menyimpan status yang dipilih untuk digunakan di view
        return view('member.profile.nego', $this->data);
    }



    public function GetDetailTransaction(Request $request)
    {
        $id_cart = $request->query('id');

        $cart_shops = DB::table('complete_cart_shop')
            ->where('id_cart', $id_cart)
            ->get();


        if ($cart_shops->isEmpty()) {
            return redirect()->route('profile.transaksi')->with('error', 'Detail transaksi tidak ditemukan.');
        }

        $transactions = [];

        foreach ($cart_shops as $cart_shop) {
            $shopId = $cart_shop->id_shop;
            $id_cart_shop = $cart_shop->id;

            // Ambil detail order
            $detailOrder = $this->model['CompleteCartShop']->getDetailOrderbyId($shopId, $id_cart_shop);
            $billing = $this->model['CompleteCartShop']->getUserById_cart_shop($id_cart_shop);

            if (!$detailOrder) {
                continue; // Skip jika detailOrder tidak ditemukan
            }

            if ($detailOrder->status == 'waiting_approve_by_ppk') {
                $detailOrder->status = 'Menunggu_Konfirmasi_PPK';
            } elseif ($detailOrder->status == 'waiting_accept_order') {
                $detailOrder->status = 'Menunggu_Konfirmasi_Penjual';
            } elseif ($detailOrder->status == 'send_by_seller') {
                $detailOrder->status = 'Dalam_Pengiriman';
            } elseif ($detailOrder->status == 'cancel_by_seller' || $detailOrder->status == 'cancel_by_time' || $detailOrder->status == 'cancel_by_marketplace' || $detailOrder->status == 'cancel_time_by_user' || $detailOrder->status == 'cancel_manual_by_user') {
                $detailOrder->status = 'Pesanan_Dibatalkan';
            } elseif ($detailOrder->status == 'on_packing_process') {
                $detailOrder->status = 'Packing';
            } elseif ($detailOrder->status == 'complete') {
                $detailOrder->status = 'Selesai';
            } elseif ($detailOrder->status == 'refund') {
                $detailOrder->status = 'Pesanan_Dikembalikan';
            }

            $detailProductorder = $this->model['CompleteCartShop']->getProductbytrax($id_cart_shop, $shopId);
            $total_barang_dengan_PPN = 0;
            $total_barang_tanpa_PPN = 0;

            foreach ($detailProductorder as $product) {
                if ($product->val_ppn != 0) {
                    $total_barang_dengan_PPN += $product->total_non_ppn;
                } else {
                    $total_barang_tanpa_PPN += $product->total_non_ppn;
                }
            }

            if (!$detailProductorder) {
                continue; // Skip this iteration if detailProductorder is not found
            }
            $transactions[] = [
                'detailOrder' => $detailOrder,
                'produk' => $detailProductorder,
                'billing' => $billing,
                'total_barang_dengan_PPN' => $total_barang_dengan_PPN,
                'total_barang_tanpa_PPN' => $total_barang_tanpa_PPN,
            ];
        }

        if (empty($transactions)) {
            return redirect()->route('profile.transaksi')->with('error', 'Tidak ada detail transaksi yang valid ditemukan.');
        }



        $this->data['transactions'] = $transactions;

        // return response()->json($this->data);

        return view('member.profile.detail_transaksi', $this->data);
    }

    public function getNegoDetail(Request $request)
    {
        $nego = $this->model['nego']->getdetailNego($request->input('id'));

        if ($nego->status_nego == 0 && $nego->status == 0) {
            $nego->status = 'diajukan';
        } elseif ($nego->status_nego >= 2 && $nego->status == 0) {
            $nego->status = 'nego_ulang';
        } elseif ($nego->status_nego >= 2 && $nego->status == 1) {
            $nego->status = 'nego_diterima';
        } elseif ($nego->status_nego >= 2 && $nego->status == 2) {
            $nego->status = 'nego_ditolak';
        } elseif ($nego->status_nego >= 1 && $nego->is_checkout == 1) {
            $nego->status = 'selesai';
        } else {
            $nego->status = 'Status_Tidak_Diketahui';
        }
        $history = DB::table('product_nego')->where('id_nego', $nego->id)->get();
        $nego->history = $history;
        //  return response()->json($nego);
        $this->data['nego'] = $nego;

        return view('member.profile.detail_nego', $this->data);
    }

    function accNego(Request $request)
    {
        $id_nego = $request->input('id_nego');
        $data_nego = DB::table('nego')->where('id', $id_nego)->first();
        $accNego = $this->model['nego']->acc_nego($id_nego);

        if ($accNego) {
            $tocart = $this->model['cart']->update_cart_after_nego($data_nego->id_shop, $id_nego);
            if ($tocart) {
                return response()->json([
                    'code' => 200,
                    'message' => 'Berhasil Mengupdate Keranjang',
                ]);
            }
            return response()->json([
                'code' => 200,
                'message' => 'Berhasil Nego Barang',
            ]);
        }
        return response()->json([
            'code' => 404,
            'message' => 'Gagal Nego Produk',
        ]);
    }

    public function tolak_nego(Request $request)
    {
        $id_nego = $request->input('id_nego');
        $note    = $request->input('reason');

        // Update 'nego' table
        $negoUpdate = DB::table('nego')
            ->where('id', $id_nego)
            ->update([
                'status' => '2',
                'status_nego' => '2'
            ]);

        // Check if 'nego' update was successful
        if ($negoUpdate) {
            // Get the latest product_nego id
            $pn = DB::table('product_nego')
                ->where('id_nego', $id_nego)
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->first();

            // Update 'product_nego' table
            $productNegoUpdate = DB::table('product_nego')
                ->where('id', $pn->id)
                ->update([
                    'status' => '2',
                    'catatan_pembeli' => $note
                ]);

            // Check if 'product_nego' update was successful
            if ($productNegoUpdate) {
                return response()->json(['status' => 'Success']);
            } else {
                return response()->json(['status' => 'Error updating product_nego'], 500);
            }
        } else {
            return response()->json(['status' => 'Error updating nego'], 500);
        }
    }

    function addRequestNego(Request $request)
    {
        $id_nego = $request->input('id_nego');
        $last_id = $request->input('last_id');
        $nego_price = $request->input('nego_price');
        $qty = $request->input('qty');
        $note = $request->input('catatan');
        $id_product = DB::table('product_nego')->where('id', $last_id)->value('id_product');
        $calc = new Calculation();

        $cf         = Lpse_config::first();
        $ppn        = $cf->ppn;
        $product    = Products::Find($id_product);

        $id_kategori = $product->id_category;
        $idtipeProduk = $product->id_tipe;

        // PPh menggunkan default barang
        $pph            = 1.5;

        // cek Jenis PPh
        $checkPPh         = $this->model['ProductCategory']->jenisProduct($id_kategori);

        // check PPN
        $CheckppnProduct   = $this->model['ProductCategory']->check_ppn($id_product);
        $checkShop         = $this->model['Shop']->getShopCategory($product->id_shop);

        if ($checkShop) {
            $checkKategori     = $this->model['ShopCategory']->getSpesialKategori($checkShop->shop_category);
            $spesial_cat_product = in_array($CheckppnProduct->id_category, [1949, 1947, 1952, 1948]) ? 1 : 0;

            if (isset($checkKategori->spesial_kategori) && $checkKategori->spesial_kategori == 1 && $spesial_cat_product == 1) {
                $ppn = 0;
            } else {
                $cek_ppn = $CheckppnProduct->barang_kena_ppn;
                if ($cek_ppn == '0') {
                    $ppn = 0;
                }
            }
        } else {
            $cek_ppn = $CheckppnProduct->barang_kena_ppn;
            if ($cek_ppn == '0') {
                $ppn = 0;
            }
        }

        // Set PPh
        if ($checkPPh == 1) {
            // Jasa biasa
            $pph = 2;
        }

        if ($idtipeProduk == 3 && $checkPPh == 1) {
            // untuk jasa sewa ruangan
            $pph = 10;
        }

        $dataArr = [
            'harga' => $nego_price,
            'ppn' => $ppn,
            'pph' => $pph,
        ];

        // Perhitungan
        $calculation    = $calc->calc_nego_harga($dataArr);
        $hargaDiterimaSatuan    = $calculation['harga_vendor_final'];

        $data   =   [
            'id_nego'           => $id_nego,
            'id_product'        => $id_product,
            'qty'               => $qty,
            'base_price'        => $nego_price,
            'harga_nego'        => $nego_price * $qty,
            'nominal_didapat'   => $hargaDiterimaSatuan,
            'catatan_pembeli'   => $note,
            'send_by'           => '0'
        ];

        $saveNego = $this->model['nego']->add_respon($data, $last_id);

        if ($saveNego) {
            return response()->json([
                'code' => 200,
                'message' => 'Berhasil Menambahkan Request Nego',
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'message' => 'Gagal Menambahkan Request Nego',
            ]);
        }
        // return response()->json([
        //     'code' => 404,
        //     'message' => $data,
        // ]);
    }

    function getwish()
    {
        $this->data['wishlists'] = $this->model['products']->getwishmember($this->data['id_user']);
        // return response()->json($this->data);
        return view('member.profile.wishlist', $this->data);
    }

    function getprofile()
    {
        $this->data['user'] = $this->model['member']->find($this->data['id_user']);
        return view('member.profile.profile', $this->data);
    }

    function v_update_password()
    {
        return view('member.profile.update_password', $this->data);
    }

    function store_password(Request $request)
    {
        $password = $request->input('current_password');
        $new_password = $request->input('new_password');

        $old_password_enc = DB::table('member')->where('id', $this->data['id_user'])->value('password');
        $old_password_des =  $this->Liberies['encryption']->decrypt($old_password_enc);

        if ($password != $old_password_des) {
            return response()->json([
                'success' => false,
                'code' => 400,
                'error' => 'Password lama salah!'
            ]);
        }
        $new_password_enc = $this->Liberies['encryption']->encrypt($new_password);
        $update = DB::table('member')->where('id', $this->data['id_user'])->update(['password' => $new_password_enc]);
        if ($update) {
            return response()->json([
                'success' => true,
                'code' => 200,
                'error' => 'Password berhasil diubah!'
            ]);
        }
        return response()->json([
            'success' => false,
            'code' => 400,
            'error' => 'Gagal mengubah password!'
        ]);
    }

    public function address()
    {
        $this->data['addresses'] =  $this->model['member']->getaddressbyIdMember($this->data['id_user']);
        // return response()->json($this->data);
        return view('member.profile.v_alamat', $this->data);
    }

    public function getuser(Request $request)
    {
        $tipe = $request->input('tipe');
        $this->data['tipe'] = $tipe;
        if ($tipe == 'pemohon') {
            $role = 3;
        } elseif ($tipe == 'Penyetuju_Pemohonan') {
            $role = 4;
        } elseif ($tipe == 'finance') {
            $role = 6;
        }

        $this->data['member_satker'] = $this->model['member']->get_member_satker($this->data['id_user'], $role);
        // return response()->json($this->data);
        return view('member.profile.v_user', $this->data);
    }

    function DeleteUser(Request $request) {
        $update = DB::table('member')->where('id', $request->input('id'))->update(['member_status' => 'delete']);

        if ($update) {
            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Berhasil Menghapus User',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'Gagal Menghapus User',
            ]);
        }
    }

    function getDetailUser($id)
    {
        $this->data['user'] = DB::table('member')->join('member_satker as ms', 'ms.id_member', 'member.id')->where('member.id', $id)->first();
        // return response()->json($data);
        return view('member.profile.detail_user', $this->data);
    }

    function store_user(Request $request)
    {
        $status = 'pending';

        if ($request->input('status') == 'Y') {
            $status = 'active';
        }

        $data_user = DB::table('member')->where('id', $this->data['id_user'])->first();

        $data = [
            'nama' => $request->input('name'),
            'username' => ' ',
            'password' => $this->Liberies['encryption']->encrypt('usereps'),
            'email' => $request->input('email'),
            'no_hp' => $request->input('no_hp'),
            'id_member_type' => $request->input('role'),
            'member_status' => $status,
            'activation_key' => ' ',
            'is_email_subscribe' => $data_user->is_email_subscribe,
            'registered_member' => 1,
            'last_update' => now(),
            'instansi' => $data_user->instansi,
            'satker' => $data_user->satker,
            'bidang' => $data_user->bidang,
            'id_instansi_lpse' => $data_user->id_instansi_lpse,
            'id_satker_lpse' => $data_user->id_satker_lpse,
            'id_bidang_lpse' => $data_user->id_bidang_lpse,
            'id_instansi' => $data_user->id_instansi,
            'id_satker' => $data_user->id_satker,
            'id_bidang' => $data_user->id_bidang,
        ];

        $id_member = DB::table('member')->insertGetId($data);

        if ($request->input('role') == 3) {
            $role = 'pemohon';
        } elseif ($request->input('role') == 4) {
            $role = 'penyetuju';
        } elseif ($request->input('role') == 6) {
            $role = 'finance';
        }

        if ($id_member) {
            $data_satker = array(
                'id_member' => $id_member,
                'id_instansi' => $data_user->id_instansi,
                'id_satker' => $data_user->id_satker,
                'list_role' => $role,
                'jabatan' => $request->input('jabatan'),
                'limit_start' => $request->input('batas_awal'),
                'limit_end' => $request->input('batas_akhir'),
                'created_user' => $data_user->id,
                'created_date' => now(),
                'updated_date' => now(),
            );

            $insert = DB::table('member_satker')->insert($data_satker);
            if ($insert) {
                return response()->json([
                    'success' => true,
                    'code' => 200,
                    'message' => 'Berhasil Menambah User',
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'code' => 400,
            'message' => 'Gagal Menambah User',
        ]);
    }

    function editAddress(Request $request)
    {
        $id_address = $request->input('id_address') ?? null;
        $province = DB::table('province')->select('*')->get();
        $this->data['address'] = 'empty';
        $this->data['provinces'] = $province;
        if ($id_address != null) {
            $this->data['address'] = $this->model['member']->getaddressbyId($id_address);
        }
        // return response()->json($this->data);
        return view('member.profile.v_tambah_alamat', $this->data);
    }

    function storeAddress(Request $request)
    {
        $member_address_id = $request->id;
        $address_name = $request->nama_penerima;
        $phone = $request->no_telepon;
        $province_id = $request->provinsi;
        $city_id = $request->kota;
        $subdistrict_id = $request->kecamatan;
        $address = $request->alamat;
        $postal_code = $request->kode_pos;

        DB::table('member_address')->where('member_id', $this->data['id_user'])->update(['is_default_shipping' => 'no', 'is_default_billing' => 'no']);

        $data = [
            'address_name' => $address_name,
            'subdistrict_id' => $subdistrict_id,
            'phone' => $phone,
            'province_id' => $province_id,
            'city_id' => $city_id,
            'address' => $address,
            'postal_code' => $postal_code,
            'member_id' => $this->data['id_user'],
            'last_updated_dt' => Carbon::now(),
            'is_default_shipping' => 'yes',
            'is_default_billing' => 'yes'
        ];

        if ($member_address_id != null) {
            DB::table('member_address')->where('member_address_id', $member_address_id)->update($data);
        } else {
            DB::table('member_address')->insert($data);
        }
        return redirect()->route('profile.address')->with('success', 'Alamat berhasil disimpan.');
    }

    function UpdateAddress(Request $request)
    {
        $id_address = $request->input('id_address');
        $action = $request->input('action');

        $data = [];
        // Atur status default berdasarkan aksi
        if ($action == 'set_billing') {
            DB::table('member_address')->where('member_id', $this->data['id_user'])->update(['is_default_billing' => 'no']);
            $data['is_default_billing'] = 'yes';
        } elseif ($action == 'set_shipping') {
            DB::table('member_address')->where('member_id', $this->data['id_user'])->update(['is_default_shipping' => 'no']);
            $data['is_default_shipping'] = 'yes';
        } elseif ($action == 'delete') {
            $data['active_status'] = 'inactive';
        } else {
            return response()->json(['error' => 'Invalid'], 404);
        }

        // Update alamat
        DB::table('member_address')->where('member_address_id', $id_address)->update($data);
        return response()->json(['success' => 'Berhasil Memperbaharui Alamat'], 200);
    }

    public function cetakInvoice(Request $request)
    {
        $id_cart_shop = $request->query('id');
        $id_shop = $request->query('id_shop');

        $detail_order  = $this->model['CompleteCartShop']->getDetailOrderbyId($id_shop, $id_cart_shop);
        $detail_order->detail = $this->model['CompleteCartShop']->getDetailProduct($id_shop, $id_cart_shop);

        $dataPembeli    = $this->model['CompleteCartShop']->getaddressUser($id_cart_shop);
        $dataSeller     = $this->model['CompleteCartShop']->getSellerById_cart_shop($id_cart_shop);

        $pdf = FacadePdf::loadView('pdf.newInvoice', ['data' => $detail_order, 'dataPembeli' => $dataPembeli, 'dataSeller' => $dataSeller]);

        return $pdf->stream('informasi_invoice.pdf');

        // return response()->json(['id_cart_shop' => $id_cart_shop, 'id_shop' => $id_shop]);
    }

    public function cetakKwitansi(Request $request)
    {
        $id_cart_shop = $request->query('id');
        $id_shop = $request->query('id_shop');

        $terbilang = $this->Liberies['terbilang'];
        $detail_order  = $this->model['CompleteCartShop']->getDetailOrderbyId($id_shop, $id_cart_shop);
        $detail_order->seller_address = $this->model['Shop']->getAddressByIdshop($id_shop);
        $detail_order->terbilang = $terbilang->terbilang($detail_order->total);
        $detail_order->tgl_indo = $terbilang->tgl_indo(date('Y-m-d', strtotime($detail_order->created_date)));
        $eps = [
            'nama' => 'PT. Elite Proxy Sistem',
            'npwp' => ' 73.035.456.0-022.000',
            'alamat' => 'Rukan Sudirman Park Apartement Jl Kh. Mas Mansyur KAV 35 A/15 Kelurahan Karet Tengsin Kec. Tanah Abang Jakarta Pusat DKI Jakarta'
        ];

        $dataPembeli    = $this->model['CompleteCartShop']->getUserById_cart_shop($id_cart_shop);

        $pdf = FacadePdf::loadView('pdf.Kwitansi', ['data' => $detail_order, 'eps' => $eps, 'dataPembeli' => $dataPembeli]);

        return $pdf->stream('Kwitansi.pdf');
    }

    public function getKontrak(Request $request)
    {
        $id_cart_shop = $request->query('id');
        $kontrak    = DB::table('kontrak')->where('id_complete_cart_shop', $id_cart_shop)->first();
        $this->data['kontrak'] = $kontrak;

        $cart = DB::table('complete_cart_shop')->where('id', $id_cart_shop)->first();

        $this->data['id_cart'] = $cart->id_cart;
        $this->data['id_shop'] = $cart->id_shop;
        $this->data['id_cart_shop'] = $id_cart_shop;
        return view('member.profile.v_kontrak', $this->data);
    }

    public function getSuratPesanan(Request $request)
    {
        $id_cart_shop = $request->query('id');
        $suratpesanan    = DB::table('s_pesanan')->where('id_complete_cart_shop', $id_cart_shop)->first();
        $this->data['suratpesanan'] = $suratpesanan;

        $cart = DB::table('complete_cart_shop')->where('id', $id_cart_shop)->first();

        $this->data['id_cart'] = $cart->id_cart;
        $this->data['id_shop'] = $cart->id_shop;
        $this->data['id_cart_shop'] = $id_cart_shop;

        return view('member.profile.v_suratpesanan', $this->data);
    }

    public function createKontrak(Request $request)
    {
        $id_cart_shop = $request->query('id');
        $id_shop = $request->query('id_shop');

        $order  = $this->model['CompleteCartShop']->getDetailOrderbyId($id_shop, $id_cart_shop);

        $order->terbilang = $this->Liberies['terbilang']->terbilang($order->total);
        $order->tgl_indo = $this->Liberies['terbilang']->tgl_indo(date('Y-m-d', strtotime("+3 day", strtotime($order->created_date))));

        $order->detail = $this->model['CompleteCartShop']->getDetailProduct($id_shop, $id_cart_shop);
        $htmlContent = view('pdf.kontrak', ['order' => $order])->render();

        $this->data['id_cart_shop'] = $id_cart_shop;
        $this->data['kontrak'] = $order;
        $this->data['htmlContent'] = $htmlContent;

        // return response()->json($this->data);

        return view('member.profile.create_kontrak', $this->data);
    }

    public function createSuratPesanan(Request $request)
    {
        $id_cart_shop = $request->query('id');
        $id_shop = $request->query('id_shop');

        $order  = $this->model['CompleteCartShop']->getDetailOrderbyId($id_shop, $id_cart_shop);

        $order->terbilang = $this->Liberies['terbilang']->terbilang($order->total);
        $order->tgl_indo = $this->Liberies['terbilang']->tgl_indo(date('Y-m-d', strtotime("+3 day", strtotime($order->created_date))));

        $order->pengiriman = $order->delivery_start ? date('d-m-y', $order->delivery_start) : null;

        if ($order->status == 'waiting_accept_order') {
            $order->status = 'Menunggu Seller Menerima Pesanan';
        } elseif ($order->status == 'on_packing_process') {
            $order->status = 'Proses Pengemasan Paket';
        } elseif ($order->status == 'send_by_seller') {
            $order->status = 'Dalam Pengiriman';
        } elseif ($order->status == 'complete') {
            $order->status = 'Paker Segera Tiba';
        } elseif ($order->status == 'complete' && $order->delivery_end != null) {
            $order->status = 'Paket Sampai';
        } elseif ($order->status == 'waiting_approve_by_ppk') {
            $order->status = 'Menunggu Persetujuan PPK';
        } else {
            $order->status = 'Pesanan Dibatalkan';
        }

        $order->detail = $this->model['CompleteCartShop']->getDetailProduct($id_shop, $id_cart_shop);
        $htmlContent = view('pdf.S_pesanan', ['order' => $order])->render();

        $this->data['id_cart_shop'] = $id_cart_shop;
        $this->data['suratpesanan'] = $order;
        $this->data['htmlContent'] = $htmlContent;

        // return response()->json($this->data);

        return view('member.profile.create_suratpesanan', $this->data);
    }


    public function editKontrak(Request $request)
    {
        $id_kontrak = $request->query('id');

        $kontrak = DB::table('kontrak')->where('id', $id_kontrak)->first();

        $this->data['id_cart_shop'] = $kontrak->id_complete_cart_shop;
        $this->data['kontrak'] = $kontrak;
        $this->data['htmlContent'] = $kontrak->document;

        // return response()->json($this->data);

        return view('member.profile.create_kontrak', $this->data);
    }

    public function editSuratPesanan(Request $request)
    {
        $id_suratpesanan = $request->query('id');

        $suratpesanan = DB::table('s_pesanan')->where('id', $id_suratpesanan)->first();

        $suratpesanan->invoice = substr($suratpesanan->invoice, 0, -4);

        $this->data['id_cart_shop'] = $suratpesanan->id_complete_cart_shop;
        $this->data['suratpesanan'] = $suratpesanan;
        $this->data['htmlContent'] = $suratpesanan->document;

        return view('member.profile.create_suratpesanan', $this->data);
    }


    public function storeKontrak(Request $request)
    {
        // Ambil data dari form
        $id_cart_shop       = $request->id_cs;
        $no_kontrak         = $request->no_kontrak;
        $total_harga        = $request->total_harga;
        $tanggal_kontrak    = $request->tanggal_kontrak;
        $nilai_kontrak      = $request->nilai_kontrak;
        $catatan            = $request->catatan;
        $content            = $request->dokumen_kontrak;

        $cart = DB::table('complete_cart_shop')->where('id', $id_cart_shop)->first();

        // return response()->json([
        //     'success' => false,
        //     'message' => $no_kontrak
        // ]);

        $dataArr = [
            'id_complete_cart_shop' => $id_cart_shop,
            'no_kontrak' => $no_kontrak,
            'id_shop' => $cart->id_shop,
            'member_id' => $this->data['id_user'],
            'total_harga' => $total_harga,
            'nilai_kontrak' => $nilai_kontrak,
            'tanggal_kontrak' => $tanggal_kontrak,
            'catatan' => $catatan,
            'document' => $content,
            'update_date' => Carbon::now(),
        ];

        $check = DB::table('kontrak')->where('id_complete_cart_shop', $id_cart_shop)->count();

        if ($check > 0) {
            // Ambil nilai is_seller_input sebelumnya
            $existingData = DB::table('kontrak')->where('id_complete_cart_shop', $id_cart_shop)->first();
            $currentIsSellerInput = $existingData->is_seller_input ?? 0;

            $data = array_merge([
                'created_date' => Carbon::now(),
                'is_seller_input' => $currentIsSellerInput + 1
            ], $dataArr);

            $update = DB::table('kontrak')->where('id_complete_cart_shop', $id_cart_shop)->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Kontrak berhasil diupdate'
            ]);
        } else {
            $data = array_merge([
                'created_date' => Carbon::now(),
                'is_seller_input' => 1
            ], $dataArr);

            $insert = DB::table('kontrak')->insert($data);

            return response()->json([
                'success' => true,
                'message' => 'Kontrak berhasil disimpan'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Kontrak gagal disimpan',
        ]);
    }

    public function storeSuratPesanan(Request $request)
    {
        $id_cart_shop       = $request->id_cs;
        $invoice            = $request->no_invoice;
        $tanggal            = $request->tanggal_pesan;
        $catatan            = $request->catatan;
        $content            = $request->dokumen_suratpesanan;

        $cart = DB::table('complete_cart_shop')->where('id', $id_cart_shop)->first();

        $dataArr = [
            'id_complete_cart_shop' => $id_cart_shop,
            'invoice' => $invoice,
            'id_shop' => $cart->id_shop,
            'id_user' => $this->data['id_user'],
            'tanggal_pesan' => $tanggal,
            'catatan' => $catatan,
            'document' => $content,
            'created_at' => Carbon::now(),
        ];

        $check = DB::table('s_pesanan')->where('id_complete_cart_shop', $id_cart_shop)->count();

        if ($check > 0) {
            $save = DB::table('s_pesanan')->where('id_complete_cart_shop', $id_cart_shop)->update($dataArr);
        } else {
            $save = DB::table('s_pesanan')->insert($dataArr);
        }

        if ($save) {
            return response()->json(['success' => true,]);
        }
        return response()->json(['success' => false,]);
    }
}
