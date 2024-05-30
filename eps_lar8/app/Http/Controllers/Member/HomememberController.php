<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Libraries\Calculation;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomememberController extends Controller
{
    protected $data;
    protected $user_id;

    public function __construct(Request $request)
    {
        // Ambil semua data sesi
        $sessionData = $request->session()->all();
        $this->user_id = $sessionData['id'] ?? null;
    }

    public function index() {
        $products= Products::getproduct();
        $this->data['id_user']=$this->user_id;

        // return response()->json(["products"=>$products]);
        return view('member.home.index',$this->data,["products"=>$products]);
    }

    public function getDetailproduct($id) {
        $this->data = Products::getproductById($id);
        $this->data['id_user']=$this->user_id;
        $produkToko = Products::get5ProductByIdShop($this->data->idToko);
        $gambarProductlain = [];
        $productlain = [];
        

        foreach ($produkToko as $produk) {
            // Check if $produk is an object, artwork_url_sm exists, is an array, and is not empty
            if (is_object($produk) && isset($produk->artwork_url_sm) && is_array($produk->artwork_url_sm) && !empty($produk->artwork_url_sm)) {
                // Add the first image from artwork_url_sm array and the product name to $gambarProductlain array
                $gambarProductlain[] =  $produk->artwork_url_sm[0];
                $productlain[] =  $produk->id;
            }
        }

        // Store the array in the data property
        $this->data->productlain = $gambarProductlain;
        $this->data->productidlain = $productlain;

        // return response()->json($this->data);
        return view('member.home.detail',$this->data);
    }

    public function ShowSeller($id_shop) {
        $where = [
        ];
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

        $products= Products::getProductByIdShop($id_shop);
        $productTerbaru = Products::getProductTerbaruByIdshop($id_shop);

        $products->level1 = Products::GetKategoryProductByIdshoplavel1($id_shop);
        $products->level2 = Products::GetKategoryProductByIdshoplavel2($id_shop);
        $products->level3 = Products::GetKategoryProductByIdshoplavel3($id_shop);

        // return response()->json($this->data);
        return view('member.home.seller', $this->data,["products"=>$products, "NewProduct"=>$productTerbaru]);
    }

    public function getProductsByEtalase($id_etalse) {
        $products= Products::getProductbyEtalase($id_etalse);
        return response()->json(["products"=>$products]);
    }

    public function getProductsByIdshop($id_shop) {
        $products= Products::getProductByIdShop($id_shop);
        return response()->json(["products"=>$products]);
    }

    public function GetKategoryProductByIdshop($id_shop) {
        $products = new \stdClass();
        $products->level1 = Products::GetKategoryProductByIdshoplavel1($id_shop);
        $products->level2 = Products::GetKategoryProductByIdshoplavel2($id_shop);
        $products->level3 = Products::GetKategoryProductByIdshoplavel3($id_shop);
        return response()->json(["products" => $products]);
    }
    public function GetProductByKategoriandIdShop($id_kategori,$id_shop) {
        $products = Products::GetProductByKategoriandIdShop($id_kategori,$id_shop);
        return response()->json(["products" => $products]);
    }

    public function dashboard(){
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

    function transaksi($kondisi){
        if ($kondisi == 'semua') {
            $status = null;
        }elseif ($kondisi == 'butuhpersetujuan') {
            $status = 'waiting_approve_by_ppk';
        }elseif ($kondisi == 'disetujui') {
            $status ='pending';
        }elseif ($kondisi == 'ditolak') {
            $status ='cancel';
        }elseif ($kondisi == 'kirim') {
            $status ='on_delivery';
        }

        $transaksi = Invoice::getOrderByIdmember($this->user_id,$status);
        
        foreach ($transaksi as $trans) {
            $detail = CompleteCartShop::getorderbyIdCart($trans->id_transaksi);
            
            foreach ($detail as $product) {
                $products = CompleteCartShop::getDetailProduct($product->id_shop,$product->id);
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
        $cart = $carts->getOngkir(1005);
        return response()->json(["test" =>$cart ]);
    }


    public function fetchProducts()
    {
        $carts = new Cart();
        $cart =$carts->_sap_get_rates('SS0521','JB1209',1);
        return response()->json(["test" =>$cart ]);
    }


public function fetchCompleteCartShop()
{
    // $cart = Cart::getCartDetails($id_cart,$id_shop);
    $tess = new Calculation;
    $dataArr = array(
        'id_courier' => 1,
        'sum_price' => 82880,
    );
    $test = $tess->calcShippingInsuranceCost($dataArr,true);
    return response()->json($test);

}
}