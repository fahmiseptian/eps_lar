<?php

namespace App\Models;

use App\Libraries\Calculation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CartShopTemporary extends Model
{
    protected $table = 'cart_shop_temporary';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function getDetailCartByIdShop($id_shop, $id_cart)
    {
        $carts = DB::table('cart_shop_temporary as cst')
            ->select(
                's.nama_pt as nama_seller',
                'cst.nama as nama_product',
                'cst.image as gambar_product',
                'cst.id as id_cst',
                'cst.*',
                'p.stock'
            )
            ->join('shop as s', 'cst.id_shop', '=', 's.id')
            ->join('product as p', 'cst.id_product', '=', 'p.id')
            ->where('cst.id_shop', $id_shop)
            ->where('cst.id_cart', $id_cart)
            ->get();
        return $carts;
    }

    function sumPriceSelectProductCart($id_cart)
    {
        $sumprice = DB::table('cart_shop_temporary as cst')
            ->where('cst.id_cart', $id_cart)
            ->where('cst.is_selected', 'Y')
            ->sum('cst.total');
        return $sumprice;
    }

    function sumqtySelected($id_cart)
    {
        $qty = DB::table('cart_shop_temporary as cst')
            ->where('cst.id_cart', $id_cart)
            ->where('cst.is_selected', 'Y')
            ->count();
        return $qty;
    }

    public function getCartSelectedByIdShop($id_shop, $id_cart)
    {
        $carts = DB::table('cart_shop_temporary as cst')
            ->select(
                's.nama_pt as nama_seller',
                'cst.nama as nama_product',
                'cst.image as gambar_product',
                'cst.id as id_cst',
                'cst.*',
                'p.stock',
            )
            ->join('shop as s', 'cst.id_shop', '=', 's.id')
            ->join('product as p', 'cst.id_product', '=', 'p.id')
            ->where('cst.id_shop', $id_shop)
            ->where('cst.id_cart', $id_cart)
            ->where('cst.is_selected', 'Y')
            ->get();

        // Hitung jumlah total barang dengan PPN dan tanpa PPN
        $total_barang_dengan_PPN = 0;
        $total_barang_tanpa_PPN = 0;

        foreach ($carts as $cart) {
            if ($cart->val_ppn != 0) {
                $total_barang_dengan_PPN += $cart->total_non_ppn;
            } else {
                $total_barang_tanpa_PPN += $cart->total_non_ppn;
            }
        }

        // Tambahkan total barang dengan dan tanpa PPN ke hasil
        $result = [
            'carts' => $carts,
            'total_barang_dengan_PPN' => $total_barang_dengan_PPN,
            'total_barang_tanpa_PPN' => $total_barang_tanpa_PPN,
        ];

        return $result;
    }



    public function updateTemporary($id_user, $id_product, $id_shop, $qty = null)
    {
        $id_cart = Cart::getIdCartbyidmember($id_user);
        $productData = Products::select(
            'products.*',
            'lp.price_lpse as price'
        )
            ->join('lpse_price as lp', 'lp.id_product', 'products.id')
            ->where('products.status_delete', 'N')
            ->where('products.status', 'active')
            ->where('products.stock', '!=', 0)
            ->where('products.id', $id_product);
        $query = $productData->get();
        $count     = $query->count();
        $p         = $query->first();


        // CEK QTY PRODUCT DI KERANJANG
        $data = DB::table('cart_shop_temporary')
            ->select('qty')
            ->where('id_product', $id_product)
            ->where('id_cart', $id_cart);
        $cek         = $data->get()->first();
        $sum_qty = ($cek && isset($cek->qty)) ? $cek->qty + $qty : $qty;

        // JIKA PRODUCT TERSEDIA & TIDAK MELEBIHI STOK
        if ($p->weight == 0) {
            $p = '203';
            return $p;
        }

        // JIKA PRODUCT TERSEDIA & TIDAK MELEBIHI STOK
        if ($p->id_shop == $id_shop) {
            $p = '202';
            return $p;
        }

        if (($sum_qty < $p->stock + 1) && ($p->id_shop != $id_shop)) {

            // cek nego
            $nego = $this->getDataNego($id_user, $id_product, $qty);
            if ($nego && ($nego->n_id_product == $id_product) && ($nego->n_qty == $qty)) {
                $temporary = $this->cartTemporary($id_cart, $p->id_shop, $p->id, $nego->n_price, $p->weight, $id_user, $qty, true);
            } else {
                $temporary = $this->cartTemporary($id_cart, $p->id_shop, $p->id, $p->price, $p->weight, $id_user, $qty);
            }
            $update_cart_shop    =     $this->updateCartShop($id_cart, $p->id_shop);
            $update_cart        =     $this->updateCart($id_cart);
            return $p;
        }
    }

    public function getDataNego($id_user, $id_product, $qty)
    {
        $data = DB::table('nego as n')
            ->select('pn.id_product as n_id_product', 'pn.qty as n_qty', 'pn.base_price as n_price')
            ->join('product_nego as pn', 'pn.id_nego', '=', 'n.id')
            ->where('n.member_id', $id_user)
            ->where('n.status', '1')
            ->where('pn.status', '1')
            ->where('pn.qty', $qty)
            ->where('pn.id_product', $id_product)
            ->where('n.complete_checkout', '0')
            ->first();
        if ($data) {
            return $data;
        }
        return false;
    }

    public function cartTemporary($id_cart, $id_shop, $id_product, $price, $weight, $id_user, $qty = null, $is_nego = null)
    {
        $existingCart = DB::table('cart_shop_temporary')
            ->select('id', 'qty')
            ->where('id_cart', $id_cart)
            ->where('id_product', $id_product);
        $q        =    $existingCart->get();
        $query    =    $q->first();

        if (empty($qty)) {
            $qty = 1;
        }

        if ($q->count() == 1) {
            $qty_check = $qty + $query->qty;

            // Note tidak ada di keranjang
            $data = DB::table('nego as n')
                ->select(
                    'n.*',
                    'n.status as n_status',
                    'pn.*',
                    'pn.status as pn_status'
                )
                ->join('product_nego as pn', 'pn.id_nego', 'n.id')
                ->where('n.member_id', $id_user)
                ->where('n.status', '1')
                ->where('pn.status', '1')
                ->where('n.complete_checkout', '0')
                ->where('pn.id_product', $id_product)
                ->where('pn.qty', $qty);

            $n             = $data->get();
            $n_count      = $n->count();
            $nego         = $n->first();

            // cek barang PPN atau tidak
            $getLpse     = Lpse_config::first();
            $getProduct = $this->getProductDetail($id_product);
            $ppn = $getLpse->ppn;
            $pph = $getLpse->pph;
            if ($getProduct->barang_kena_ppn == 0) {
                $ppn = '0';
            }

            // update
            $promo_price = $this->getPromoPrice($id_product);
            $flash_price = $this->getFlashPrice($id_product, $qty);
            $lpse_price  = $this->getLpsePrice($id_product);

            $nama                 = $getProduct->name;
            $image                 = $getProduct->artwork_url_sm[0];
            $input_price        = $getProduct->price;
            $fee_mp_percent        = $getLpse->fee_mp_percent;
            $fee_mp_nominal        = $getLpse->fee_mp_nominal;
            $fee_pg_percent        = $getLpse->fee_pg_percent;
            $fee_pg_nominal        = $getLpse->fee_pg_nominal;
            $tot_fee_perc        = $fee_mp_percent + $fee_pg_percent;
            $tot_fee_nom        = $fee_mp_nominal + $fee_pg_nominal;

            // $tot_fee             = round($input_price * ($tot_fee_perc/100)) + ($tot_fee_nom + $tot_fee_nom * ($ppn / 100));
            $harga_dasar_lpse     = round($lpse_price / (1 + ($ppn / 100)));
            // $ppn_nom        	= $harga_dasar_lpse * ($ppn / 100);
            // $pph_nom        	= $harga_dasar_lpse * ($pph / 100);

            $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
            $pph_nom            = round($harga_dasar_lpse * ($pph / 100));

            $tot_hp              = $harga_dasar_lpse + $ppn_nom;

            $id_temporary     = $query->id;
            if ($is_nego && $n_count > 0 && $nego->base_price > 0) {
                // NOTE jika ada nego
                $harga_dasar_lpse = 0;
                $harga_dasar_lpse_satuan = 0;

                if ($qty >= 1) {
                    $harga_dasar_lpse_satuan = round($nego->harga_nego / $nego->qty);
                    $harga_dasar_lpse_exc     = round($harga_dasar_lpse_satuan / (1 + ($ppn / 100)));
                    $harga_dasar_lpse     = $harga_dasar_lpse_exc * $qty;
                } else {
                    $harga_dasar_lpse_satuan = $nego->harga_nego;
                    $harga_dasar_lpse     = round($nego->harga_nego / (1 + ($ppn / 100)));
                }

                $input_price         = $nego->nominal_didapat / $nego->qty;
                $harga_tayang         = $nego->base_price;
                // $ppn_nom 			= $nego->harga_nego - $harga_dasar_lpse;
                // $pph_nom 			= ($pph / 100) * $harga_dasar_lpse;

                $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
                $pph_nom            = round($harga_dasar_lpse * ($pph / 100));

                $insertData = [
                    'id_nego' => $nego->id_nego,
                    'input_price' => $input_price,
                    'price' => $harga_tayang,
                    'qty' => $qty,
                    'nominal_ppn' => DB::raw($ppn_nom),
                    'nominal_pph' => DB::raw($pph_nom),
                    'val_ppn' => $ppn,
                    'val_pph' => $pph,
                    'total_non_ppn' => $harga_dasar_lpse,
                    'harga_dasar_lpse' => DB::raw('total_non_ppn / qty'),
                    'total' =>  $nego->harga_nego
                ];
            } else {
                // NOTE tanpa ada nego

                // echo "p1-"; exit();
                if ($flash_price > 0) { // && $et !=null
                    // jika produk terdapat harga (flashsale & promo) atau (flashsale)
                    $price_non_ppn     = $flash_price;

                    $input_price         = $this->getFlashOriginPrice($id_product, $qty);
                    $harga_dasar_lpse     = round($price_non_ppn / (1 + ($ppn / 100)));
                    // $ppn_nom        	= $harga_dasar_lpse * ($ppn / 100);
                    // $pph_nom        	= $harga_dasar_lpse * ($pph / 100);

                    $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
                    $pph_nom            = round($harga_dasar_lpse * ($pph / 100));

                    $tot_hp              = $harga_dasar_lpse + $ppn_nom;
                } elseif ($promo_price > 0) {
                    // jika produk terdapat harga promo saja
                    $price_non_ppn     = $promo_price;

                    $input_price         = $this->getPromoOriginPrice($id_product);
                    $harga_dasar_lpse     = round($price_non_ppn / (1 + ($ppn / 100)));
                    // $ppn_nom        	= $harga_dasar_lpse * ($ppn / 100);
                    // $pph_nom        	= $harga_dasar_lpse * ($pph / 100);

                    $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
                    $pph_nom            = round($harga_dasar_lpse * ($pph / 100));

                    $tot_hp              = $harga_dasar_lpse + $ppn_nom;
                } elseif ($lpse_price > 0) {
                    // jika produk terdapat harga lpse
                    $price_non_ppn     = $lpse_price; // ($lpse_price / (1 + ($ppn / 100)));
                } else {
                    $price_non_ppn     = $price;
                }

                if (!empty($qty)) {
                    $insertData = [
                        'qty' => DB::raw('qty + $qty'),
                    ];
                } else {
                    $insertData = [
                        'qty' => DB::raw('qty + 1'),
                    ];
                }
                $insertData = [
                    'price' => $price_non_ppn,
                    'input_price' => $input_price,
                    'nominal_ppn' => DB::raw("$ppn_nom * qty"),
                    'nominal_pph' => DB::raw("$pph_nom * qty"),
                    'harga_dasar_lpse' => $harga_dasar_lpse,
                    'total_non_ppn' => DB::raw("harga_dasar_lpse * qty"),
                    'total' => DB::raw("price * qty"),
                    'id_nego' => null,
                ];
            }
            $insertData = [
                'nama' => $nama,
                'image' => $image,
                'fee_mp_percent' => $fee_mp_percent,
                'fee_mp_nominal' => $fee_mp_nominal,
                'fee_pg_percent' => $fee_pg_percent,
                'fee_pg_nominal' => $fee_pg_nominal,
                'val_ppn' => $ppn,
                'val_pph' => $pph,
                'total_weight' => DB::raw('weight * qty'),
                'id_shop' => $id_shop,
                'is_selected' => 'Y'
            ];

            $update_temporary = DB::table('cart_shop_temporary')
                ->where('id', $id_temporary)
                ->update($insertData);
            if ($update_temporary) {
                return true;
            }
        } else {
            // NOTE Jika tidak ada product di keranjang
            // Note tidak ada di keranjang
            $data = DB::table('nego as n')
                ->select(
                    'n.*',
                    'n.status as n_status',
                    'pn.*',
                    'pn.status as pn_status'
                )
                ->join('product_nego as pn', 'pn.id_nego', 'n.id')
                ->where('n.member_id', $id_user)
                ->where('n.status', '1')
                ->where('pn.status', '1')
                ->where('n.complete_checkout', '0')
                ->where('pn.id_product', $id_product)
                ->where('pn.qty', $qty);

            $n             = $data->get();
            $n_count      = $n->count();
            $nego         = $n->first();

            // cek barang PPN atau tidak
            $getLpse     = Lpse_config::first();
            $getProduct = $this->getProductDetail($id_product);
            $ppn = $getLpse->ppn;
            $pph = $getLpse->pph;
            if ($getProduct->barang_kena_ppn == 0) {
                $ppn = '0';
            }

            // update
            $promo_price = $this->getPromoPrice($id_product);
            $flash_price = $this->getFlashPrice($id_product, $qty);
            $lpse_price  = $this->getLpsePrice($id_product);
            $insertData = [
                'id_nego' => null,
                'id_cart' => $id_cart,
                'id_product' => $id_product
            ];

            $nama                 = $getProduct->name;
            $image                 = $getProduct->artwork_url_sm[0];
            $input_price        = $getProduct->price;
            $fee_mp_percent        = $getLpse->fee_mp_percent;
            $fee_mp_nominal        = $getLpse->fee_mp_nominal;
            $fee_pg_percent        = $getLpse->fee_pg_percent;
            $fee_pg_nominal        = $getLpse->fee_pg_nominal;
            $tot_fee_perc        = $fee_mp_percent + $fee_pg_percent;
            $tot_fee_nom        = $fee_mp_nominal + $fee_pg_nominal;

            // $tot_fee             = round($input_price * ($tot_fee_perc/100)) + ($tot_fee_nom + $tot_fee_nom * ($ppn / 100));
            $harga_dasar_lpse     = round($lpse_price / (1 + ($ppn / 100)));
            // $ppn_nom        	= $harga_dasar_lpse * ($ppn / 100);
            // $pph_nom        	= $harga_dasar_lpse * ($pph / 100);

            $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
            $pph_nom            = round($harga_dasar_lpse * ($pph / 100));

            $tot_hp              = $harga_dasar_lpse + $ppn_nom;

            if ($is_nego && $n_count > 0 && $nego->base_price > 0) {
                $harga_dasar_lpse = 0;
                $harga_dasar_lpse_satuan = 0;

                if ($qty >= 1) {
                    $harga_dasar_lpse_satuan = round($nego->harga_nego / $nego->qty);
                    $harga_dasar_lpse_exc     = round($harga_dasar_lpse_satuan / (1 + ($ppn / 100)));
                    $harga_dasar_lpse     = $harga_dasar_lpse_exc * $qty;
                } else {
                    $harga_dasar_lpse_satuan = $nego->harga_nego;
                    $harga_dasar_lpse     = round($nego->harga_nego / (1 + ($ppn / 100)));
                }

                $input_price         = $nego->nominal_didapat / $nego->qty;
                $harga_tayang         = $nego->base_price;
                // $ppn_nom 			= $nego->harga_nego - $harga_dasar_lpse;
                // $pph_nom 			= ($pph / 100) * $harga_dasar_lpse;
                $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
                $pph_nom            = round($harga_dasar_lpse * ($pph / 100));

                $insertData = [
                    'id_nego' => $nego->id_nego,
                    'input_price' => $input_price,
                    'price' => $harga_tayang,
                    'qty' => $qty,
                    'nominal_ppn' => $ppn_nom,
                    'nominal_pph' => $pph_nom,
                    'val_ppn' => $ppn,
                    'val_pph' => $pph,
                    'total_non_ppn' => $harga_dasar_lpse,
                    'harga_dasar_lpse' => DB::raw('ROUND(total_non_ppn / qty)'),
                    'total' => $nego->harga_nego
                ];
            } else {
                // echo "p2-"; exit();
                if ($flash_price > 0) { //
                    // jika produk terdapat harga (flashsale & promo) atau (flashsale)
                    $price_non_ppn     = $flash_price;

                    $input_price         = $this->getFlashOriginPrice($id_product, $qty);
                    $harga_dasar_lpse     = round($price_non_ppn / (1 + ($ppn / 100)));
                    // $ppn_nom        	= $harga_dasar_lpse * ($ppn / 100);
                    // $pph_nom        	= $harga_dasar_lpse * ($pph / 100);

                    $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
                    $pph_nom            = round($harga_dasar_lpse * ($pph / 100));

                    $tot_hp              = $harga_dasar_lpse + $ppn_nom;
                } elseif ($promo_price > 0) {
                    // jika produk terdapat harga promo saja
                    $price_non_ppn     = $promo_price;

                    $input_price         = $this->getPromoOriginPrice($id_product);
                    $harga_dasar_lpse     = round($price_non_ppn / (1 + ($ppn / 100)));
                    // $ppn_nom        	= $harga_dasar_lpse * ($ppn / 100);
                    // $pph_nom        	= $harga_dasar_lpse * ($pph / 100);

                    $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
                    $pph_nom            = round($harga_dasar_lpse * ($pph / 100));

                    $tot_hp              = $harga_dasar_lpse + $ppn_nom;
                } elseif ($lpse_price > 0) {
                    // jika produk terdapat harga lpse
                    $price_non_ppn     = $lpse_price; // ($lpse_price / (1 + ($ppn / 100)));
                } else {
                    $price_non_ppn     = $price;
                }
                if (!empty($qty)) {
                    $insertData = [
                        'qty' => DB::raw('qty + $qty'),
                    ];
                } else {
                    $insertData = [
                        'qty' => DB::raw('qty + 1'),
                    ];
                }
                $insertData = [
                    'price' => $price_non_ppn,
                    'input_price' => $input_price,
                    'nominal_ppn' => DB::raw("$ppn_nom * qty"),
                    'nominal_pph' => DB::raw("$pph_nom * qty"),
                    'harga_dasar_lpse' => $harga_dasar_lpse,
                    'total_non_ppn' => DB::raw("ROUND(harga_dasar_lpse * qty)"),
                    'total' => DB::raw("price * qty"),
                    'id_nego' => null
                ];
            }

            $insertData = [
                'id_cart' => $id_cart,
                'id_product' => $id_product,
                'price' => $price_non_ppn,
                'qty' => $qty,
                'nominal_ppn' => DB::raw("$ppn_nom * qty"),
                'total' => DB::raw("price * qty"),
                'total_non_ppn' => DB::raw("total-nominal_ppn"),
                'input_price' => $input_price,
                'harga_dasar_lpse' =>  $harga_dasar_lpse,
                'nama' => $nama,
                'image' => $image,
                'nominal_pph' =>  $pph_nom,
                'fee_mp_percent' => $fee_mp_percent,
                'fee_mp_nominal' => $fee_mp_nominal,
                'fee_pg_percent' => $fee_pg_percent,
                'fee_pg_nominal' => $fee_pg_nominal,
                'val_ppn' => $ppn,
                'val_pph' => $pph,
                'weight' => $weight,
                'total_weight' => DB::raw("weight * qty"),
                'id_shop' => $id_shop,

            ];

            $insert_temporary = DB::table('cart_shop_temporary')->insertGetId($insertData);
            if ($insert_temporary) {
                return true;
            }
        }
    }

    public function updateCartShop($id_cart, $id_shop)
    {
        $cf     = Lpse_config::first();
        $ppn     = $cf->ppn / 100;
        $pph     = $cf->pph / 100;

        $id_address_shop = $this->_getShopAddressId($id_shop);

        // Data Shop
        $dataShop = DB::table('cart_shop')
            ->select('id', 'id_coupon', 'id_shipping', 'sum_price', 'sum_shipping', 'insurance_nominal', 'handling_cost', 'handling_cost_non_ppn')
            ->where('id_cart', $id_cart)
            ->where('id_shop', $id_shop);
        $query = $dataShop->get();
        $count     = $dataShop->count();
        $shop     = $query->first();

        $id_shipping = $shop->id_shipping ?? 0 ?: 0;

        $data = DB::table('cart_shop_temporary')
            ->select(
                DB::raw('sum(total) as sum_price'),
                DB::raw('sum(if(nominal_ppn=0, 0, total_non_ppn)) as sum_price_ppn'),
                DB::raw('sum(total_non_ppn) as sum_price_non_ppn'),
                DB::raw('sum(nominal_ppn) as sum_ppn'),
                DB::raw('sum(nominal_pph) as sum_pph'),
                DB::raw('sum(qty) as sum_qty'),
                DB::raw('sum(total_weight) as total_weight')
            )
            ->where('id_cart', $id_cart)
            ->where('id_shop', $id_shop)
            ->where('is_selected', 'Y')
            ->first();

        $data_shipping = DB::table('shipping')
            ->select('price')
            ->where('id', $id_shipping)
            ->value('price');

        if ($data->total_weight == 0) {
            $data->total_weight = ($data->sum_qty * $this->config_default_weight);
        }

        $handling_cost_exlude_ppn = $shop->handling_cost_non_ppn ?? 0;

        // FIXME perhitungan PPH beserta product non ppn
        $ppn_price = round($data->sum_ppn);
        $pph_price = round($data->sum_pph);

        if ($handling_cost_exlude_ppn > 0) {
            $ppn_price = round(($data->sum_price_ppn + $handling_cost_exlude_ppn) * $ppn);
            $pph_price = round(($data->sum_price_non_ppn + $handling_cost_exlude_ppn) * $pph);
        } else {
            $ppn_price = round(($data->sum_price_ppn) * $ppn);
            $pph_price = round(($data->sum_price_non_ppn) * $pph);
        }

        $base_price_shipping = $data_shipping->price ?? 0;
        $total_weight = $data->total_weight;
        $dataArr_ship = [
            'total_weight' => $total_weight,
            'base_price' => $base_price_shipping,
        ];
        $base_price_shipping_ = ceil($total_weight / 1000) * $base_price_shipping;
        $result_calc_shipping = $this->calc_shipping_cost($dataArr_ship);
        $sum_shipping = $result_calc_shipping['price'] ?? 0;
        $sum_price = isset($data->sum_price) ? $data->sum_price : 0;
        $sum_price_non_ppn = isset($data->sum_price_non_ppn) ? $data->sum_price_non_ppn : 0;
        $qty = isset($data->sum_qty) ? $data->sum_qty : 0;

        $data_shop = [
            'id_shipping' => 0, // muncul saat checkout
            'base_rate' => 0, // muncul saat checkout
            'pph_shipping' => 0, // muncul saat checkout
            'discount' => 0, // muncul saat checkout
            'subtotal' => 0, // muncul saat checkout
            'total' => 0, // muncul saat checkout
            // 'note' => 0, // muncul saat checkout
            // 'note_seller' => 0, // muncul saat checkout
            'no_resi' => 0, // muncul saat checkout
            'is_seller_readed' => 0, // muncul saat checkout
            'pin' => 0, // tidak diketahui
            'djp_status' => 0, // muncul saat checkout
            'pickup_number' => 0, // muncul saat checkout
            'file_do' => 0, // muncul saat checkout
            'file_pajak' => 0, // muncul saat checkout
            'is_cron_email' => 0, // tidak dipakai
            'id_address_shop' => $id_address_shop,
            'total_weight' => $data->total_weight,
            'sum_price' => $sum_price, // nilai sum_price tidak boleh null
            'base_price_shipping' => $base_price_shipping_,
            'sum_shipping' => $sum_shipping,
            'sum_price_ppn' => $data->sum_price_ppn,
            'sum_price_non_ppn' => $sum_price_non_ppn,
            'ppn_price' => $ppn_price,
            'pph_price' => $pph_price,
            'qty' => $qty,
            'handling_cost_non_ppn' => $handling_cost_exlude_ppn
        ];

        if ($count == 0) {
            $data_shop['id_cart'] = $id_cart;
            $data_shop['id_shop'] = $id_shop;
            // $data_shop['ppn_price'] = round($data->sum_ppn);

            $shop = $this->_insertShop($data_shop);
        } else {
            $data_shop['id_coupon'] = $shop->id_coupon;
            // $data_shop['ppn_price'] = round($data->sum_ppn + $shop->sum_shipping);

            $shop = $this->_updateShop($id_cart,$shop->id, $data_shop);
        }
        return true;
    }


    public function updateCart($id_cart)
    {
        $getLpse = $this->getlpseConfig();
        $ppn = $getLpse->ppn;
        $pph = $getLpse->pph;

        $shop = DB::table('cart_shop')
            ->select(
                DB::raw('SUM(sum_price) as total'),
                DB::raw('SUM(sum_price_non_ppn) as total_non_ppn'),
                DB::raw('SUM(ppn_price) as total_ppn'),
                DB::raw('SUM(pph_price + pph_shipping) as total_pph'),
                DB::raw('SUM(total) as grandtotal'),
                DB::raw('SUM(qty) as qty'),
                DB::raw('SUM(sum_shipping + insurance_nominal) as total_shipping'),
                DB::raw('SUM(ppn_shipping) as total_ppn_shipping'),
                DB::raw('SUM(discount) as total_discount'),
                DB::raw('SUM(sum_price_ppn) as sum_price_product_ppn_only'),
                DB::raw('SUM(handling_cost_non_ppn) as handling_cost_non_ppn')
            )
            ->where('id_cart', $id_cart)
            ->first();

        // NOTE sum_non_ppn = val_ppn = o
        $sum_price = isset($shop->total) ? $shop->total : 0;
        $sum_shipping_non_ppn = isset($shop->total_shipping) ? $shop->total_shipping : 0;
        $sum_price_non_ppn = isset($shop->total_non_ppn) ? $shop->total_non_ppn : 0;
        $sum_discount = isset($shop->total_discount) ? $shop->total_discount : 0;
        $qty = isset($shop->qty) ? $shop->qty : 0;
        $total_pph = isset($shop->total_pph) ? $shop->total_pph : 0;

        $data_cart = [
            'handling_cost_non_ppn' => 0, // muncul saat checkout
            'sum_price' => $sum_price,
            'sum_price_ppn_only' => $shop->sum_price_product_ppn_only,
            'sum_shipping' => $shop->total_shipping + $shop->total_ppn_shipping,
            'sum_shipping_non_ppn' => $sum_shipping_non_ppn,
            'sum_price_non_ppn' => $sum_price_non_ppn,
            'sum_discount' => $sum_discount,
            'qty' => $qty,
            'total_non_ppn' => $shop->total_non_ppn + $shop->total_shipping,
            'total_ppn' => $shop->total_ppn + $shop->total_ppn_shipping,
            'total_pph' => $total_pph,
            'val_ppn' => $ppn,
            'val_pph' => $pph,
            // 'handling_cost_non_ppn' => $shop->handling_cost_non_ppn,
        ];


        $cart = $this->_updateCart($id_cart, $data_cart);
    }

    public function _updateCart($id_cart, $data_cart)
    {
        DB::table('cart')
            ->where('id', $id_cart)
            ->update($data_cart);

        DB::table('cart')
            ->where('id', $id_cart)
            ->update([
                'total' => DB::raw('sum_price_non_ppn + sum_shipping_non_ppn + total_ppn + handling_cost_non_ppn - sum_discount - discount')
            ]);

        return true;
    }


    public function getProductDetail($id_product)
    {
        $product = Products::select(
            'products.*',
            'product_category.barang_kena_ppn'
        )
            ->leftJoin('product_category', 'products.id_category', '=', 'product_category.id')
            ->where('products.id', $id_product)
            ->first();

        return $product;
    }

    // GET FLASHPRICE BY ID PRODUCT AND QTY
    public function getFlashOriginPrice($id_product, $qty = null)
    {
        date_default_timezone_set('Asia/Jakarta');
        $date_now = Carbon::now()->format('Y-m-d');

        $query = DB::table('flash_sale_product')
            ->select('flashsale_origin')
            ->where('id_product', $id_product)
            ->where('is_approved', '1')
            ->where('ready_stok >', '0')
            ->where('end_date >=', $date_now)
            ->where('start_date <=', $date_now);

        if ($qty != null) {
            $query->where('limit_purchase >=', $qty);
        }
        $data = $query->get();

        if ($data->num_rows() > 0) {
            $flash = $data->row();
            $fprice = $flash->flashsale_origin;
            return $fprice;
        } else {
            return '0';
        }
    }

    // GET PROMO BY ID PRODUCT AND QTY
    public function getPromoOriginPrice($id_product)
    {
        date_default_timezone_set('Asia/Jakarta');
        $date_now = Carbon::now()->format('Y-m-d');

        $query = DB::table('promo_product')
            ->select('promo_origin')
            ->where('id_product', $id_product)
            ->where('is_active', 'Y');

        $data = $query->get();
        if ($data->count() > 0) {
            $promo = $data->first();
            $fpromo = $promo->promo_origin;
            return $fpromo;
        } else {
            return '0';
        }
    }

    // GET PROMO PRICE BY ID PRODUCT
    public function getPromoPrice($id_product)
    {
        $query = DB::table('promo_product')
            ->select('promo_price')
            ->where('id_product', $id_product);
        $data = $query->get();

        if ($data->count() > 0) {
            $promo = $data->frist();
            $price = $promo->promo_price;
            return $price;
        } else {
            return '0';
        }
    }

    // GET FLASHPRICE BY ID PRODUCT AND QTY
    public function getFlashPrice($id_product, $qty = null)
    {
        date_default_timezone_set('Asia/Jakarta');
        $date_now = Carbon::now()->format('Y-m-d');

        $query = DB::table('flash_sale_product')
            ->select('flashsale_price')
            ->where('id_product', $id_product)
            ->where('is_approved', 1)
            ->where('ready_stok', '>', 0)
            ->whereDate('end_date', '>=', $date_now)
            ->whereDate('start_date', '<=', $date_now);

        if ($qty !== null) {
            $query->where('limit_purchase', '>=', $qty);
        }

        $data = $query->first();

        if ($data) {
            return $data->flashsale_price;
        } else {
            return '0';
        }
    }

    public function getLpsePrice($id_product)
    {
        $data = DB::table('lpse_price')
            ->select('price_lpse')
            ->where('id_product', $id_product)
            ->first();

        if ($data) {
            return $data->price_lpse;
        } else {
            return '0';
        }
    }

    // GET ID ADDRESS SHOP
    function _getShopAddressId($id_shop)
    {
        $query = DB::table('shop')
            ->select('id_address')
            ->where('id', $id_shop);
        $data = $query->first();
        return $data->id_address;
    }

    function calc_shipping_cost($dataArr)
    {
        $lpseConfig = $this->getLpseConfig();
        $int_rounded = 100;

        $total_weight = $dataArr['total_weight'] ?? 1000;
        $base_price = $dataArr['base_price'];

        if (!isset($dataArr['ppn'])) {
            $ppn = $lpseConfig->ppn;
        } else {
            $ppn = $dataArr['ppn'];
        }

        if (!isset($dataArr['pph'])) {
            $pph = $lpseConfig->pph;
        } else {
            $pph = $dataArr['pph'];
        }

        $ppn = ($ppn / 100);
        $pph = (2 / 100);

        // NOTE grams to kg
        $total_weight = ceil($total_weight / 1000);

        // NOTE Formula
        $shipping_price = $base_price * $total_weight;
        $shipping_price_ppn = $shipping_price * $ppn;
        $shipping_price_pph = $shipping_price * $pph;

        // NOTE Total Shipping exlude PPN
        $total_shipping = $shipping_price + $shipping_price_ppn + $shipping_price_pph;
        $total_shipping = ceil($total_shipping / $int_rounded) * $int_rounded;

        // NOTE Include PPN & PPH
        $total_shipping_ppn = round($total_shipping * (1 + $ppn));
        $total_shipping_pph = round($total_shipping * (1 + $pph));

        return [
            'base_price' => $base_price,
            'price' => $total_shipping,
            'price_ppn' => $total_shipping_ppn,
            'price_pph' => $total_shipping_pph,

        ];
    }

    public function _insertShop($data_shop)
    {
        DB::table('cart_shop')->insert($data_shop);
        $total = DB::raw('sum_price + sum_shipping - discount');
        // $total = DB::raw('sum_price_non_ppn + ppn_price + sum_shipping + ppn_shipping - discount');
        DB::table('cart_shop')->update(['total' => $total]);
    }

    public function _updateShop($id_cart, $id_shop, $data_shop)
    {
        $sum_price = $data_shop['sum_price'];
        $voucher = $this->getCartVoucherById($data_shop['id_coupon'], $sum_price);
        $cf = Lpse_config::first();
        $ppn = $cf->ppn / 100;
        $pph = $cf->pph / 100;

        $discount = '0';

        if ($voucher) {
            foreach ($voucher as $v) {
                $type = $v->discount_type;
                if ($type == 'percent') {
                    $discount = ($v->discount_value / 100 * $sum_price);
                    if ($v->max_discount > 0 && $v->max_discount < $discount) {
                        $discount = $v->max_discount;
                    }
                } else {
                    $discount = $v->discount_value;
                    if ($v->discount_value > $sum_price) {
                        $discount = $sum_price;
                    }
                }
            }
        }

        $id_coupon = ($sum_price == null || $sum_price == '0') ? null : $data_shop['id_coupon'];

        // $total = DB::raw("sum_price_non_ppn + ppn_price + sum_shipping + insurance_nominal + ppn_shipping  + handling_cost_non_ppn - $discount");
        $total = 0; // dihitung saat cekout

        $insertData = array_merge($data_shop, [
            'subtotal' => 0, // dihitung saat cekout
            'id_coupon' => $id_coupon,
            'discount' => $discount,
            'ppn_shipping' => DB::raw("(ROUND((sum_shipping + insurance_nominal) * $ppn))"),
            'pph_shipping' => DB::raw("(ROUND((sum_shipping + insurance_nominal) * $pph))"),
            // 'subtotal' => DB::raw("sum_shipping + insurance_nominal + sum_price_non_ppn + handling_cost_non_ppn"),
            'total' => $total
        ]);

        DB::table('cart_shop')
            ->where('id_cart', $id_cart)
            ->where('id_shop', $id_shop)
            ->update($insertData);

        $shippingData = DB::table('cart_shop')
            ->select('sum_shipping', 'insurance_nominal')
            ->where('id_cart', $id_cart)
            ->where('id_shop', $id_shop)
            ->first();

        if ($shippingData) {
            $ppn_shipping = round(($shippingData->sum_shipping + $shippingData->insurance_nominal) * $ppn);
            $pph_shipping = round(($shippingData->sum_shipping + $shippingData->insurance_nominal) * $pph);

            DB::table('cart_shop')
                ->where('id_cart', $id_cart)
                ->where('id_shop', $id_shop)
                ->update([
                    'ppn_shipping' => $ppn_shipping,
                    'pph_shipping' => $pph_shipping
                ]);
        }
    }


    public function getCartVoucherById($id_coupon, $sum_price)
    {
        if (empty($sum_price)) {
            $sum_price = '0';
        }
        $voucher = DB::table('coupon')
            ->select('*')
            ->where('id', $id_coupon)
            ->whereRaw("min_limit_purchase <= $sum_price")
            ->get();

        if ($voucher->count() > 0) {
            return $voucher;
        } else {
            return false;
        }
    }

    public function getLpseConfig()
    {
        return DB::table('lpse_config')
            ->select('*')
            ->first();
    }

    function deletecart($id_user, $id_temporary, $id_shop)
    {
        $id_cart = Cart::getIdCartbyidmember($id_user);

        $get = DB::table('cart_shop_temporary')
            ->select('id_nego')
            ->where('id', $id_temporary)
            ->where('id_cart', $id_cart)
            ->where('id_shop', $id_shop)
            ->first();

        if ($get) {
            $data = (array) $get;
            if (!empty($data)) {
                // NOTE if nego exists, then delete nego
                DB::table('nego')
                    ->where('id', $data['id_nego'])
                    ->delete();

                DB::table('product_nego')
                    ->where('id_nego', $data['id_nego'])
                    ->delete();
            }
        }

        // Delete Data
        DB::table('cart_shop_temporary')
            ->where('id', $id_temporary)
            ->where('id_cart', $id_cart)
            ->delete();

        // NOTE check if no current id_shop items left
        $data = DB::table('cart_shop_temporary')
            ->select('id_shop')
            ->where('id_cart', $id_cart)
            ->where('id_shop', $id_shop)
            ->get()
            ->toArray();

        $is_item_left = !empty($data);

        // NOTE updating data when items removed, but check if there still others item left.
        $this->UpdateShopCart($id_cart, $id_shop);

        if (!$is_item_left) {
            // NOTE Delete data at cart_shop, because there no record items left at cart_shop_temporary
            DB::table('cart_shop')
                ->where('id_cart', $id_cart)
                ->where('id_shop', $id_shop)
                ->delete();
        }

        $this->updateCart($id_cart);
    }

    public function updateRatesGlobal($id_cart, $id_shop)
    {
        $d = DB::table('shop_courier as sc')
            ->select(
                'c.id as courier_id',
                'c.code as courier',
                DB::raw('(SELECT ma.city_id FROM cart_shop cs LEFT JOIN member_address ma ON ma.member_address_id = cs.id_address_shop WHERE cs.id_shop = ' . $id_shop . ' AND id_cart = ' . $id_cart . ') as id_city_origin'),
                DB::raw('(SELECT ma.subdistrict_id FROM cart LEFT JOIN member_address ma ON ma.member_address_id = cart.id_address_user WHERE cart.id = ' . $id_cart . ') as id_subdistrict_dest'),
                DB::raw('(SELECT total_weight FROM cart_shop WHERE id_shop = ' . $id_shop . ' AND id_cart = ' . $id_cart . ' AND total_weight != 0) as total_weight')
            )
            ->join('courier as c', 'c.id', '=', 'sc.id_courier', 'left')
            ->where('sc.id_shop', $id_shop)
            ->limit(1)
            ->first();

        $cs = DB::table('cart_shop')
            ->select('id_shipping')
            ->where('id_cart', $id_cart)
            ->where('id_shop', $id_shop)
            ->first();

        if ($d->id_subdistrict_dest != 0) {
            $ship = $this->cekShipping($d->courier_id, $d->id_city_origin, $d->id_subdistrict_dest, '1', $id_shop);
            $data = $ship->first();
        } else {
            $data = null;
        }

        if ($data != null) {
            $cf = $this->getLpseConfig();
            $ppn = $cf->ppn / 100;
            $pph = $cf->pph / 100;

            // Memperbarui nilai di tabel cart_shop
            DB::table('cart_shop')
                ->where('id_shop', $id_shop)
                ->where('id_cart', $id_cart)
                ->update([
                    'ppn_shipping' => DB::raw('ROUND(' . $ppn . ' * (sum_shipping + insurance_nominal))'),
                    'pph_shipping' => DB::raw('ROUND(' . $pph . ' * (sum_shipping + insurance_nominal))'),
                    'subtotal' => DB::raw('sum_shipping + insurance_nominal + sum_price_non_ppn'),
                    'total' => DB::raw('sum_price_non_ppn + ppn_price + sum_shipping + ppn_shipping - discount')
                ]);

            $this->updateCart($id_cart);
            return $data;
        }
    }

    public function cekShipping($id_courier, $id_city_origin, $id_subdistrict_dest, $limit = null, $id_shop = null)
    {
        $query = DB::table('shipping')
            ->select('*')
            ->where('id_courier', $id_courier)
            ->where('id_city_origin', $id_city_origin)
            ->where('id_subdistrict_dest', $id_subdistrict_dest)
            ->orderBy('last_updated_dt', 'DESC')
            ->orderBy('price', 'ASC');

        if ($limit != null) {
            $query->limit($limit);
        }

        $ship = $query->get();

        return $ship;
    }

    function UpdateqtyCart($id_cst, $dataArr)
    {
        $update = DB::table('cart_shop_temporary as cst')
            ->where('id', $id_cst)
            ->update($dataArr);

        return $update;
    }


    public function updateTemporaryById($id_user, $id_shop, $id_cst, $qty)
    {
        $cartShop = new CartShop();
        $id_cart = Cart::getIdCartbyidmember($id_user);
        $update_temp = $this->_updateqtyCST($id_cart, $id_cst, $qty, $id_user);

        DB::table('cart_shop')
            ->where('id_cart', $id_cart)
            ->where('id_shop', $id_shop)
            ->update([
                'is_insurance' => '0',
                'insurance_nominal' => '0'
            ]);

        // $update_cart_shop = $this->updateCartShop($id_cart, $id_shop);
        // $update_cart = $this->updateCart($id_cart);

        $update_cart_shop = $cartShop->refreshCartShop($id_cart, $id_shop);
        $update_cart = $cartShop->refreshCart($id_cart);

        if ($update_cart_shop) {
            $sumprice = $this->sumPriceSelectProductCart($id_cart);
            $totalqty = $this->sumqtySelected($id_cart);

            $data = [
                'sumprice' => $sumprice,
                // 'is_selected' => $cst->is_selected,
                'qty' => $totalqty
            ];

            return $data;
        } else {
            return false;
        }
    }

    private function _updateqtyCST($id_cart, $id_cst, $qty, $id_user)
    {
        $cst = DB::table('cart_shop_temporary')
            ->join('products as p', 'p.id', '=', 'cart_shop_temporary.id_product')
            ->where('cart_shop_temporary.id', $id_cst)
            ->where('cart_shop_temporary.id_cart', $id_cart)
            ->select('cart_shop_temporary.id_product', 'cart_shop_temporary.qty', 'p.price')
            ->first();

        $getLpse = Lpse_config::first();
        $getProduct = $this->getProductDetail($cst->id_product);
        $ppn = $getLpse->ppn;
        $pph = $getLpse->pph;

        if ($getProduct->barang_kena_ppn == 0) {
            $ppn = 0;
        }

        $promo_price = $this->getPromoPrice($cst->id_product);
        $flash_price = $this->getFlashPrice($cst->id_product, $qty);
        $lpse_price = $this->getLpsePrice($cst->id_product);
        $price = $cst->price;

        $nego = DB::table('nego as n')
            ->join('product_nego as pn', 'pn.id_nego', '=', 'n.id')
            ->where('n.member_id', $id_user)
            ->where('n.status', 1)
            ->where('pn.status', 1)
            ->where('n.complete_checkout', 0)
            ->where('pn.id_product', $cst->id_product)
            ->where('pn.qty', $qty)
            ->select('n.*', 'pn.*')
            ->first();

        // DATA
        $nama = $getProduct->name;
        $image = $getProduct->artwork_url_sm[0];
        $input_price = $getProduct->price;
        $fee_mp_percent = $getLpse->fee_mp_percent;
        $fee_mp_nominal = $getLpse->fee_mp_nominal;
        $fee_pg_percent = $getLpse->fee_pg_percent;
        $fee_pg_nominal = $getLpse->fee_pg_nominal;
        $tot_fee_perc = $fee_mp_percent + $fee_pg_percent;
        $tot_fee_nom = $fee_mp_nominal + $fee_pg_nominal;

        $harga_dasar_lpse = round($lpse_price / (1 + ($ppn / 100)));
        $ppn_nom = round($harga_dasar_lpse * ($ppn / 100));
        $pph_nom = round($harga_dasar_lpse * ($pph / 100));
        $tot_hp = $harga_dasar_lpse + $ppn_nom;

        if ($nego && $nego->base_price > 0) {
            $harga_dasar_lpse = 0;
            $harga_dasar_lpse_satuan = 0;

            if ($qty >= 1) {
                $harga_dasar_lpse_satuan = round($nego->harga_nego / $nego->qty);
                $harga_dasar_lpse_exc = round($harga_dasar_lpse_satuan / (1 + ($ppn / 100)));
                $harga_dasar_lpse = $harga_dasar_lpse_exc * $qty;
            } else {
                $harga_dasar_lpse_satuan = $nego->harga_nego;
                $harga_dasar_lpse = round($nego->harga_nego / (1 + ($ppn / 100)));
            }

            $input_price = $nego->nominal_didapat / $nego->qty;
            $harga_tayang = $nego->base_price;
            $ppn_nom = round($harga_dasar_lpse * ($ppn / 100));
            $pph_nom = round($harga_dasar_lpse * ($pph / 100));

            DB::table('cart_shop_temporary')
                ->where('id', $id_cst)
                ->where('id_cart', $id_cart)
                ->update([
                    'id_nego' => $nego->id_nego,
                    'input_price' => $input_price,
                    'price' => $harga_tayang,
                    'qty' => $qty,
                    'nominal_ppn' => $ppn_nom,
                    'nominal_pph' => $pph_nom,
                    'val_ppn' => $ppn,
                    'val_pph' => $pph,
                    'total_non_ppn' => $harga_dasar_lpse,
                    'harga_dasar_lpse' => DB::raw('total_non_ppn / qty'),
                    'total' => $nego->harga_nego,
                ]);
        } else {
            if ($flash_price > 0) {
                $price_non_ppn = $flash_price;
                $input_price = $this->getFlashOriginPrice($cst->id_product, $qty);
                $harga_dasar_lpse = round($price_non_ppn / (1 + ($ppn / 100)));
                $ppn_nom = round($harga_dasar_lpse * ($ppn / 100));
                $pph_nom = round($harga_dasar_lpse * ($pph / 100));
                $tot_hp = $harga_dasar_lpse + $ppn_nom;
            } elseif ($promo_price > 0) {
                $price_non_ppn = $promo_price;
                $input_price = $this->getPromoOriginPrice($cst->id_product);
                $harga_dasar_lpse = round($price_non_ppn / (1 + ($ppn / 100)));
                $ppn_nom = round($harga_dasar_lpse * ($ppn / 100));
                $pph_nom = round($harga_dasar_lpse * ($pph / 100));
                $tot_hp = $harga_dasar_lpse + $ppn_nom;
            } elseif ($lpse_price > 0) {
                $price_non_ppn = $lpse_price;
            } else {
                $price_non_ppn = $price;
            }

            DB::table('cart_shop_temporary')
                ->where('id', $id_cst)
                ->where('id_cart', $id_cart)
                ->update([
                    'id_nego' => null,
                    'input_price' => $input_price,
                    'price' => $price_non_ppn,
                    'qty' => $qty,
                    'nominal_ppn' => DB::raw($ppn_nom . '*qty'),
                    'nominal_pph' => DB::raw($pph_nom . '*qty'),
                    'val_ppn' => $ppn,
                    'val_pph' => $pph,
                    'harga_dasar_lpse' => $harga_dasar_lpse,
                    'total_non_ppn' => DB::raw('harga_dasar_lpse*qty'),
                    'total' => DB::raw('price*qty'),
                ]);
        }

        $dataArr = [
            'harga' => $input_price,
            'fee_pg_percent' => $fee_pg_percent,
            'fee_mp_percent' => $fee_mp_percent,
            'fee_mp_nominal' => $fee_mp_nominal,
            'fee_pg_nominal' => $fee_pg_nominal,
            'ppn' => $ppn,
            'pph' => $pph,
            'qty' => $qty,
        ];

        $result = Calculation::calc_harga_tayang($dataArr);
        $price_exlude_with_ppn = $result['price_exlude_with_ppn'];
        $final_price = $result['price_final'];

        $calc_fee = $result['selisih_fee_calc'];
        $calc_vendor_price_fee = $result['price_vendor_with_fee'];
        $calc_vendor_price_fee_pph = $result['price_vendor_with_fee_pph'];

        $price_mp_get = $result['price_mp_get'];
        $price_mp_satuan = $result['price_mp_satuan'];
        $price_mp_total_incl = $result['price_mp_total_incl'];
        $price_mp_total_excl = $result['price_mp_total_excl'];

        DB::table('cart_shop_temporary')
            ->where('id', $id_cst)
            ->where('id_cart', $id_cart)
            ->update([
                'calc_fee' => $calc_fee,
                'calc_vendor_price_fee' => $calc_vendor_price_fee,
                'calc_vendor_price_fee_pph' => $calc_vendor_price_fee_pph,
                'calc_mp_price_get' => $price_mp_get,
                'calc_fee_mp_satuan_nominal' => $price_mp_satuan,
                'calc_fee_mp_incl_nominal' => $price_mp_total_incl,
                'calc_fee_mp_excl_nominal' => $price_mp_total_excl,
                'total_weight' => DB::raw('weight*qty'),
                'is_selected' => 'Y',
            ]);

        return true;
    }

    function CheckCart($id_cart, $id_product, $id_user, $qty)
    {
        $products = new Products();
        $p = $products->getproduct($id_product);
        // Nego 
        $nego = $this->getDataNego($id_user, $id_product, $qty);
        if ($nego && ($nego->n_id_product == $id_product) && ($nego->n_qty == $qty)) {
            $temporary = $this->UpdateTemporaryCart($id_cart, $p->id, $nego->n_price,  $id_user, $qty, true);
        } else {
            $temporary = $this->UpdateTemporaryCart($id_cart, $p->id, $p->price,  $id_user, $qty);
        }

        $update_cart_shop    =     $this->UpdateShopCart($id_cart, $p->id_shop);
        $update_cart        =     $this->updateCart($id_cart);
        return true;
    }

    function UpdateTemporaryCart($id_cart, $id_product, $price, $id_user, $qty, $is_nego = null)
    {
        $temporary = DB::table('cart_shop_temporary')
            ->where('id_cart', $id_cart)
            ->where('id_product', $id_product);

        $products = new Products();
        $data_product = $products->GetDetialProduct($id_product);

        $check_temporary = $temporary->count();
        if ($check_temporary > 0) {
            $data_old = $temporary->first();
            $data_nego = DB::table('nego as n')
                ->select(
                    'n.*',
                    'n.status as n_status',
                    'pn.*',
                    'pn.status as pn_status'
                )
                ->join('product_nego as pn', 'pn.id_nego', 'n.id')
                ->where('n.member_id', $id_user)
                ->where('n.status', '1')
                ->where('pn.status', '1')
                ->where('n.complete_checkout', '0')
                ->where('pn.id_product', $id_product)
                ->where('pn.qty', $qty);
            $n_count      = $data_nego->count();
            $nego         = $data_nego->first();

            // Config 
            $getLpse     = Lpse_config::first();
            $ppn = $getLpse->ppn;
            $pph = $getLpse->pph;
            if ($data_product->barang_kena_ppn == 0) {
                $ppn = '0';
            }
            $promo_price = $this->getPromoPrice($id_product);
            $flash_price = $this->getFlashPrice($id_product, $qty);
            $lpse_price  = $this->getLpsePrice($id_product);

            $name       = $data_product->name;
            $image      = $data_product->image;
            $input_price = $data_product->price;
            $fee_mp_percent        = $getLpse->fee_mp_percent;
            $fee_mp_nominal        = $getLpse->fee_mp_nominal;
            $fee_pg_percent        = $getLpse->fee_pg_percent;
            $fee_pg_nominal        = $getLpse->fee_pg_nominal;
            $tot_fee_perc        = $fee_mp_percent + $fee_pg_percent;
            $tot_fee_nom        = $fee_mp_nominal + $fee_pg_nominal;

            // peritungan
            $harga_dasar_lpse     = round($lpse_price / (1 + ($ppn / 100)));
            $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
            $pph_nom            = round($harga_dasar_lpse * ($pph / 100));
            $tot_hp              = $harga_dasar_lpse + $ppn_nom;

            if ($is_nego && $n_count > 0 && $nego->base_price > 0) {
                // Jika Nego
                $harga_dasar_lpse = 0;
                $harga_dasar_lpse_satuan = 0;
                if ($qty >= 1) {
                    $harga_dasar_lpse_satuan = round($nego->harga_nego / $nego->qty);
                    $harga_dasar_lpse_exc     = round($harga_dasar_lpse_satuan / (1 + ($ppn / 100)));
                    $harga_dasar_lpse     = $harga_dasar_lpse_exc * $qty;
                } else {
                    $harga_dasar_lpse_satuan = $nego->harga_nego;
                    $harga_dasar_lpse     = round($nego->harga_nego / (1 + ($ppn / 100)));
                }

                $input_price         = $nego->nominal_didapat / $nego->qty;
                $harga_tayang         = $nego->base_price;
                $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
                $pph_nom            = round($harga_dasar_lpse * ($pph / 100));

                $insertData = [
                    'id_nego' => $nego->id_nego,
                    'input_price' => $input_price,
                    'price' => $harga_tayang,
                    'qty' => $qty,
                    'nominal_ppn' => DB::raw($ppn_nom),
                    'nominal_pph' => DB::raw($pph_nom),
                    'val_ppn' => $ppn,
                    'val_pph' => $pph,
                    'total_non_ppn' => $harga_dasar_lpse,
                    'harga_dasar_lpse' => DB::raw('total_non_ppn / qty'),
                    'total' =>  $nego->harga_nego
                ];
            } else {
                if ($flash_price > 0) {
                    $price_non_ppn     = $flash_price;

                    $input_price         = $this->getFlashOriginPrice($id_product, $qty);
                    $harga_dasar_lpse     = round($price_non_ppn / (1 + ($ppn / 100)));
                    $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
                    $pph_nom            = round($harga_dasar_lpse * ($pph / 100));
                    $tot_hp              = $harga_dasar_lpse + $ppn_nom;
                } elseif ($promo_price > 0) {
                    // jika produk terdapat harga promo saja
                    $price_non_ppn     = $promo_price;

                    $input_price         = $this->getPromoOriginPrice($id_product);
                    $harga_dasar_lpse     = round($price_non_ppn / (1 + ($ppn / 100)));
                    $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
                    $pph_nom            = round($harga_dasar_lpse * ($pph / 100));

                    $tot_hp              = $harga_dasar_lpse + $ppn_nom;
                } elseif ($lpse_price > 0) {
                    $price_non_ppn     = $lpse_price;
                } else {
                    $price_non_ppn     = $price;
                }

                if (!empty($qty)) {
                    $insertData = [
                        'qty' => DB::raw('qty + $qty'),
                    ];
                } else {
                    $insertData = [
                        'qty' => DB::raw('qty + 1'),
                    ];
                }
                $insertData = [
                    'price' => $price_non_ppn,
                    'input_price' => $input_price,
                    'nominal_ppn' => DB::raw("$ppn_nom * qty"),
                    'nominal_pph' => DB::raw("$pph_nom * qty"),
                    'harga_dasar_lpse' => $harga_dasar_lpse,
                    'total_non_ppn' => DB::raw("harga_dasar_lpse * qty"),
                    'total' => DB::raw("price * qty"),
                    'id_nego' => null,
                ];
            }
            $insertData = [
                'nama' => $data_product->name,
                'image' => $image,
                'fee_mp_percent' => $fee_mp_percent,
                'fee_mp_nominal' => $fee_mp_nominal,
                'fee_pg_percent' => $fee_pg_percent,
                'fee_pg_nominal' => $fee_pg_nominal,
                'val_ppn' => $ppn,
                'val_pph' => $pph,
                'total_weight' => DB::raw('weight * qty'),
                'id_shop' => $data_product->id_shop,
                'is_selected' => 'Y'
            ];

            $update_temporary = DB::table('cart_shop_temporary')
                ->where('id', $data_old->id)
                ->update($insertData);
            if ($update_temporary) {
                return true;
            }
        } else {
            $data_nego = DB::table('nego as n')
                ->select(
                    'n.*',
                    'n.status as n_status',
                    'pn.*',
                    'pn.status as pn_status'
                )
                ->join('product_nego as pn', 'pn.id_nego', 'n.id')
                ->where('n.member_id', $id_user)
                ->where('n.status', '1')
                ->where('pn.status', '1')
                ->where('n.complete_checkout', '0')
                ->where('pn.id_product', $id_product)
                ->where('pn.qty', $qty);

            $n_count      = $data_nego->count();
            $nego         = $data_nego->first();

            // cek barang PPN atau tidak
            $getLpse     = Lpse_config::first();
            $ppn = $getLpse->ppn;
            $pph = $getLpse->pph;
            if ($data_product->barang_kena_ppn == 0) {
                $ppn = '0';
            }

            // update
            $promo_price = $this->getPromoPrice($id_product);
            $flash_price = $this->getFlashPrice($id_product, $qty);
            $lpse_price  = $this->getLpsePrice($id_product);
            $insertData = [
                'id_nego' => null,
                'id_cart' => $id_cart,
                'id_product' => $id_product
            ];

            $nama                 = $data_product->name;
            $image                 = $data_product->image;
            $input_price        = $data_product->price;
            $fee_mp_percent        = $getLpse->fee_mp_percent;
            $fee_mp_nominal        = $getLpse->fee_mp_nominal;
            $fee_pg_percent        = $getLpse->fee_pg_percent;
            $fee_pg_nominal        = $getLpse->fee_pg_nominal;
            $tot_fee_perc        = $fee_mp_percent + $fee_pg_percent;
            $tot_fee_nom        = $fee_mp_nominal + $fee_pg_nominal;

            $harga_dasar_lpse     = round($lpse_price / (1 + ($ppn / 100)));
            $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
            $pph_nom            = round($harga_dasar_lpse * ($pph / 100));

            $tot_hp              = $harga_dasar_lpse + $ppn_nom;

            if ($is_nego && $n_count > 0 && $nego->base_price > 0) {
                $harga_dasar_lpse = 0;
                $harga_dasar_lpse_satuan = 0;

                if ($qty >= 1) {
                    $harga_dasar_lpse_satuan = round($nego->harga_nego / $nego->qty);
                    $harga_dasar_lpse_exc     = round($harga_dasar_lpse_satuan / (1 + ($ppn / 100)));
                    $harga_dasar_lpse     = $harga_dasar_lpse_exc * $qty;
                } else {
                    $harga_dasar_lpse_satuan = $nego->harga_nego;
                    $harga_dasar_lpse     = round($nego->harga_nego / (1 + ($ppn / 100)));
                }

                $input_price         = $nego->nominal_didapat / $nego->qty;
                $harga_tayang         = $nego->base_price;
                $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
                $pph_nom            = round($harga_dasar_lpse * ($pph / 100));

                $insertData = [
                    'id_nego' => $nego->id_nego,
                    'input_price' => $input_price,
                    'price' => $harga_tayang,
                    'qty' => $qty,
                    'nominal_ppn' => $ppn_nom,
                    'nominal_pph' => $pph_nom,
                    'val_ppn' => $ppn,
                    'val_pph' => $pph,
                    'total_non_ppn' => $harga_dasar_lpse,
                    'harga_dasar_lpse' => DB::raw('ROUND(total_non_ppn / qty)'),
                    'total' => $nego->harga_nego
                ];
            } else {
                if ($flash_price > 0) { //
                    // jika produk terdapat harga (flashsale & promo) atau (flashsale)
                    $price_non_ppn     = $flash_price;

                    $input_price         = $this->getFlashOriginPrice($id_product, $qty);
                    $harga_dasar_lpse     = round($price_non_ppn / (1 + ($ppn / 100)));
                    $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
                    $pph_nom            = round($harga_dasar_lpse * ($pph / 100));

                    $tot_hp              = $harga_dasar_lpse + $ppn_nom;
                } elseif ($promo_price > 0) {
                    // jika produk terdapat harga promo saja
                    $price_non_ppn     = $promo_price;

                    $input_price         = $this->getPromoOriginPrice($id_product);
                    $harga_dasar_lpse     = round($price_non_ppn / (1 + ($ppn / 100)));
                    $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
                    $pph_nom            = round($harga_dasar_lpse * ($pph / 100));

                    $tot_hp              = $harga_dasar_lpse + $ppn_nom;
                } elseif ($lpse_price > 0) {
                    // jika produk terdapat harga lpse
                    $price_non_ppn     = $lpse_price; // ($lpse_price / (1 + ($ppn / 100)));
                } else {
                    $price_non_ppn     = $price;
                }
                if (!empty($qty)) {
                    $insertData = [
                        'qty' => DB::raw('qty + $qty'),
                    ];
                } else {
                    $insertData = [
                        'qty' => DB::raw('qty + 1'),
                    ];
                }
                $insertData = [
                    'price' => $price_non_ppn,
                    'input_price' => $input_price,
                    'nominal_ppn' => DB::raw("$ppn_nom * qty"),
                    'nominal_pph' => DB::raw("$pph_nom * qty"),
                    'harga_dasar_lpse' => $harga_dasar_lpse,
                    'total_non_ppn' => DB::raw("ROUND(harga_dasar_lpse * qty)"),
                    'total' => DB::raw("price * qty"),
                    'id_nego' => null
                ];
            }

            $insertData = [
                'id_cart' => $id_cart,
                'id_product' => $id_product,
                'price' => $price_non_ppn,
                'qty' => $qty,
                'nominal_ppn' => DB::raw("$ppn_nom * qty"),
                'total' => DB::raw("price * qty"),
                'total_non_ppn' => DB::raw("total-nominal_ppn"),
                'input_price' => $input_price,
                'harga_dasar_lpse' =>  $harga_dasar_lpse,
                'nama' => $nama,
                'image' => $image,
                'nominal_pph' =>  $pph_nom,
                'fee_mp_percent' => $fee_mp_percent,
                'fee_mp_nominal' => $fee_mp_nominal,
                'fee_pg_percent' => $fee_pg_percent,
                'fee_pg_nominal' => $fee_pg_nominal,
                'val_ppn' => $ppn,
                'val_pph' => $pph,
                'weight' => $data_product->weight,
                'total_weight' => DB::raw("weight * qty"),
                'id_shop' => $data_product->id_shop,

            ];

            $insert_temporary = DB::table('cart_shop_temporary')->insertGetId($insertData);
            if ($insert_temporary) {
                return true;
            }
        }
    }

    function UpdateShopCart($id_cart, $id_shop)
    {
        $cf     = Lpse_config::first();
        $ppn     = $cf->ppn / 100;
        $pph     = $cf->pph / 100;

        $id_address_shop = $this->_getShopAddressId($id_shop);

        // Data Shop
        $dataShop = DB::table('cart_shop')
            ->select('id', 'id_coupon', 'id_shipping', 'sum_price', 'sum_shipping', 'insurance_nominal', 'handling_cost', 'handling_cost_non_ppn')
            ->where('id_cart', $id_cart)
            ->where('id_shop', $id_shop);
        $count     = $dataShop->count();
        $shop     = $dataShop->first();

        $id_shipping = $shop->id_shipping ?? 0;

        $data = DB::table('cart_shop_temporary')
            ->select(
                DB::raw('sum(total) as sum_price'),
                DB::raw('sum(if(nominal_ppn=0, 0, total_non_ppn)) as sum_price_ppn'),
                DB::raw('sum(total_non_ppn) as sum_price_non_ppn'),
                DB::raw('sum(nominal_ppn) as sum_ppn'),
                DB::raw('sum(nominal_pph) as sum_pph'),
                DB::raw('sum(qty) as sum_qty'),
                DB::raw('sum(total_weight) as total_weight')
            )
            ->where('id_cart', $id_cart)
            ->where('id_shop', $id_shop)
            ->where('is_selected', 'Y')
            ->first();

        $data_shipping = DB::table('shipping')
            ->select('price')
            ->where('id', $id_shipping)
            ->value('price');

        if ($data->total_weight == 0) {
            $data->total_weight = ($data->sum_qty * 1000);
        }

        $handling_cost_exlude_ppn = $shop->handling_cost_non_ppn ?? 0;

        // FIXME perhitungan PPH beserta product non ppn
        $ppn_price = round($data->sum_ppn);
        $pph_price = round($data->sum_pph);

        if ($handling_cost_exlude_ppn > 0) {
            $ppn_price = round(($data->sum_price_ppn + $handling_cost_exlude_ppn) * $ppn);
            $pph_price = round(($data->sum_price_non_ppn + $handling_cost_exlude_ppn) * $pph);
        } else {
            $ppn_price = round($data->sum_price_ppn * $ppn);
            $pph_price = round($data->sum_price_non_ppn * $pph);
        }

        $base_price_shipping = $data_shipping->price ?? 0;
        $total_weight = $data->total_weight;
        $dataArr_ship = [
            'total_weight' => $total_weight,
            'base_price' => $base_price_shipping,
        ];

        $calc = new Calculation();

        $base_price_shipping_ = ceil($total_weight / 1000) * $base_price_shipping;
        $result_calc_shipping = $calc->OngkirSudahPPN( $base_price_shipping, $total_weight);
        $sum_shipping = $result_calc_shipping['Ongkir_akhir'] ?? 0;
        $sum_price = $data->sum_price ?? 0;
        $sum_price_non_ppn = $data->sum_price_non_ppn ?? 0;
        $qty = $data->sum_qty ?? 0;

        $data_shop = [
            'id_shipping' => 0, // muncul saat checkout
            'base_rate' =>  $base_price_shipping, // muncul saat checkout
            'pph_shipping' => 0, // muncul saat checkout
            'discount' => 0, // muncul saat checkout
            'subtotal' => 0, // muncul saat checkout
            'total' => 0, // muncul saat checkout
            'no_resi' => 0, // muncul saat checkout
            'is_seller_readed' => 0, // muncul saat checkout
            'pin' => 0, // tidak diketahui
            'djp_status' => 0, // muncul saat checkout
            'pickup_number' => 0, // muncul saat checkout
            'file_do' => 0, // muncul saat checkout
            'file_pajak' => 0, // muncul saat checkout
            'is_cron_email' => 0, // tidak dipakai
            'id_address_shop' => $id_address_shop,
            'total_weight' => $data->total_weight,
            'sum_price' => $sum_price, // nilai sum_price tidak boleh null
            'base_price_shipping' => $base_price_shipping_,
            'sum_shipping' => $sum_shipping,
            'sum_price_ppn' => $data->sum_price_ppn,
            'sum_price_non_ppn' => $sum_price_non_ppn,
            'ppn_price' => $ppn_price,
            'pph_price' => $pph_price,
            'qty' => $qty,
            'handling_cost_non_ppn' => $handling_cost_exlude_ppn
        ];

        if ($count == 0) {
            $data_shop['id_cart'] = $id_cart;
            $data_shop['id_shop'] = $id_shop;
            $shop = $this->_insertShop($data_shop);
        } else {
            $data_shop['id_coupon'] = $shop->id_coupon;
            $shop = $this->_updateShop($id_cart, $id_shop, $data_shop);
        }
        return true;
    }
}
