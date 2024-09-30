<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Libraries\Calculation;
use App\Models\Cart;
use App\Models\Lpse_config;
use App\Models\Nego;
use App\Models\ProductCategory;
use App\Models\Products;
use App\Models\Saldo;
use App\Models\Shop;
use App\Models\ShopCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\ElseIf_;

class NegoController extends Controller
{

    protected $user_id;
    protected $username;
    protected $seller;
    protected $Model;
    protected $Libraries;
    protected $data;
    protected $menu;

    public function __construct(Request $request)
    {
        $this->seller 	= $request->session()->get('seller_id');
        $sellerType     = Shop::getTypeById($this->seller);
        $saldoPending   = Saldo::calculatePendingSaldo($this->seller);

        // Membuat $this->data
        $this->data['title'] = 'Nego Pengadaan';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldoPending;

        // Model
        $this->Model['Shop'] = new Shop();
        $this->Model['Nego'] = new Nego();
        $this->Model['Cart'] = new Cart();
        $this->Model['ProductCategory'] = new ProductCategory();
        $this->Model['ShopCategory'] = new ShopCategory();

        // Libraries
        $this->Libraries['Calculation'] = new Calculation();
    }

    public function index()
    {
        $negos = $this->Model['Nego']->getNegos($this->seller, 0,'<',2);
        return view('seller.nego.index',$this->data,['negos'=>$negos]);
    }

    function GetDataNego($kondisi) {
        if ($kondisi == 'nego_ulang') {
            $status = 0 ;
            $status_nego = 2 ;
            $send_by = null;
            $spesial = null ;
        }elseif ($kondisi == 'telah_direspon') {
            $status = 1 ;
            $status_nego = 2 ;
            $send_by = null;
            $spesial = null ;
        }elseif($kondisi == 'nego_batal'){
            $status = 2 ;
            $status_nego = 2 ;
            $send_by = null;
            $spesial = null ;
        }else {
            $status = 0 ;
            $status_nego = '<';
            $send_by = 0;
            $spesial = 2 ;
        }
        $data = $this->Model['Nego']->getNegos($this->seller, $status, $status_nego,$spesial,$send_by);
        return response()->json(['negos'=>$data]);
    }

    function getDetailNego($id_nego){
        $nego = $this->Model['Nego']->DetailNego($this->seller,$id_nego);
        return response()->json(['nego'=>$nego]);
    }

    function calcNego(Request $request ){
        $qty        = $request->qty;
        $id_product = $request->id_product;
        $harga      = $request->hargaResponSatuan;

        $cf         = Lpse_config::first();
        $ppn        = $cf->ppn;

        $product = Products::Find($id_product);

        $id_kategori = $product->id_category;
        $idtipeProduk = $product->id_tipe;

        // PPh menggunkan default barang
        $pph            = 1.5;

        // cek Jenis PPh
        $checkPPh         = $this->Model['ProductCategory']->jenisProduct($id_kategori);

        // check PPN
        $CheckppnProduct   = $this->Model['ProductCategory']->check_ppn($id_product);
        $checkShop         = $this->Model['Shop']->getShopCategory($this->seller);

        if ($checkShop) {
            $checkKategori     = $this->Model['ShopCategory']->getSpesialKategori($checkShop->shop_category);
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
            'harga' => $harga,
            'ppn' => $ppn,
            'pph' => $pph,
        ];

        // Perhitungan
        $calculation    =$this->Libraries['Calculation']->calc_nego_harga($dataArr);
        $SellerPrice    =$calculation['harga_vendor_final'];

        // Callback
        $data           = [
            'qty' => $qty,
            'hargaSatuanDiterimaSeller' => $SellerPrice,
            'hargaTotalDiterimaSeller' => ($SellerPrice * $qty)
        ];

        return response()->json($data);
    }

    public function add_respon(Request $request){
        try {
            $qty                    = $request->qty;
            $id_nego                = $request->id_nego;
            $last_id                = $request->last_id;
            $note                   = $request->negoNote;
            $id_product             = $request->product;
            $hargaSatuan            = $request->hargaSatuan;
            $hargaDiterimaSatuan    = $request->hargaDiterimaSatuan;
            $hargaResponSatuan      = $request->hargaResponSatuan;

            $data   =   [
                'id_nego'           => $id_nego,
                'id_product'        => $id_product,
                'qty'               => $qty,
                'base_price'        => $hargaResponSatuan,
                'harga_nego'        => $hargaResponSatuan * $qty,
                'nominal_didapat'   => $hargaDiterimaSatuan,
                'catatan_penjual'   => $note,
                'send_by'           => '1'
            ];

            $saveNego = $this->Model['Nego']->add_respon($data,$last_id);

            if ($saveNego) {
                return response()->json(['status'=> 'Success']);
            } else {
                return response()->json(['status'=> 'Erorr']);
            }
        } catch (\Exception $e) {
            return response()->json(['status'=> 'Error: ' . $e->getMessage()]);
        }
    }

    public function acc_nego(Request $request)
    {
        $id_nego = $request->id_nego;

        $accNego = $this->Model['Nego']->acc_nego($id_nego);

        if ($accNego) {
            $tocart = $this->Model['Cart']->update_cart_after_nego($this->seller,$id_nego);
        }

        return response()->json(['status'=> $tocart]);
    }

    public function tolak_nego(Request $request)
    {
        $id_nego = $request->id_nego;
        $note    = $request->alasan;

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
                    'catatan_penjual' => $note
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

    public function Test_nego( $id_nego )
    {

        $test   =$this->Model['Cart']->update_cart_temp( $id_nego);

        return response()->json($test);
    }

}
