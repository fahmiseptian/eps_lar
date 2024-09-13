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
use App\Models\Products;
use App\Models\Shop;
use App\Models\Shop_courier;
use App\Models\ShopBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomememberController extends Controller
{
    protected $data;
    protected $user_id;
    protected $model;

    public function __construct(Request $request)
    {
        // Ambil semua data sesi
        $sessionData = $request->session()->all();
        $this->user_id = $sessionData['id'] ?? null;

        $this->model['products'] = new Products();
        $this->model['shop_banner'] = new ShopBanner();
        $this->model['member'] = new Member();
        $this->data['nama_user'] = '';

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
        $this->data = Shop::getShopById($id_shop);
        if (is_object($this->data)) {
            $this->data = (array) $this->data;
        }
        $jmlhproduct = Products::countProductByIdShop($id_shop, $where);
        $jmlhTerjualproduct = Products::countproductTerjualbyId($id_shop);
        $etalsetoko = Etalase::getEtalasetoko($id_shop);
        // Buat array data toko
        $dataToko = [
            'jmlhproduct' => $jmlhproduct,
            'jmlhTerjualproduct' => $jmlhTerjualproduct,
            'etalsetoko' => $etalsetoko,
        ];
        $this->data = array_merge($this->data, $dataToko);

        $products = Products::getProductByIdShop($id_shop);
        $productTerbaru = Products::getProductTerbaruByIdshop($id_shop);

        $products->level1 = Products::GetKategoryProductByIdshoplavel1($id_shop);
        $products->level2 = Products::GetKategoryProductByIdshoplavel2($id_shop);
        $products->level3 = Products::GetKategoryProductByIdshoplavel3($id_shop);

        if ($this->data['id_user'] != null) {
            $this->data['member'] = $this->model['member']->find($this->data['id_user']);
            $this->data['nama_user'] = $this->data['member']->nama;
        }

        // return response()->json($this->data);
        return view('member.home.seller', $this->data, ["products" => $products, "NewProduct" => $productTerbaru]);
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
        $carts = new Cart();
        $cart = $carts->getProductDetail(284);
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
}
