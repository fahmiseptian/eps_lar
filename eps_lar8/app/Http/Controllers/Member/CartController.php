<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Libraries\Calculation;
use App\Models\Cart;
use App\Models\CartShop;
use App\Models\CartShopTemporary;
use App\Models\Invoice;
use App\Models\Lpse_config;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    protected $data;
    public function __construct(Request $request)
    {
        $this->data['carts'] = new CartShopTemporary();
        // Ambil semua data sesi
        $sessionData = $request->session()->all();
        $this->data['id_user'] = $sessionData['id'] ?? null;
    }
    // Metode lain dalam controller
    public function index() {
        $id_member=$this->data['id_user'];
        $cart = Cart::where('id_user',$id_member)->select('id','total','qty')->first();

        if ($cart) {
            $cart->detail = CartShop::getdetailcartByIdcart($cart->id);

            foreach ($cart->detail as $detail ) {
                $products =CartShopTemporary::getDetailCartByIdShop($detail->id_shop,$cart->id);
                $detail->products= $products;
            }

            // return response()->json(["cart"=>$cart]);
            return view('member.cart.index',$this->data,["cart"=>$cart]);
        } else{
            return view('member.cart.empty-cart',$this->data);
        }
    }

    public function updateQuantity(Request $request)
    {
        $id_cart = $request->input('id_cart');
        $id_cst = $request->input('id_cst');
        $id_cs = $request->input('id_cs');
        $action = $request->input('action');
        $quantity = $request->input('quantity');

        try {
            DB::transaction(function () use ($id_cart, $id_cst, $id_cs, $action, $quantity, &$newQuantity, &$remainingQuantity) {
                if ($action === 'decrease') {
                    DB::table('cart_shop_temporary')->where('id', $id_cst)->decrement('qty', 1);
                    DB::table('cart_shop')->where('id', $id_cs)->decrement('qty', 1);
                    DB::table('cart')->where('id', $id_cart)->decrement('qty', 1);
                } elseif ($action === 'increase') {
                    DB::table('cart_shop_temporary')->where('id', $id_cst)->increment('qty', 1);
                    DB::table('cart_shop')->where('id', $id_cs)->increment('qty', 1);
                    DB::table('cart')->where('id', $id_cart)->increment('qty', 1);
                } elseif ($action === 'change' && $quantity !== null) {
                    $oldQuantity = DB::table('cart_shop_temporary')->where('id', $id_cst)->value('qty');
                    $difference = $quantity - $oldQuantity;
                    DB::table('cart_shop_temporary')->where('id', $id_cst)->update(['qty' => $quantity]);
                    DB::table('cart_shop')->where('id', $id_cs)->increment('qty', $difference);
                    DB::table('cart')->where('id', $id_cart)->increment('qty', $difference);
                }

                $newQuantity = DB::table('cart_shop_temporary')->where('id', $id_cst)->value('qty');
                $stock = DB::table('product')->join('cart_shop_temporary', 'product.id', '=', 'cart_shop_temporary.id_product')
                            ->where('cart_shop_temporary.id', $id_cst)
                            ->value('product.stock');
                $remainingQuantity = $stock - $newQuantity;
            });

            return response()->json([
                'success' => true,
                'new_quantity' => $newQuantity,
                'remaining_quantity' => $remainingQuantity
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    function addCart(Request $request){

        $id_member=$this->data['id_user'];
        $id_product = $request->id_product;
        $qty = $request->qty;
        $id_shop= 0;

        $addcart =$this->data['carts']->updateTemporary($id_member,$id_product,$id_shop,$qty) ;

        return response()->json(["Masagge"=>$addcart]);
    }

    function deletecart($id_temporary,$id_shop) {
        $id_member=$this->data['id_user'];
        $deletecart = $this->data['carts']->deletecart($id_member,$id_temporary,$id_shop);
    }

    function checkout(){
        $carts = new Cart();
        $cf = Lpse_config::first();
        $payment = Payment::getpaymentActive();
        $id_member = $this->data['id_user'];
        $cart = $carts->getCart($id_member);
        $insert_handling_cost =$carts->insertHandlingCost($id_member);

        $this->data['cartAddress']  = $carts->getaddressCart($cart->id_address_user);
        $cart->detail = CartShop::getdetailcartByIdcart($cart->id);

        $total_barang_dengan_PPN = 0;
        $total_barang_tanpa_PPN = 0;
        $total_shipping = 0;
        $total_insurance = 0;
        $total_ppn_product = 0;
        $total_ppn_shipping = 0;
        $total_diskon = 0;

        foreach ($cart->detail as $detail ) {
            $pengiriman = $carts->getRates($cart->id,$detail->id_shop);
            $products = CartShopTemporary::getCartSelectedByIdShop($detail->id_shop,$cart->id);

            // Gabungkan pengiriman dan detail produk ke dalam satu objek
            $detail->pengiriman = $pengiriman;
            $detail->products = $products['carts'];
            $detail->total_barang_dengan_PPN = $products['total_barang_dengan_PPN'];
            $detail->total_barang_tanpa_PPN = $products['total_barang_tanpa_PPN'];

            // Tambahkan jumlah total barang dengan PPN dan tanpa PPN
            $total_barang_dengan_PPN += $detail->total_barang_dengan_PPN;
            $total_barang_tanpa_PPN += $detail->total_barang_tanpa_PPN;
            $total_shipping += $detail->sum_shipping;
            $total_insurance += $detail->insurance_nominal;
            $total_ppn_product += $detail->ppn_price;
            $total_ppn_shipping += $detail->ppn_shipping;
            $total_diskon += $detail->discount;
        }
        $cart->total_diskon = $total_diskon;
        $cart->total_barang_dengan_PPN = $total_barang_dengan_PPN;
        $cart->total_barang_tanpa_PPN = $total_barang_tanpa_PPN;
        $cart->total_shipping = $total_shipping;
        $cart->total_insurance = $total_insurance;
        $cart->total_ppn = $total_ppn_product + $total_ppn_shipping;
        $cart->ppn = $cf->ppn / 100;
        $cart->pph = $cf->pph / 100;
        $cart->payment = $payment;

        // Mengembalikan data cart yang sudah digabung
        // return response()->json(["cart"=>$cart]);
        return view('member.cart.checkout',$this->data,["cart"=>$cart]);
    }

    function getaddress() {
        $address = Member::getaddressbyIdMember($this->data['id_user']);
        return response()->json(["address"=>$address]);
    }

    function updateAddressCart($member_address_id) {
        $cart = Cart::getCart($this->data['id_user']);
        $updatecart = Cart::where('id', $cart->id)->update([
            'id_address_user' => $member_address_id
        ]);
    }

    function getOngkir($id_shipping,$id) {
        $calculation = new Calculation();
        $cartShop = new CartShop();
        $priceRecord = DB::table('shipping')->where('id', $id_shipping)->first('price');

        // Pastikan bahwa $priceRecord tidak null dan memiliki properti price
        if ($priceRecord && isset($priceRecord->price)) {
            $price = $priceRecord->price;
            $ongkir_akhir = $calculation->OngkirSudahPPN($price);

            $updatecart = CartShop::where('id', $id)->update([
                'id_shipping' => $id_shipping,
                'sum_shipping' => $ongkir_akhir['ongkir_sudah_ppn_dan_pph'],
                'ppn_shipping' => $ongkir_akhir['ppn_ongkir'],
                'pph_shipping' => $ongkir_akhir['pph_ongkir'],
                'base_price_shipping' => $ongkir_akhir['base_price'],
                // 'base_rate' => $ongkir_akhir['base_price'],
            ]);

            $shop = DB::table('cart_shop')->where('id', $id)->first('id_shop');
            $id_shop =$shop->id_shop;
            $cart = Cart::getCart($this->data['id_user']);
            $id_cart = $cart->id;

            $cartShop->refreshCartShop($id_cart, $id_shop);
            $cartShop->refreshCart($id_cart);

            return response()->json(["ongkir" => $id_cart]);
        } else {
            return response()->json(["error" => "Shipping price not found."], 404);
        }
    }

    function insurance($id_shop, $id_courier, $idcs, $status){
        $cart = new CartShop();
        $id_user = $this->data['id_user'];

        if ($status == 'add') {
            $status = true;
        }else {
            $status = false;
        }

        $is_insurance =  $cart->insurance($id_user,$id_shop,$id_courier,$status,$idcs);
        return response()->json([$is_insurance]);
    }

    function updatePayment(Request $request){
        $carts = new Cart();
        $id_member = $this->data['id_user'];
        $cart = $carts->getCart($id_member);
        $id_cart = $cart->id;
        $id_payment = $request->id_payment;
        $updatecart = $carts->where('id', $id_cart)->update([
            'id_payment' => $id_payment,
            'jml_top' => 0
        ]);
        if ($updatecart) {
            $insert_handling_cost =$carts->insertHandlingCost($id_member);
            $detail = $carts->where('id',$id_cart)->first();
            return response()->json(['payment' => $detail]);
        }else {
            return response()->json(['payment' => 'tidak ditemukan']);
        }
    }

    function updateTOP($top){
        $carts = new Cart();
        $cart = $carts->getCart($this->data['id_user']);
        $id_cart = $cart->id;
        $top = $top;
        $updatecart = $carts->where('id', $id_cart)->update([
            'jml_top' => $top
        ]);
    }

    function finish_checkout(Request $request){
        $complete_cart = new Invoice();
        $id_user = $this->data['id_user'];
        // $data_user = Member::getDataMember($id_user);
        $id_cart = $request->id_cart;

        $data 		= array('id_user' => $id_user, 'id' => $id_cart);
        // $migrate_checkout =$carts->migrate_checkout($data);
        $migrate_checkout = $complete_cart->migrate_cart_checkout_cond($id_user, $id_cart);


        if ($migrate_checkout) {
            return response()->json(['status' => 'success', 'id_cart' => $id_cart]);
		} else {
            return response()->json(['status' => 'failed', 'id_cart' => $id_cart]);
		}
    }

}
