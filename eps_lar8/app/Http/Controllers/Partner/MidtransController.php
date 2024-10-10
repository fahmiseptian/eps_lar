<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Libraries\Lpse;
use App\Libraries\Midtrans;
use App\Models\Cart;
use App\Models\Invoice;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    protected $data;
    protected $libraries;

    public function __construct(Request $request)
    {
        $token = $request->input('token');
        $this->data['is_lpse'] = 0;
        if ($token != null) {
            $lpse = new Lpse();
            $this->data['is_lpse'] = 1;
            $cek_token = $lpse->check_token($token);
        }
        // Load Midtrans Library
        $this->libraries['Midtrans'] = new Midtrans();
    }


    function payment_request(Request $request)
    {
        $id_cart = $request->input('id_cart');
        $cond = $request->input('cond');

        $member_id = $request->input('member_id');


        $data_cart = $this->libraries['Midtrans']->getDataInvoice($id_cart);
        if ($data_cart == null) {
            return response()->json([
                'code' => 404,
                'error' => 'Invoice tidak ditemukan'
            ]);
        }

        $invoice = $data_cart->invoice;
        $mid_order_id = strtotime(date('Y-m-d H:i:s')) . '-' . $id_cart;
        $enable_payments = array('credit_card');

        $data_item = [];

        if (!empty($cond)) {
            if ($cond == 'payment') {
                $checkout = $this->libraries['Midtrans']->get_trans_detail_data($id_cart);

                if (!empty($checkout)) {
                    $address_arr = $checkout['address'];
                    $shop = $checkout['shop'];

                    $address_shipping = $address_arr['shipping'];
                    $address_billing = $address_arr['billing'];

                    $cart_id = $checkout['id_cart'];
                    $cart_invoice = $checkout['invoice'];
                    $cart_status = $checkout['status'];
                    $cart_create_date = $checkout['created_date'];
                    $cart_last_update_date = $checkout['last_update'];
                    $cart_id_payment = $checkout['id_payment'];
                    $cart_due_date_payment = $checkout['due_date_payment'];
                    $cart_payment_name = $checkout['payment_name'];
                    $cart_note = $checkout['note'];

                    $cart_sum_price_non_ppn = $checkout['sum_price_non_ppn'];
                    $cart_sum_shipping = $checkout['sum_shipping'];
                    $cart_sum_shipping_non_ppn = $checkout['sum_shipping_non_ppn'];
                    $cart_total_ppn = $checkout['total_ppn'];
                    $cart_sum_discount = $checkout['sum_discount'];
                    $cart_handling_cost = $checkout['handling_cost'];
                    $cart_total_all = $checkout['total']; // include ongkir, ppn, diskon, etc

                    $cart_va_number = $checkout['va_number'];
                    $cart_status_pembayaran_top = $checkout['status_pembayaran_top'];


                    $final_price = $cart_total_all;

                    // NOTE Data Shop
                    foreach ($shop as $key => $val) {
                        // NOTE Shop Data
                        $shop_data = $val['data'];
                        $shop_detail = $val['detail'];

                        $shop_id_ = $shop_data['id'];
                        $shop_id = $shop_data['id_shop'];
                        $shop_name = $shop_data['shop_name'];
                        $shop_qty = $shop_data['qty'];
                        $shop_total_price = $shop_data['sum_price'];
                        $shop_total_shipping = $shop_data['sum_shipping'];
                        $shop_discount = $shop_data['discount'];
                        $shop_total_shop = $shop_data['total_shop'];

                        $shop_insurance_nominal = $shop_data['insurance_nominal'];
                        $shop_ppn_shipping = $shop_data['ppn_shipping'];

                        $shop_total_weight = $shop_data['total_weight'];
                        $shop_status = $shop_data['status'];
                        $shop_no_awb = $shop_data['no_awb'];
                        $shop_id_courier = $shop_data['id_courier'];
                        $shop_is_bast = $shop_data['is_bast'];

                        $shop_note = $shop_data['note'];
                        $shop_note_seller = $shop_data['note_seller'];
                        $shop_shipping_desc = $shop_data['shipping_desc'];
                        $shop_shipping_service = $shop_data['shipping_service'];
                        $shop_shipping_etd = $shop_data['shipping_etd'];

                        $shop_id_coupon = $shop_data['id_coupon'];
                        $shop_code_coupon = $shop_data['code_coupon'];
                        $shop_name_coupon = $shop_data['name_coupon'];

                        // NOTE Data Shop Item
                        foreach ($shop_detail as $keyc => $valc) {
                            $shop_detail_id = $valc['id'];
                            $shop_detail_product_id = $valc['product_id'];
                            $shop_detail_product_id_nego = $valc['id_nego'];
                            $shop_detail_product_name = $valc['product_name'];
                            $shop_detail_product_image = $valc['product_image'];
                            $shop_detail_product_price = $valc['product_price'];
                            $shop_detail_product_qty = $valc['product_qty'];
                            $shop_detail_product_seoname = $valc['product_seoname'];
                            $shop_detail_product_is_ppn = $valc['is_ppn'];

                            $shop_detail_total_price = $valc['total'];
                            $shop_detail_total_weight = $valc['total_weight'];
                            $shop_detail_status = $valc['status'];

                            $data_item[] = array(
                                "id"             => $shop_detail_id,
                                "price"            => $shop_detail_product_price,
                                "quantity"         => (int)$shop_detail_product_qty,
                                "name"            => substr($shop_detail_product_name, 0, 30),
                            );
                        }
                    }

                    $data_item[] = array(
                        "id"                => 'BP1',
                        "price"                => $cart_sum_shipping,
                        "quantity"             => '1',
                        "name"                => 'Biaya Pengiriman',
                    );

                    $data_item[] = array(
                        "id"                 => 'BL1',
                        "price"                => $cart_handling_cost,
                        "quantity"             => '1',
                        "name"                => 'Biaya Layanan',
                    );

                    $data_item[] = array(
                        "id"                 => 'TD1',
                        "price"                => -$cart_sum_discount,
                        "quantity"             => '1',
                        "name"                => 'Total Discount',
                    );
                }
            }
        }

        $transaction_details = array(
            'order_id'         => $mid_order_id,
            'gross_amount'     => $final_price,
        );
        $transaction = array(
            'enabled_payments' => $enable_payments,
            // 'customer_details' => $customer_details,
            'transaction_details' => $transaction_details,
            'item_details' => $data_item,
            'credit_card' => array('secure' => true, 'saved_card' => true),
            'user_id' => $member_id ? $member_id : $data_cart->id_member,
        );

        $midtrans_log_request = array(
            'id_order'     => $transaction_details['order_id'],
            'id_cart'     => $id_cart,
            'param'         => json_encode($transaction),
            'flag'             => 'midtrans',
        );

        $insert_log_request     = $this->libraries['Midtrans']->insertMidRequest($midtrans_log_request);
        // return $transaction;
        $dataSnap =  $this->libraries['Midtrans']->get_snapToken($transaction);

        return response()->json($dataSnap);
    }

    public function get_refresh_token(Request $request)
    {
        $id_cart = $request->input('id_cart');
        $cond = $request->input('cond');
        $member_id = $request->input('member_id');

        $complete_cart = new Invoice();
        $carts = new Cart();

        $new_token = '';
        if (!empty($cond)) {
            if ($cond == 'payment') {
                $data = array('id' => $id_cart);
                $migrate_checkout = $carts->migrate_checkout($data);
            }
        } else {
            $migrate_checkout = $complete_cart->migrate_cart_checkout_cond($member_id, $id_cart);
        }

        $token_lpse = $this->libraries['Midtrans']->getLpseTokenReq($id_cart);
        $get_stat = $this->libraries['Midtrans']->get_status($id_cart);

        $data_status     = array(
            'status'            => $get_stat['payment_status'],
            'payment_detail'    => strtoupper($get_stat['payment_method']),
        );

        $update         = $this->libraries['Midtrans']->FinalchangePaymentStatus($id_cart, $data_status);

        if ( $this->data['is_lpse'] == 1) {
            $lpse = new Lpse();
            $cek_token = $lpse->confirm_trans($id_cart);
        }
        return response()->json($get_stat);
    }


    function send_email(Request $request)
    {
        $id_cart = $request->input('id_cart');
        $data_cart = $this->libraries['Midtrans']->send_email($id_cart);
        return response()->json($data_cart);
    }



    function test(Request $request)
    {
        $id_cart = $request->input('id_cart');
        $data_cart = $this->libraries['Midtrans']->get_trans_detail_data($id_cart);
        return response()->json($data_cart);
    }
}
