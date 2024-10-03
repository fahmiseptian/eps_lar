<?php

namespace App\Models;

use App\Libraries\Calculation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CartShop extends Model
{
    protected $table = 'cart_shop';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $calculation;
    protected $CartShopTemporary;

    public function __construct()
    {
        $this->calculation = new Calculation;
        $this->CartShopTemporary = new CartShopTemporary;
    }

    function getdetailcartByIdcart($id_cart)
    {
        $detailCart = DB::table('cart_shop as cs')
            ->select(
                'cs.id as id_cs',
                'cs.id_shop',
                'cs.id_shipping',
                'cs.sum_shipping',
                'cs.ppn_shipping',
                'cs.is_insurance',
                'cs.insurance_nominal',
                'cs.base_price_asuransi',
                'cs.sum_asuransi',
                'cs.ppn_price',
                'cs.total',
                'cs.discount',
                'cs.pesan_seller',
                'cs.keperluan',
                's.nama_pt as nama_seller',
                'shipping.id_courier',
                'shipping.deskripsi as deskripsi_pengiriman'
            )
            ->join('shop as s', 'cs.id_shop', '=', 's.id')
            ->leftJoin('shipping', 'cs.id_shipping', '=', 'shipping.id')
            ->where('cs.id_cart', $id_cart)
            ->get();

        foreach ($detailCart as $detail) {
            if ($detail->id_shipping == '0' || $detail->id_shipping === null) {
                $detail->id_courier = '0';
            }
        }

        return $detailCart;
    }

    function Detailcsis_selected($id_cart)
    {
        $detailCart = DB::table('cart_shop as cs')
            ->select(
                'cs.id as id_cs',
                'cs.id_shop',
                'cs.id_shipping',
                'cs.sum_shipping',
                'cs.ppn_shipping',
                'cs.is_insurance',
                'cs.insurance_nominal',
                'cs.base_price_asuransi',
                'cs.sum_asuransi',
                'cs.ppn_price',
                'cs.total',
                'cs.discount',
                'cs.pesan_seller',
                'cs.keperluan',
                's.nama_pt as nama_seller',
                'shipping.id_courier',
                'shipping.deskripsi as deskripsi_pengiriman'
            )
            ->join('shop as s', 'cs.id_shop', '=', 's.id')
            ->leftJoin('shipping', 'cs.id_shipping', '=', 'shipping.id')
            ->where('cs.id_cart', $id_cart)
            ->where('cs.qty', '!=', 0)
            ->get();

        return $detailCart;
    }

    public function insurance($id_user, $id_shop, $id_courier, $status, $idcs)
    {
        $id_cart = Cart::getIdCartbyidmember($id_user);

        // Subquery untuk mendapatkan sum_price
        $sumPriceQuery = DB::table('cart_shop')
            ->where('id_cart', $id_cart)
            ->where('id_shop', $id_shop)
            ->select('sum_price')
            ->first();

        $sum_price = $sumPriceQuery ? $sumPriceQuery->sum_price : 0;

        $cour = DB::table('courier as a')
            ->select('a.insurance_fee_percent', 'a.insurance_fee_nominal')
            ->where('a.id', $id_courier)
            ->first();


        if ($cour) {
            if ($status == 'true') {
                $status = '1';
                $dataArr = [
                    'id_courier' => $id_courier,
                    'sum_price' => $sum_price,
                ];

                $result = $this->calculation->calcShippingInsuranceCost($dataArr, true);
                $insurance_nominal = $result['price'];
                $base_price_asuransi = $result['base_price'];
                $sum_asuransi = $result['price_ppn'];
            } else {
                $status = '0';
                $insurance_nominal = '0';
                $base_price_asuransi = 0;
                $sum_asuransi = 0;
            }
        } else {
            $status = '0';
            $insurance_nominal = '0';
            $base_price_asuransi = 0;
            $sum_asuransi = 0;
        }

        $dupdate = [
            'is_insurance' => $status,
            'insurance_nominal' => $insurance_nominal,
            'base_price_asuransi' => $base_price_asuransi,
            'sum_asuransi' => $sum_asuransi
        ];

        $this->insuranced($idcs, $dupdate);
        $this->refreshCartShop($id_cart, $id_shop);
        $this->refreshCart($id_cart);

        $data = DB::table('cart_shop as cs')
            ->select(
                DB::raw('SUM(cs.total) as total')
            )
            ->where('cs.id_shop', $id_shop)
            ->where('cs.id_cart', $id_cart)
            ->first();

        return $data;
    }

    public function insuranced($idcs, $dupdate)
    {
        $cf     = Lpse_config::first();
        $ppn = $cf->ppn / 100;
        $pph = $cf->pph / 100;

        // Update the cart_shop table
        DB::table('cart_shop')
            ->where('id', $idcs)
            ->update($dupdate);

        return true;
    }

    public function refreshCart($id_cart)
    {
        $cf     = Lpse_config::first();
        $ppn = $cf->ppn / 100;
        $pph = $cf->pph / 100;

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

        $data_cart = [
            'sum_price' => $shop->total,
            'sum_price_ppn_only' => $shop->sum_price_product_ppn_only,
            'sum_shipping' => $shop->total_shipping + $shop->total_ppn_shipping,
            'sum_shipping_non_ppn' => $shop->total_shipping,
            'sum_price_non_ppn' => $shop->total_non_ppn,
            'sum_discount' => $shop->total_discount,
            'qty' => $shop->qty,
            'total_non_ppn' => $shop->total_non_ppn + $shop->total_shipping,
            'total_ppn' => $shop->total_ppn + $shop->total_ppn_shipping,
            'total_pph' => $shop->total_pph,
            'val_ppn' => ($ppn * 100),
            'val_pph' => ($pph * 100),
            'handling_cost_non_ppn' => $shop->handling_cost_non_ppn,
        ];

        $this->_refreshCart($id_cart, $data_cart);
    }

    private function _refreshCart($id_cart, $data_cart)
    {
        $update_data = [
            'sum_price' => $data_cart['sum_price'],
            'sum_price_ppn_only' => $data_cart['sum_price_ppn_only'],
            'sum_shipping' => $data_cart['sum_shipping'],
            'sum_shipping_non_ppn' => $data_cart['sum_shipping_non_ppn'],
            'sum_price_non_ppn' => $data_cart['sum_price_non_ppn'],
            'sum_discount' => $data_cart['sum_discount'],
            'qty' => $data_cart['qty'],
            'total_non_ppn' => $data_cart['total_non_ppn'],
            'total_ppn' => $data_cart['total_ppn'],
            'total_pph' => $data_cart['total_pph'],
            'val_ppn' => $data_cart['val_ppn'],
            'val_pph' => $data_cart['val_pph'],
            'handling_cost_non_ppn' => $data_cart['handling_cost_non_ppn'],
        ];

        DB::table('cart')
            ->where('id', $id_cart)
            ->update($update_data);

        DB::table('cart')
            ->where('id', $id_cart)
            ->update([
                'total' => DB::raw('sum_price_non_ppn + sum_shipping_non_ppn + total_ppn + handling_cost_non_ppn - sum_discount - discount')
            ]);

        return true;
    }

    public function refreshCartShop($id_cart, $id_shop)
    {
        $cf = Lpse_config::first();
        $ppn = $cf->ppn / 100;
        $pph = $cf->pph / 100;

        $id_address_shop = $this->CartShopTemporary->_getShopAddressId($id_shop);

        $shop = DB::table('cart_shop')
            ->select(
                'id',
                'id_coupon',
                'id_shipping',
                'sum_price',
                'sum_shipping',
                'insurance_nominal',
                'handling_cost',
                'handling_cost_non_ppn'
            )
            ->where('id_cart', $id_cart)
            ->where('id_shop', $id_shop)
            ->first();

        $id_shipping = $shop->id_shipping ?? 0;

        $data = DB::table('cart_shop_temporary')
            ->select(
                DB::raw('SUM(total) as sum_price'),
                DB::raw('SUM(if(nominal_ppn=0, 0, total_non_ppn)) as sum_price_ppn'),
                DB::raw('SUM(total_non_ppn) as sum_price_non_ppn'),
                DB::raw('SUM(nominal_ppn) as sum_ppn'),
                DB::raw('SUM(nominal_pph) as sum_pph'),
                DB::raw('SUM(qty) as sum_qty'),
                DB::raw('SUM(total_weight) as total_weight')
            )
            ->where('id_cart', $id_cart)
            ->where('id_shop', $id_shop)
            ->where('is_selected', 'Y')
            ->first();

        $data_shipping = DB::table('shipping')
            ->select('price')
            ->where('id', $id_shipping)
            ->first();

        if ($data->total_weight == 0) {
            $data->total_weight = ($data->sum_qty * $this->config_default_weight);
        }

        $handling_cost_exlude_ppn = $shop->handling_cost_non_ppn ?? 0;

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

        $result_calc_shipping = $this->CartShopTemporary->calc_shipping_cost($dataArr_ship);

        $sum_shipping = $result_calc_shipping['price'] ?? 0;

        $data_shop = [
            'id_address_shop' => $id_address_shop,
            'total_weight' => $data->total_weight,
            'sum_price' => $data->sum_price,
            'base_price_shipping' => $base_price_shipping_,
            'sum_shipping' => $sum_shipping,
            'sum_price_ppn' => $data->sum_price_ppn,
            'sum_price_non_ppn' => $data->sum_price_non_ppn,
            'ppn_price' => $ppn_price,
            'pph_price' => $pph_price,
            'qty' => $data->sum_qty,
            'insurance_nominal' => $shop->insurance_nominal,
            'handling_cost_non_ppn' => $handling_cost_exlude_ppn,
            // 'handling_cost' => $shop->handling_cost,
        ];

        if (!$shop) {
            $data_shop['id_cart'] = $id_cart;
            $data_shop['id_shop'] = $id_shop;

            $shop = $this->CartShopTemporary->_insertShop($data_shop);
        } else {
            $data_shop['id_coupon'] = $shop->id_coupon;

            $shop = $this->_refreshCartShop($shop->id, $data_shop);
        }
        return TRUE;
    }

    public function _refreshCartShop($id_shop, $data_shop)
    {
        $sum_price = $data_shop['sum_price'];
        $voucher = $this->CartShopTemporary->getCartVoucherById($data_shop['id_coupon'], $sum_price);
        $cf     = Lpse_config::first();
        $ppn     = $cf->ppn / 100;
        $pph     = $cf->pph / 100;

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
        } else {
            $discount = '0';
        }

        if ($sum_price == null || $sum_price == '0') {
            $data_shop['id_coupon'] = null;
        }

        DB::table('cart_shop')
            ->where('id', $id_shop)
            ->update([
                'id_coupon' => $data_shop['id_coupon'],
                'discount' => $discount,
                'sum_price' => $data_shop['sum_price'],
                'sum_shipping' => $data_shop['sum_shipping'],
                'insurance_nominal' => $data_shop['insurance_nominal'],
                // 'handling_cost' => $data_shop['handling_cost'],
                'handling_cost_non_ppn' => $data_shop['handling_cost_non_ppn'],
                'ppn_shipping' => DB::raw('ROUND((sum_shipping + insurance_nominal) * ' . $ppn . ')'),
                'pph_shipping' => DB::raw('ROUND((sum_shipping + insurance_nominal) * ' . $pph . ')'),
                'subtotal' => DB::raw('sum_shipping + insurance_nominal + sum_price_non_ppn + handling_cost_non_ppn'),
                // 'total' => DB::raw('sum_price_non_ppn + ppn_price + sum_shipping + insurance_nominal + ppn_shipping  + handling_cost_non_ppn - discount')
            ]);

        // Manual update ppn & pph shipping
        $data_shop = DB::table('cart_shop')
            ->select('sum_shipping', 'insurance_nominal')
            ->where('id', $id_shop)
            ->first();

        if ($data_shop) {
            $ppn_shipping = round(($data_shop->sum_shipping + $data_shop->insurance_nominal) * $ppn);
            $pph_shipping = round(($data_shop->sum_shipping + $data_shop->insurance_nominal) * $pph);

            DB::table('cart_shop')
                ->where('id', $id_shop)
                ->update([
                    'ppn_shipping' => $ppn_shipping,
                    'pph_shipping' => $pph_shipping,
                    'total' => DB::raw('sum_price_non_ppn + ppn_price + sum_shipping + insurance_nominal + ppn_shipping  + handling_cost_non_ppn - discount')
                ]);
        }
    }
}
