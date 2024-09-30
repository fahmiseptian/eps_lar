<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Libraries\Calculation;
use App\Libraries\Checkout;
use App\Models\Cart;
use App\Models\CartShop;
use App\Models\CartShopTemporary;
use App\Models\CompleteCartShop;
use App\Models\Etalase;
use App\Models\Etalse;
use App\Models\Invoice;
use App\Models\Lpse_config;
use App\Models\Member;
use App\Models\Nego;
use App\Models\ProductCategory;
use App\Models\Products;
use App\Models\Shop;
use App\Models\Shop_courier;
use App\Models\ShopBanner;
use App\Models\ShopCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomememberController extends Controller
{
    protected $data;
    protected $user_id;
    protected $model;
    protected $Libraries;

    public function __construct(Request $request)
    {
        // Ambil semua data sesi
        $sessionData = $request->session()->all();
        $this->user_id = $sessionData['id'] ?? null;

        $this->Libraries['Calculation'] = new Calculation();

        $this->model['products'] = new Products();
        $this->model['shop_banner'] = new ShopBanner();
        $this->model['shop'] = new Shop();
        $this->model['member'] = new Member();
        $this->model['ProductCategory'] = new ProductCategory();
        $this->model['ShopCategory'] = new ShopCategory();
        $this->data['nama_user'] = '';
        $this->data['id_user'] =  $sessionData['id'] ?? null;

        if ($this->user_id != null) {
            $this->data['member'] = $this->model['member']->find($this->user_id);
            $this->data['nama_user'] = $this->data['member']->nama;
        }
    }

    function keranjang($id_member)
    {
        $checkout = new Checkout();
        $keranjang = $checkout->keranjang($id_member);
        return response()->json(["keranjang" => $keranjang]);
    }

    public function index()
    {
        $products =  $this->model['products']->getShowproduct(35);
        $banners = $this->model['shop_banner']->getBannerbyTipe(1);
        $chill_banner =  $this->model['shop_banner']->getBannerbyTipe(2, 3);
        $this->data['id_user'] = $this->user_id;

        $banner_product_category = DB::table('product_category')->where('active', 'Y')->where('icon', '!=', '')->get();
        $promos = DB::table('promo_category')->where('active', 'Y')->where('display_status', 'show')->get();
        $product_search = $this->model['products']->getPencarianProdukwithlimit(4);
        $random_search = $this->model['products']->getRandomSerach();
        $categories = collect();
        $productsearch = collect();
        $stores = collect();

        return view('member.home.index', $this->data, [
            "products" => $products,
            "banners" => $banners,
            "random_search" => $random_search,
            "chill_banner" => $chill_banner,
            "banner_product_category" => $banner_product_category,
            "promos" => $promos,
            "product_search" => $product_search,
            "categories" => $categories,
            "stores" => $stores,
            "productsearch" => $productsearch
        ]);
    }

    function refreshHits()
    {
        $random_search = $this->model['products']->getRandomSerach();
        return response()->json([
            "status" => "success",
            "data" => $random_search,
        ]);
    }

    public function getPaginatedProducts(Request $request)
    {
        $products =  $this->model['products']->getShowproduct(35);

        if ($request->ajax()) {
            return view('member.home.product-list', compact('products'))->render();
        }

        return view('home.index', compact('products'));
    }

    public function getDetailproduct($id)
    {
        $this->data = $this->model['products']->getproductById($id);
        $this->data['id_user'] = $this->user_id;
        $produkToko = $this->model['products']->get5ProductByIdShop($this->data->idToko);
        $gambarProduct = $this->model['products']->getGambarProduct($id);


        if ($this->data['id_user'] != null) {
            $this->data['member'] = $this->model['member']->find($this->data['id_user']);
            $this->data['nama_user'] = $this->data['member']->nama;
        }

        // Store the array in the data property
        $this->data->produkToko = $produkToko;
        $this->data->gambarProduct = $gambarProduct;

        // return response()->json($this->data);
        return view('member.home.detail', $this->data);
    }

    public function ShowSeller($id_shop)
    {
        $where = [];
        $shopData = $this->model['shop']->getShopById($id_shop);
        $jmlhproduct = $this->model['products']->countProductByIdShop($id_shop, $where);
        $jmlhTerjualproduct = $this->model['products']->countproductTerjualbyId($id_shop);
        $etalsetoko = Etalase::getEtalasetoko($id_shop);

        $products = $this->model['products']->getProductByIdShop($id_shop);
        $productTerbaru = $this->model['products']->getProductTerbaruByIdshop($id_shop);

        $products->level1 = $this->model['products']->GetKategoryProductByIdshoplavel1($id_shop);
        $products->level2 = $this->model['products']->GetKategoryProductByIdshoplavel2($id_shop);
        $products->level3 = $this->model['products']->GetKategoryProductByIdshoplavel3($id_shop);

        if (isset($shopData->id_user)) {
            $member = $this->model['member']->find($shopData->id_user);
            $shopData->nama_user = $member->nama ?? '';
        }

        return view('member.home.seller', $this->data, [
            'shopData' => $shopData,
            'jmlhproduct' => $jmlhproduct,
            'jmlhTerjualproduct' => $jmlhTerjualproduct,
            'etalsetoko' => $etalsetoko,
            'products' => $products,
            'NewProduct' => $productTerbaru
        ]);
    }

    public function getProductsByEtalase($id_etalse)
    {
        $products = Products::getProductbyEtalase($id_etalse);
        return response()->json(["products" => $products]);
    }

    public function getProductsByIdshop($id_shop)
    {
        $products = Products::getProductByIdShop($id_shop);
        return response()->json(["products" => $products]);
    }

    public function GetKategoryProductByIdshop($id_shop)
    {
        $products = new \stdClass();
        $products->level1 = Products::GetKategoryProductByIdshoplavel1($id_shop);
        $products->level2 = Products::GetKategoryProductByIdshoplavel2($id_shop);
        $products->level3 = Products::GetKategoryProductByIdshoplavel3($id_shop);
        return response()->json(["products" => $products]);
    }
    public function GetProductByKategoriandIdShop($id_kategori, $id_shop)
    {
        $products = Products::GetProductByKategoriandIdShop($id_kategori, $id_shop);
        return response()->json(["products" => $products]);
    }

    public function dashboard()
    {
        $semuakondisi = ['belum', 'sudah', 'ulang', 'batal'];
        $statuses = ['baru', 'belumbayar', 'pengiriman', 'selesai', 'batal'];
        $results = [];
        foreach ($statuses as $status) {
            $results[$status] = Invoice::CountPesananUserbyIduser($this->user_id, $status);
        }
        foreach ($semuakondisi as $kondisi) {
            $results[$kondisi] = Nego::CountNegobyIdmember($this->user_id, $kondisi);
        }
        return response()->json([
            "pesanan" => $results['baru'],
            "pesananbelumbayar" => $results['belumbayar'],
            "dalampengiriman" => $results['pengiriman'],
            "pesananselesai" => $results['selesai'],
            "pesananbatal" => $results['batal'],
            "negobelum" => $results['belum'],
            "negosudah" => $results['sudah'],
            "negoulang" => $results['ulang'],
        ]);
    }

    function transaksi($kondisi)
    {
        if ($kondisi == 'semua') {
            $status = null;
        } elseif ($kondisi == 'butuhpersetujuan') {
            $status = 'waiting_approve_by_ppk';
        } elseif ($kondisi == 'disetujui') {
            $status = 'pending';
        } elseif ($kondisi == 'ditolak') {
            $status = 'cancel';
        } elseif ($kondisi == 'kirim') {
            $status = 'on_delivery';
        }

        // $detail = CompleteCartShop::getorderbyIdCart(630);


        $transaksi = Invoice::getOrderByIdmember($this->user_id, $status);

        foreach ($transaksi as $trans) {
            $detail = CompleteCartShop::getorderbyIdCart($trans->id_transaksi);

            foreach ($detail as $product) {
                $products = CompleteCartShop::getDetailProduct($product->id_shop, $product->id);
                $product->products = $products;
            }

            $trans->detail = $detail;
        }

        return response()->json(["transaksi" => $transaksi]);
    }





    // tester
    public function tampil()
    {
        $carts = new Calculation();
        $cart = $carts->OngkirSudahPPN(9000);
        return response()->json(["test" => $cart]);
    }


    public function fetchProducts()
    {
        $carts = new Cart();
        $cart = $carts->insertHandlingCost(702);
        return response()->json(["test" => $cart]);
    }


    public function fetchCompleteCartShop()
    {
        $carts = new Calculation();
        $dataArr = [
            'fee_nominal' => 3500,
            'fee_percent' => 0,
            'ppn' => 11,

            'sum_price' => 935000,
            'sum_price_ppn_only' => 935000,
            'sum_shipping' => 15900,
            // 'total_ppn' => $dcart->total_ppn,
            // 'total_non_ppn' => $dcart->total_non_ppn,
        ];
        $cart = $carts->calc_handling_cost($dataArr);
        return response()->json(["test" => $cart]);
    }

    function calc_nego(Request $request)
    {
        $qty        = $request->quantity;
        $id_product = $request->id_produk;
        $harga      = $request->nego_price;
        $note      = $request->note;

        $cf         = Lpse_config::first();
        $ppn        = $cf->ppn;

        $product = Products::Find($id_product);

        $id_kategori = $product->id_category;
        $idtipeProduk = $product->id_tipe;

        // PPh menggunkan default barang
        $pph            = 1.5;

        $checkPPh         = $this->model['ProductCategory']->jenisProduct($id_kategori);

        // check PPN
        $CheckppnProduct   = $this->model['ProductCategory']->check_ppn($id_product);
        $checkShop         = $this->model['shop']->getShopCategory($product->id_shop);

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
            'harga' => $harga,
            'ppn' => $ppn,
            'pph' => $pph,
        ];

        $calculation    = $this->Libraries['Calculation']->calc_nego_harga($dataArr);
        $SellerPrice    = $calculation['harga_vendor_final'];

        $harga_tayang = DB::table('lpse_price')->where('id_product', $id_product)->value('price_lpse');

        // save Nego
        $save = DB::table('nego')->insertGetId([
            'member_id' => $this->user_id,
            'id_shop' => $product->id_shop,
            'status' => 0,
            'complete_checkout' => 0,
            'status_nego' => 0,
            'created_date' => date('Y-m-d H:i:s'),
            'qty' => $qty,
            'harga_awal_satuan' => $harga_tayang,
            'harga_awal_total' => ($harga_tayang * $qty),
            'harga_input_seller' => $product->price,
        ]);

        // save product
        DB::table('product_nego')->insert([
            'id_nego' => $save,
            'id_product' => $id_product,
            'qty' => $qty,
            'harga_nego' => ($harga * $qty),
            'base_price' => $harga,
            'nominal_didapat' => ($calculation['harga_vendor_final'] * $qty),
            'status' => 0,
            'timestamp' => date('Y-m-d H:i:s'),
            'update_date' => date('Y-m-d H:i:s'),
            'catatan_pembeli' => $note,
            'send_by' => 0
        ]);

        return response()->json([
            'data' => $calculation,
            'error' => 0,
            'info' => 'Berhasil menghitung nego harga'
        ]);
    }
}
