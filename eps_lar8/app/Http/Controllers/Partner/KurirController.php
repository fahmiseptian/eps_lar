<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Libraries\JNE;
use App\Libraries\RPX;
use App\Libraries\SAP;
use App\Models\CompleteCartShop;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KurirController extends Controller {
    protected $Config;
    protected $Models;
    protected $Libraries;

    public function __construct()
    {
        $this->Config['ENVIRONMENT']    = getenv('ENVIRONMENT');

        // Models
        $this->Models['CCS'] = new CompleteCartShop();
        $this->Models['Shop'] = new Shop();

        // Libraries
        $this->Libraries['JNE'] = new JNE();
        $this->Libraries['RPX'] = new RPX();
        $this->Libraries['SAP'] = new SAP();
    }

    // Get Data order
    public function getDetailOrderPickup($id_cart_shop, $id_shop, $is_detail = false)
    {
        $new_data = [];

        $data = DB::table('complete_cart_shop as a')
            ->select(
                'b.invoice',
                'a.id_cart',
                'a.id AS id_cart_shop',
                'a.id_shop',
                'b.id_user AS id_member',
                'a.id_coupon',
                'a.id_address_shop',
                'b.id_address_user',
                'a.id_shipping',
                'a.is_insurance',
                'a.insurance_nominal',
                'a.base_rate',
                'a.sum_price',
                'a.sum_price_non_ppn',
                'a.ppn_price',
                'a.pph_price',
                'a.pph_shipping',
                'a.sum_shipping',
                'a.ppn_shipping',
                'a.total_weight',
                'a.qty',
                'a.discount',
                'a.subtotal',
                'a.total',
                'sp.id_courier',
                'sp.service as courier_service_code'
            )
            ->leftJoin('complete_cart as b', 'a.id_cart', '=', 'b.id')
            ->leftJoin('shipping as sp', 'sp.id', '=', 'a.id_shipping')
            ->where('a.id_shop', $id_shop)
            ->where('a.id', $id_cart_shop)
            ->first();

        if ($data) {
            $new_data['data'] = $data; // Tidak perlu diubah menjadi array, sudah berupa objek
            if ($is_detail) {
                $new_data['address']['shipper'] = $this->getMemberAddressById($data->id_address_shop);
                $new_data['address']['receiver'] = $this->getMemberAddressById($data->id_address_user);
                $new_data['detail'] = $this->getOrderProduct($data->id_cart, $data->id_shop);
            }
            return response()->json($new_data);
        }

        return response()->json(['error' => 'Data not found'], 404);
    }


    private function getMemberAddressById($id_address)
    {
        $data = DB::table('member_address as ma')
            ->select(
                'ma.member_address_id',
                'ma.province_id',
                'ma.city_id',
                'ma.subdistrict_id',
                'ma.address_name',
                'mm.email',
                'ma.address',
                'ma.phone',
                'p.province_name',
                'c.city_name',
                's.subdistrict_name',
                'ma.lat',
                'ma.lng',
                'ma.postal_code',
                'c.jne_dest_id as jne_district_code',
                's.sap_district_code'
            )
            ->join('member as mm', 'mm.id', '=', 'ma.member_id')
            ->leftJoin('province as p', 'p.province_id', '=', 'ma.province_id')
            ->leftJoin('city as c', 'c.city_id', '=', 'ma.city_id')
            ->leftJoin('subdistrict as s', 's.subdistrict_id', '=', 'ma.subdistrict_id')
            ->where('ma.member_address_id', $id_address)
            ->first();

        return $data ? (array) $data : false;
    }

    public function getOrderProduct($id_cart, $id_shop)
    {
        $data = DB::table('complete_cart_shop_detail as a')
            ->select(
                'a.id_cart',
                'a.id_shop',
                'a.id_product',
                'a.id AS id_temp',
                'a.price AS base_price',
                'a.nama AS product_name',
                'a.nominal_ppn',
                'a.nominal_pph',
                'a.qty',
                'a.weight',
                'a.total_weight',
                'a.total_non_ppn',
                'a.total',
                'a.status',
                'a.id_nego',
                'a.val_pph',
                'a.val_ppn',
                'a.input_price',
                'a.harga_dasar_lpse',
                'a.fee_mp_percent',
                'a.fee_mp_nominal',
                'a.fee_pg_nominal',
                'a.fee_pg_percent',
                'b.price'
            )
            ->leftJoin('products as b', 'b.id', '=', 'a.id_product')
            ->where('a.id_cart', $id_cart)
            ->where('a.id_shop', $id_shop)
            ->get();

        return !$data->isEmpty() ? $data->toArray() : false;
    }

    // End Get data Order

    // Pickup
    function anter(Request $request) {
		$id_courier = $request->id_courier;
		$id_order_shop = $request->id_order_shop;
		$id_shop 	= $this->Models['Shop']->getIdShopByOrder($id_order_shop);

		if ($id_courier == "1") {
			$create = $this->pickupJne($id_order_shop, $id_shop);
		} else if ($id_courier == "4") {
			$create = $this->pickup_rpx($id_order_shop, $id_shop);
		}

        return response()->json($create);
	}

    function pickup(Request $request){
        $id_courier = $request->id_courier;
		$id_order_shop = $request->id_order_shop;
		$id_shop 	= $this->Models['Shop']->getIdShopByOrder($id_order_shop);

        if ($id_courier == "4") {
			$create = $this->pickup_rpx2($id_order_shop, $id_shop);
		} else if ($id_courier == "6") {
			$create = $this->pickup_sap($id_order_shop, $id_shop);
		}

        return response()->json($create);
    }

    public function pickup_rpx($id_cart_shop, $id_shop)
    {
        $post_array = [];
        $is_data_exists = false;
        $is_insurance_ = 'N';

        // Ambil data dari getDetailOrderPickup
        $response = $this->getDetailOrderPickup($id_cart_shop, $id_shop, true);
        $data = json_decode($response->getContent(), true); // Ambil data dari JSON response

        if ($data && isset($data['data'])) {
            $is_data_exists = true;

            $data_order = $data['data'];
            $data_detail = $data['detail'];
            $data_shipper = $data['address']['shipper'];
            $data_receiver = $data['address']['receiver'];

            $insurance_cost = $data_order['insurance_nominal'];
            $goods_amounts = ($data_order['sum_price'] + $data_order['ppn_shipping']);

            if ($data_order['is_insurance'] == 1) {
                $is_insurance_ = 'Y';
                $goods_amounts += $insurance_cost;
            }

            $post_array['shipper_name']  = $data_shipper['address_name'];
            $post_array['shipper_company']  = $data_shipper['address_name'];
            $post_array['shipper_address1'] = $data_shipper['address'];

            $post_array['shipper_state']  = $data_shipper['province_name'];
            $post_array['shipper_city']  = $data_shipper['city_name'];
            $post_array['shipper_kecamatan']  = $data_shipper['subdistrict_name'];
            $post_array['shipper_zip']   = $data_shipper['postal_code'];
            $post_array['shipper_phone'] = $data_shipper['phone'];
            $post_array['shipper_mobile_no'] = $data_shipper['phone'];

            $post_array['consignee_name']  = $data_receiver['address_name'];
            $post_array['consignee_address1'] = $data_receiver['address'];

            $post_array['consignee_state']  = $data_receiver['province_name'];
            $post_array['consignee_city']  = $data_receiver['city_name'];
            $post_array['consignee_kecamatan']   = $data_receiver['subdistrict_name'];
            $post_array['consignee_zip']   = $data_receiver['postal_code'];
            $post_array['consignee_phone'] = $data_receiver['phone'];
            $post_array['consignee_mobile_no'] = $data_receiver['phone'];

            $post_array['service_type_id'] =  $data_order['courier_service_code'];
            $post_array['tot_weight'] = ceil($data_order['total_weight'] / 1000);

            $post_array['desc_of_goods'] = '';

            $post_array['insurance'] = $is_insurance_;

            foreach ($data_detail as $p) {
                if ($post_array['desc_of_goods'] == '') {
                    $post_array['desc_of_goods'] .= $p['product_name'];
                } else {
                    $post_array['desc_of_goods'] .= ', ' . $p['product_name'];
                }
            }

            $post_array['tot_declare_value'] = $goods_amounts;
        } else {
            return response()->json(['status' => 'error'], 404);
        }

        if ($is_data_exists) {
            $result = $this->Libraries['RPX']->send_shipmentData($post_array);

            if ($result) {
                if (isset($result['RPX']['DATA'])) {
                    for ($i = 0; $i < count($result['RPX']['DATA']); $i++) {
                        if ($result['RPX']['DATA'][$i]['RESULT'] == "Success") {
                            // Mengambil nomor AWB_RETURN
                            $no_awb = $result['RPX']['DATA'][$i]['AWB_RETURN'];

                            DB::table('complete_cart_shop')
                                ->where('id', $id_cart_shop)
                                ->where('id_shop', $id_shop)
                                ->update([
                                    'no_resi' => $no_awb,
                                    'status' => 'send_by_seller',
                                    'delivery_start' => now()
                                ]);
                            return response()->json(['status' => 'success'], 200);
                            // Memberikan respons JSON sukses
                            die(json_encode(array('status' => 'success')));
                        }
                    }
                } else {
                    // Jika 'DATA' tidak ada, menangani pesan kesalahan dari 'RESULT'
                    $msg = $result['RPX']['RESULT'] ?? 'Unknown';
                    return response()->json(['status' => 'error', 'msg' => $msg], 404);
                }
            }
        }
    }


    public function pickup_rpx2($id_cart_shop, $id_shop)
    {
        $post_array = [];
        $is_data_exists = false;
        $is_insurance_ = 'N';

        // Ambil data dari getDetailOrderPickup
        $response = $this->getDetailOrderPickup($id_cart_shop, $id_shop, true);
        $data = json_decode($response->getContent(), true); // Ambil data dari JSON response

        if ($data && isset($data['data'])) {
            $is_data_exists = true;

            $data_order = $data['data'];
            $data_detail = $data['detail'];
            $data_shipper = $data['address']['shipper'];
            $data_receiver = $data['address']['receiver'];

            $insurance_cost = $data_order['insurance_nominal'];
            $goods_amounts = ($data_order['sum_price'] + $data_order['ppn_shipping']);

            if ($data_order['is_insurance'] == 1) {
                $is_insurance_ = 'Y';
                $goods_amounts += $insurance_cost;
            }
            $post_array['order_type']  = 'MP';

            $post_array['shipper_name']  = $data_shipper['address_name'];
            $post_array['shipper_company']  = $data_shipper['address_name'];
            $post_array['shipper_address1'] = $data_shipper['address'];

            $post_array['shipper_state']  = $data_shipper['province_name'];
            $post_array['shipper_city']  = $data_shipper['city_name'];
            $post_array['shipper_kecamatan']  = $data_shipper['subdistrict_name'];
            $post_array['shipper_zip']   = $data_shipper['postal_code'];
            $post_array['shipper_phone'] = $data_shipper['phone'];
            $post_array['shipper_mobile_no'] = $data_shipper['phone'];

            $post_array['consignee_name']  = $data_receiver['address_name'];
            $post_array['consignee_address1'] = $data_receiver['address'];

            $post_array['consignee_state']  = $data_receiver['province_name'];
            $post_array['consignee_city']  = $data_receiver['city_name'];
            $post_array['consignee_kecamatan']   = $data_receiver['subdistrict_name'];
            $post_array['consignee_zip']   = $data_receiver['postal_code'];
            $post_array['consignee_phone'] = $data_receiver['phone'];
            $post_array['consignee_mobile_no'] = $data_receiver['phone'];

            $post_array['service_type_id'] =  $data_order['courier_service_code'];
            $post_array['tot_weight'] = ceil($data_order['total_weight'] / 1000);

            $post_array['desc_of_goods'] = '';

            $post_array['insurance'] = $is_insurance_;

            foreach ($data_detail as $p) {
                if ($post_array['desc_of_goods'] == '') {
                    $post_array['desc_of_goods'] .= $p['product_name'];
                } else {
                    $post_array['desc_of_goods'] .= ', ' . $p['product_name'];
                }
            }

            $post_array['tot_declare_value'] = $goods_amounts;
        } else {
            return response()->json(['status' => 'error'], 404);
        }

        if ($is_data_exists) {
            $result = $this->Libraries['RPX']->send_shipmentData($post_array);

            if ($result) {
                if (isset($result['RPX']['DATA'])) {
                    for ($i = 0; $i < count($result['RPX']['DATA']); $i++) {
                        if ($result['RPX']['DATA'][$i]['RESULT'] == "Success") {
                            // Mengambil nomor AWB_RETURN
                            $no_awb = $result['RPX']['DATA'][$i]['AWB_RETURN'];

                            DB::table('complete_cart_shop')
                                ->where('id', $id_cart_shop)
                                ->where('id_shop', $id_shop)
                                ->update([
                                    'no_resi' => $no_awb,
                                    'status' => 'send_by_seller',
                                    'delivery_start' => now()
                                ]);

                            $pickup_array['awb_numbers']  = $no_awb;
                            $pickup_array['pickup_request_by']  = $post_array['shipper_name'];
                            $pickup_array['pickup_company_address']  = $post_array['shipper_address1'];
                            $pickup_array['pickup_postal_code']  = $post_array['shipper_zip'];
                            $pickup_array['pickup_cellphone']  = $post_array['shipper_phone'];
                            $pickup_array['service_type']  = $post_array['service_type_id'];
                            $pickup_array['destin_postal_code']  = $post_array['consignee_zip'];
                            $pickup_array['tot_declare_value']  = $post_array['tot_declare_value'];
                            $pickup_array['total_weight']  = $post_array['tot_weight'];

                            $send_pickup    = $this->Libraries['RPX']->send_pickupRequest($pickup_array);

                            for ($i = 0; $i < count($result['RPX']['DATA']); $i++) {
                                if ($send_pickup['RPX']['DATA'][$i]['RESULT'] == "Success") {
                                    $res_pickup_number = $send_pickup['RPX']['DATA'][$i]['PICKUP_REQUEST_NO'];
                                    $res_pin = $send_pickup['RPX']['DATA'][$i]['PIN'];
                                    DB::table('complete_cart_shop')
                                        ->where('id', $id_cart_shop)
                                        ->where('id_shop', $id_shop)
                                        ->update([
                                            'pickup_number' => $res_pickup_number,
                                            'pin' => $res_pin,
                                        ]);
                                    return response()->json(['status' => 'success'], 200);
                                    // Memberikan respons JSON sukses
                                    die(json_encode(array('status' => 'success')));
                                }
                            }
                            return response()->json(['status' => 'success'], 200);
                        }
                    }
                } else {
                    // Jika 'DATA' tidak ada, menangani pesan kesalahan dari 'RESULT'
                    $msg = $result['RPX']['RESULT'] ?? 'Unknown';
                    return response()->json(['status' => 'error', 'msg' => $msg], 404);
                }
            }
        }
    }



    public function pickupJne($id_cart_shop, $id_shop)
    {
        $post_array = [];
        $is_data_exists = false;
        $is_insurance_ = 'N';

        // Ambil data dari getDetailOrderPickup
        $response = $this->getDetailOrderPickup($id_cart_shop, $id_shop, true);
        $data = json_decode($response->getContent(), true); // Ambil data dari JSON response

        if ($data && isset($data['data'])) {
            $is_data_exists = true;

            $data_order = $data['data'];
            $data_detail = $data['detail'];
            $data_shipper = $data['address']['shipper'];
            $data_receiver = $data['address']['receiver'];

            $insurance_cost = $data_order['insurance_nominal'];
            $goods_amounts = ($data_order['sum_price'] + $data_order['sum_shipping'] + $data_order['ppn_shipping']);

            if ($data_order['is_insurance'] == 1) {
                $is_insurance_ = 'Y';
                $goods_amounts += $insurance_cost;
            }

            $post_array['OLSHOP_ORDERID'] = $data_order['invoice'].'-'.$id_cart_shop;

            $post_array['OLSHOP_GOODSTYPE'] = '1';
            $post_array['OLSHOP_INS_FLAG'] = "N";
            $post_array['OLSHOP_COD_FLAG'] = "N";
            $post_array['OLSHOP_COD_AMOUNT'] = "0";

            $post_array['OLSHOP_SHIPPER_NAME'] = $data_shipper['address_name'];
            $post_array['OLSHOP_SHIPPER_ADDR1'] = $data_shipper['address'];
            $post_array['OLSHOP_SHIPPER_ADDR2'] = "";
            $post_array['OLSHOP_SHIPPER_CITY'] = $data_shipper['city_name'];
            $post_array['OLSHOP_SHIPPER_ZIP'] = $data_shipper['postal_code'];
            $post_array['OLSHOP_SHIPPER_PHONE'] = $data_shipper['phone'];
            $post_array['OLSHOP_ORIG'] = $data_shipper['jne_district_code'];

            $post_array['OLSHOP_RECEIVER_NAME'] = $data_receiver['address_name'];
            $post_array['OLSHOP_RECEIVER_ADDR1'] = $data_receiver['address'];
            $post_array['OLSHOP_RECEIVER_ADDR2'] = "";
            $post_array['OLSHOP_RECEIVER_CITY'] = $data_receiver['city_name'];
            $post_array['OLSHOP_RECEIVER_ZIP'] = $data_receiver['postal_code'];
            $post_array['OLSHOP_RECEIVER_PHONE'] = $data_receiver['phone'];
            $post_array['OLSHOP_DEST'] = $data_receiver['jne_district_code'];

            $post_array['OLSHOP_SERVICE'] = $data_order['courier_service_code'];
            $post_array['OLSHOP_QTY'] = $data_order['qty'];
            $post_array['OLSHOP_WEIGHT'] = ceil($data_order['total_weight'] / 1000);
            $post_array['OLSHOP_GOODSDESC'] = '';

            $post_array['OLSHOP_INS_FLAG'] = $is_insurance_;

            foreach ($data_detail as $p) {
                if ($post_array['OLSHOP_GOODSDESC'] == '') {
                    $post_array['OLSHOP_GOODSDESC'] .= $p['product_name'];
                } else {
                    $post_array['OLSHOP_GOODSDESC'] .= ', ' . $p['product_name'];
                }
            }

            $post_array['OLSHOP_GOODSVALUE'] = $goods_amounts;

        } else {
            return response()->json(['status' => 'error'], 404);
        }

        if ($is_data_exists) {
            $result = $this->Libraries['JNE']->generateCnote($post_array);

            if ($result) {
                DB::table('complete_cart_shop')
                    ->where('id', $id_cart_shop)
                    ->where('id_shop', $id_shop)
                    ->update([
                        'no_resi' => $result,
                        'status' => 'send_by_seller',
                        'delivery_start' => now()
                    ]);

                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error', 'message' => $result['message'] ?? 'Unknown error']);
            }
        }

        return false;
    }

    function pickup_sap($id_cart_shop, $id_shop) {
        $post_array = [];
        $is_data_exists = false;
        $is_insurance_ = 1;

        // Ambil data dari getDetailOrderPickup
        $response = $this->getDetailOrderPickup($id_cart_shop, $id_shop, true);
        $data = json_decode($response->getContent(), true); // Ambil data dari JSON response

        if ($data && isset($data['data'])) {
            $is_data_exists = true;

            $salt_unique_code = 'EPS' . $id_cart_shop . now()->format('YmdHis');

            $data_order = $data['data'];
            $data_detail = $data['detail'];
            $data_shipper = $data['address']['shipper'];
            $data_receiver = $data['address']['receiver'];

            $insurance_cost = $data_order['insurance_nominal'];
            $goods_amounts = ($data_order['sum_price'] + $data_order['ppn_shipping']);

            if ($data_order['is_insurance'] == 1) {
                $is_insurance_ = 2;
                // $goods_amounts += $insurance_cost;
            }

            $post_array['reference_no']  = $data_order['invoice'].'-'.$id_cart_shop;
            $post_array['pickup_name']  = $data_shipper['address_name'];
            $post_array['pickup_address'] = $data_shipper['address'];
            $post_array['pickup_phone'] = $data_shipper['phone'];

            $post_array['pickup_latitude'] = round($data_shipper['lat'], 6);
            $post_array['pickup_longitude'] = round($data_shipper['lng'], 6);
            $post_array['pickup_district_code'] = $data_shipper['sap_district_code'];

            $post_array['shipper_name']  = $data_shipper['address_name'];
            $post_array['shipper_address'] = $data_shipper['address'];
            $post_array['shipper_phone'] = $data_shipper['phone'];

            $post_array['receiver_name']  = $data_receiver['address_name'];
            $post_array['receiver_address'] = $data_receiver['address'];
            $post_array['receiver_phone'] = $data_receiver['phone'];
            $post_array['destination_district_code'] = $data_receiver['sap_district_code'];

            $post_array['service_type_code'] =  $data_order['courier_service_code'];
            $post_array['weight'] = ceil($data_order['total_weight'] / 1000);

            // is_shipping_insurance
            $post_array['insurance_flag'] = $is_insurance_;
            $post_array['insurance_value'] = $insurance_cost;
            $post_array['goods_desc']   = '';

            foreach ($data_detail as $p) {
                if ($post_array['goods_desc'] == '') {
                    $post_array['goods_desc'] .= $p['product_name'];
                } else {
                    $post_array['goods_desc'] .= ', ' . $p['product_name'];
                }
            }

            $post_array['quantity'] = 1;
            $post_array['item_value'] = $goods_amounts;
        }else {
            return response()->json(['status' => 'error'], 404);
        }

        if ($is_data_exists) {
            $result = $this->Libraries['SAP']->pickup_order($post_array);

            if ($result['status'] == 'success') {
                $no_awb = $result['no_resi'];

                DB::table('complete_cart_shop')
                    ->where('id', $id_cart_shop)
                    ->where('id_shop', $id_shop)
                    ->update([
                        'no_resi' => $no_awb,
                        'pickup_number' => $data_order['invoice'].'-'.$id_cart_shop,
                        'status' => 'send_by_seller',
                        'delivery_start' => now()
                    ]);

                return response()->json(['status' => 'success'], 200);
            }
            return $result;
        }
        return response()->json(['status' => 'error'], 404);
    }


    // Tracking
    function Tracking(Request $request) {
        $id_courier = $request->id_courier;
        $resi       = $request->resi;

        if ($id_courier == 1) {
            $track = $this->Libraries['JNE']->tracking($resi);
        } elseif ($id_courier == 4) {
            $track = $this->Libraries['RPX']->tracking($resi);
        } elseif ($id_courier == 6) {
            $track = $this->Libraries['SAP']->tracking($resi);
        }

        return $track;
    }
}
