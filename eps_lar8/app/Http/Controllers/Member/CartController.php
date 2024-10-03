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
    protected $model;
    public function __construct(Request $request)
    {
        $this->model['CartShopTemporary'] = new CartShopTemporary();
        $this->model['CartShop'] = new CartShop();
        $this->model['member'] = new Member();
        $this->model['Cart'] = new Cart();

        // Ambil semua data sesi
        $sessionData = $request->session()->all();
        $this->data['id_user'] = $sessionData['id'] ?? null;

        $this->data['nama_user'] = '';

        if ($this->data['id_user'] != null) {
            $this->data['member'] = $this->model['member']->find($this->data['id_user']);
            $this->data['nama_user'] = $this->data['member']->nama;
        }
    }
    // Metode lain dalam controller
    public function index()
    {
        $id_member = $this->data['id_user'];
        // $id_cart = $this->data['Cart']->getIdCartbyidmember($id_member);
        $cart = Cart::where('id_user', $id_member)->select('id', 'total', 'qty')->first();

        if ($cart) {
            if ($cart->qty > 0) {
                $cart->qty = $this->model['CartShopTemporary']->sumqtySelected($cart->id);
                $cart->detail = $this->model['CartShop']->getdetailcartByIdcart($cart->id);

                foreach ($cart->detail as $detail) {
                    $products = $this->model['CartShopTemporary']->getDetailCartByIdShop($detail->id_shop, $cart->id);
                    $detail->products = $products;
                }

                $sumprice = $this->model['CartShopTemporary']->sumPriceSelectProductCart($cart->id);
                $cart->sumprice = $sumprice;

                // return response()->json(["cart"=>$cart]);
                return view('member.cart.keranjang', $this->data, ["cart" => $cart]);
            }
            return view('member.cart.empty-cart', $this->data);
        }
        return view('member.cart.empty-cart', $this->data);
    }

    function updateProductCart(Request $request)
    {
        $id_user = $this->data['id_user'];
        $id_cst = $request->id_cst;
        $qty = $request->qty;

        $cst = $this->model['CartShopTemporary']->find($id_cst);
        if (!$cst) {
            return response()->json(['message' => 'Product Cart Tidak ditemukan'], 404);
        }
        $hasil = $this->model['CartShopTemporary']->updateTemporaryById($id_user, $cst->id_shop, $id_cst, $qty);
        $total_product = DB::table('cart_shop_temporary')->where('id', $id_cst)->value('total');

        return response()->json(['success' => true, 'total' =>  $total_product, 'hasil' => $hasil]);
    }

    function updateqtyCart(Request $request)
    {
        $id_user = $this->data['id_user'];
        $cartShop = $this->model['CartShop'];

        $id_cst = $request->id_cst;
        $action = $request->action;
        $quantity = $request->quantity;

        $cst = $this->model['CartShopTemporary']->find($id_cst);
        if (!$cst) {
            return response()->json(['message' => 'Product Cart Tidak ditemukan'], 404);
        }

        try {
            $hasil = null;
            DB::transaction(function () use ($id_user, $cst, $id_cst, $action, $quantity, &$hasil) {
                if ($action == 'decrease') {
                    $newQty = $cst->qty - 1;
                    $dataArr = [
                        'qty' => $newQty,
                    ];
                    $hasil = $this->model['CartShopTemporary']->updateTemporaryById($id_user, $cst->id_shop, $id_cst, $newQty);
                } elseif ($action == 'increase') {
                    $newQty = $cst->qty + 1;
                    $dataArr = [
                        'qty' => $newQty,
                    ];
                    $hasil = $this->model['CartShopTemporary']->updateTemporaryById($id_user, $cst->id_shop, $id_cst, $newQty);
                } elseif ($action == 'custom') {
                    $dataArr = [
                        'qty' => $quantity,
                    ];
                    $hasil = $this->model['CartShopTemporary']->updateTemporaryById($id_user, $cst->id_shop, $id_cst, $quantity);
                }
            });

            return response()->json([
                'success' => true,
                'hasil' => $hasil
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    function addCart(Request $request)
    {

        $id_member = $this->data['id_user'];
        $id_product = $request->id_product;
        $qty = $request->qty;

        $id_address = DB::table('member_address')
            ->where('member_id', $id_member)
            ->where('is_default_shipping', 'yes')
            ->value('member_address_id');

        if (empty($id_address)) {
            return response()->json(['message' => 'Alamat tidak ditemukan'], 404);
        }
        $id_cart = $this->model['Cart']->getIdCartbyidmember($id_member);

        $addcart = $this->model['CartShopTemporary']->CheckCart($id_cart, $id_product, $id_member, $qty);

        return response()->json(["Masagge" => 'Berhasil Memasukan Product Ke Keranjang']);
    }

    function deletecart($id_temporary, $id_shop)
    {
        $id_member = $this->data['id_user'];
        $deletecart = $this->model['CartShopTemporary']->deletecart($id_member, $id_temporary, $id_shop);
    }

    function checkout()
    {
        $carts = new Cart();
        $cf = Lpse_config::first();
        $payment = Payment::getpaymentActive();
        $id_member = $this->data['id_user'];
        $cart = $carts->getCart($id_member);
        $insert_handling_cost = $carts->insertHandlingCost($id_member);

        $this->data['cartAddress']  = $carts->getaddressCart($cart->id_address_user);
        $cart->detail = $this->model['CartShop']->Detailcsis_selected($cart->id);

        $this->data['provinces'] = DB::table('province')->get();

        $total_barang_dengan_PPN = 0;
        $total_barang_tanpa_PPN = 0;
        $total_shipping = 0;
        $total_insurance = 0;
        $total_ppn_product = 0;
        $total_ppn_shipping = 0;
        $total_diskon = 0;

        foreach ($cart->detail as $detail) {
            $pengiriman = $carts->getRates($cart->id, $detail->id_shop);
            $products = $this->model['CartShopTemporary']->getCartSelectedByIdShop($detail->id_shop, $cart->id);

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

        $this->data['satker_ppk'] =  $this->model['member']->getlimitppk($this->data['id_user']);


        // Mengembalikan data cart yang sudah digabung
        // return response()->json([$cart]);
        return view('member.cart.v_checkout', $this->data, ["cart" => $cart]);
    }

    function getaddress()
    {
        $address = Member::getaddressbyIdMember($this->data['id_user']);
        return response()->json(["address" => $address]);
    }

    function updateAddressCart($member_address_id)
    {
        $cart = Cart::getCart($this->data['id_user']);
        $updatecart = Cart::where('id', $cart->id)->update([
            'id_address_user' => $member_address_id
        ]);
    }

    function getOngkir($id_shipping, $id)
    {
        $calculation = new Calculation();
        $cartShop = $this->model['CartShop'];
        $priceRecord = DB::table('shipping')->where('id', $id_shipping)->first('price');

        // Pastikan bahwa $priceRecord tidak null dan memiliki properti price
        if ($priceRecord && isset($priceRecord->price)) {
            $price = $priceRecord->price;
            $ongkir_akhir = $calculation->OngkirSudahPPN($price);

            $updatecart = $this->model['CartShop']->where('id', $id)->update([
                'id_shipping' => $id_shipping,
                'sum_shipping' => $ongkir_akhir['ongkir_sudah_ppn_dan_pph'],
                'ppn_shipping' => $ongkir_akhir['ppn_ongkir'],
                'pph_shipping' => $ongkir_akhir['pph_ongkir'],
                'base_price_shipping' => $ongkir_akhir['base_price'],
                'base_rate' => $ongkir_akhir['base_price'],
                // 'base_rate' => $ongkir_akhir['base_price'],
            ]);

            $shop = DB::table('cart_shop')->where('id', $id)->first('id_shop');
            $id_shop = $shop->id_shop;
            $cart = Cart::getCart($this->data['id_user']);
            $id_cart = $cart->id;
            $id_courier = DB::table('shipping')->where('id', $id_shipping)->value('id_courier');

            $this->model['CartShop']->insurance($this->data['id_user'], $id_shop, $id_courier, 'false', $id);

            $cartShop->refreshCartShop($id_cart, $id_shop);
            $cartShop->refreshCart($id_cart);

            return response()->json(["ongkir" => $ongkir_akhir]);
        } else {
            return response()->json(["error" => "Shipping price not found."], 404);
        }
    }

    function insurance($id_shop, $id_courier, $idcs, $status)
    {
        $cart = $this->model['CartShop'];
        $id_user = $this->data['id_user'];

        if ($status == 'add') {
            $status = true;
        } else {
            $status = false;
        }

        $is_insurance =  $cart->insurance($id_user, $id_shop, $id_courier, $status, $idcs);
        return response()->json([$is_insurance]);
    }

    function updatePayment(Request $request)
    {
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
            $insert_handling_cost = $carts->insertHandlingCost($id_member);
            $detail = $carts->where('id', $id_cart)->first();
            return response()->json([
                'code' => 200,
                'payment' => $detail,
                'success' => true,
            ]);
        }
        return response()->json([
            'code' => 400,
            'payment' => 'payment tidak ditemukan',
            'success' => false,
        ]);
    }

    function updateTOP($top)
    {
        $carts = new Cart();
        $cart = $carts->getCart($this->data['id_user']);
        $id_cart = $cart->id;
        $top = $top;
        $updatecart = $carts->where('id', $id_cart)->update([
            'jml_top' => $top
        ]);
    }

    function finish_checkout(Request $request)
    {
        $complete_cart = new Invoice();
        $id_user = $this->data['id_user'];
        // $data_user = Member::getDataMember($id_user);
        $id_cart = $request->id_cart;
        $status = $request->status;

        $id_payment = DB::table('cart')->where('id', $id_cart)->value('id_payment');

        if ($id_payment == 30) {
            // BCA Virtual Account
        } elseif ($id_payment == 22) {
            //  Midtrans KKP
        } elseif ($id_payment == 31) {
            // BNI Virtual Account
        }

        $data         = array('id_user' => $id_user, 'id' => $id_cart);
        // $migrate_checkout =$carts->migrate_checkout($data);
        if ($status != null) {
            $migrate_checkout = $complete_cart->migrate_cart_checkout_cond($id_user, $id_cart, true);
        } else {
            $migrate_checkout = $complete_cart->migrate_cart_checkout_cond($id_user, $id_cart);
        }
        if ($migrate_checkout) {
            return response()->json(['status' => 'success', 'id_cart' => $id_cart]);
        } else {
            return response()->json(['status' => 'failed', 'id_cart' => $id_cart]);
        }
    }

    function updateIsSelectProduct(Request $request)
    {
        $id_cart = $request->id_cart;
        $id_cst = $request->id_cst;
        $cst = $this->model['CartShopTemporary']->find($id_cst);
        $cs = DB::table('cart_shop')->where('id_cart', $id_cart)->where('id_shop', $cst->id_shop)->first();
        if (!$cst) {
            return response()->json(['message' => 'Product Cart Tidak ditemukan'], 404);
        }
        $update_cart_shop = $this->model['CartShopTemporary']->UpdateShopCart($id_cart, $cst->id_shop);
        $update_cart = $this->model['CartShopTemporary']->updateCart($id_cart);

        if ($cst->is_selected == 'Y') {
            $cst->is_selected = 'N';
            $cst->save();
            $qty_total = $cs->qty - 1;
            $cs->qty = $qty_total;
            DB::table('cart_shop')->where('id_cart', $id_cart)->where('id_shop', $cst->id_shop)->update(['qty' => $qty_total]);
        } else {
            $cst->is_selected = 'Y';
            $cst->save();
            $qty_total = $cs->qty + 1;
            $cs->qty = $qty_total;
            DB::table('cart_shop')->where('id_cart', $id_cart)->where('id_shop', $cst->id_shop)->update(['qty' => $qty_total]);
        }
        $sumprice = $this->model['CartShopTemporary']->sumPriceSelectProductCart($id_cart);
        $totalqty = $this->model['CartShopTemporary']->sumqtySelected($id_cart);

        $carts = [
            'sumprice' => $sumprice,
            'is_selected' => $cst->is_selected,
            'qty' => $totalqty,
            'id_shop' => $cst->id_shop,
        ];

        return response()->json(['carts' => $carts], 200);
    }

    function updateIsSelectShop(Request $request)
    {
        $id_cart = $request->id_cart;
        $id_shop = $request->id_shop;
        $isSelected = $request->is_selected;

        DB::table('cart_shop_temporary')->where('id_cart', $id_cart)->where('id_shop', $id_shop)->update(['is_selected' => $isSelected]);
        $update_cart_shop = $this->model['CartShopTemporary']->UpdateShopCart($id_cart, $id_shop);
        $update_cart = $this->model['CartShopTemporary']->updateCart($id_cart);

        $sumprice = $this->model['CartShopTemporary']->sumPriceSelectProductCart($id_cart);
        $totalqty = $this->model['CartShopTemporary']->sumqtySelected($id_cart);

        $carts = [
            'sumprice' => $sumprice,
            'qty' => $totalqty,
        ];

        return response()->json(['carts' => $carts], 200);
    }

    function UpdateNote(Request $request)
    {
        $id_cart_shop = $request->input('id_cs');
        $note = $request->input('note');
        $tipe = $request->input('tipe');

        $update = DB::table('cart_shop')->where('id', $id_cart_shop)->update([
            $tipe => $note,
        ]);

        if ($update) {
            return response()->json([
                'code' =>  200,
                'massage' =>  'Berhasil Mengupdate catatan',
                'success' => true,
            ]);
        }
        return response()->json([
            'code' =>  400,
            'massage' =>  'gagal memngupdate catatan',
            'success' => false,
        ]);
    }
}
