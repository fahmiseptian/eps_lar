<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Libraries\Calculation;
use App\Models\Cart;
use App\Models\CartShop;
use App\Models\CartShopTemporary;
use App\Models\Member;
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
        $cart->detail = CartShop::getdetailcartByIdcart($cart->id);

        foreach ($cart->detail as $detail ) {
            $products =CartShopTemporary::getDetailCartByIdShop($detail->id_shop,$cart->id);
            $detail->products= $products;
        }

        // return response()->json(["cart"=>$cart]);
        return view('member.cart.index',$this->data,["cart"=>$cart]);
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
    
        $id_member=$this->data['id_user'];
        $cart = $carts->getCart($id_member);
        $this->data['cartAddress']  = $carts->getaddressCart($cart->id_address_user);
        $cart->detail = CartShop::getdetailcartByIdcart($cart->id);

        foreach ($cart->detail as $detail ) {
            $pengiriman = $carts->getRates($cart->id,$detail->id_shop);
            $products =CartShopTemporary::getCartSelectedByIdShop($detail->id_shop,$cart->id);
            $detail->products= $products;
            $detail->pengiriman= $pengiriman;
        }

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
            ]);



            return response()->json(["ongkir" => $ongkir_akhir]);
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
    
}
