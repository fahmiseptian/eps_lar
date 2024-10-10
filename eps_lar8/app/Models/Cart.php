<?php

namespace App\Models;

use App\Libraries\nusoap_client;
use CurlHandle;
use Illuminate\Database\Eloquent\Model;
use App\Libraries\Calculation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Cart extends Model
{
    protected $table = 'cart';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'id_cart',
        'invoice',
        'id_address_user',
        'id_voucher',
        'sum_price',
        'sum_price_non_ppn',
        'sum_shipping_non_ppn',
        'sum_shipping',
        'sum_discount',
        'coin_usage',
        'coin_reward',
        'qty',
        'discount',
        'handling_cost',
        'total',
        'total_non_ppn',
        'sum_shipping',
        'total_ppn',
        'total_pph',
        'val_ppn',
        'val_pph',
        'id_shop',
        'id_product',
        'price',
        'weight',
        'id_user',
        'qty',
        'is_selected',
        'nama',
        'image',
        'fee_mp_percent',
        'fee_mp_nominal',
        'fee_pg_percent',
        'fee_pg_nominal',
        'val_ppn',
        'val_pph',
        'total_weight',
        'total',
        'input_price',
        'nominal_ppn',
        'nominal_pph',
        'harga_dasar_lpse',
        'total_non_ppn',
        'id_nego',
    ];

    public function getIdCartbyidmember($id_user)
    {
        $data = self::select('id')
            ->where('complete_checkout', 'N')
            ->where('id_user', $id_user)
            ->first();

        if (!empty($data->id)) {
            return $data->id;
        } else {
            $id_address = DB::table('member_address')
                ->where('member_id', $id_user)
                ->where('is_default_shipping', 'yes')
                ->value('member_address_id');

            $cart = Cart::create([
                'id_user' => $id_user,
                'invoice' => 0,
                'id_address_user' => $id_address,
                'id_voucher' => 0,
                'sum_price' => 0,
                'sum_price_non_ppn' => 0,
                'sum_shipping_non_ppn' => 0,
                'sum_shipping' => 0,
                'sum_discount' => 0,
                'coin_usage' => 0,
                'qty' => 0,
                'coin_reward' => 0,
                'discount' => 0,
                'handling_cost' => 0,
                'total' => 0,
                'total_non_ppn' => 0,
                'total_ppn' => 0,
                'total_pph' => 0,
                'val_ppn' => 0,
                'val_pph' => 0,
            ]);

            return $cart->id;
        }
    }

    function getCart($id_user)
    {
        $query = self::select('*')
            ->where('complete_checkout', 'N')
            ->where('id_user', $id_user)
            ->first();

        if ($query->id_address_user == 0) {
            $id_address = DB::table('member_address')
                ->where('member_id', $id_user)
                ->where('is_default_shipping', 'yes')
                ->value('member_address_id');

            $update = Cart::where('id', $query->id)->update(['id_address_user' => $id_address]);
        }
        return $query;
    }

    function getaddressCart($id_address_user)
    {
        $data = DB::table('member_address as ma')
            ->select(
                'ma.member_address_id',
                'ma.phone',
                'ma.address_name',
                'ma.address',
                'ma.postal_code',
                'ma.province_id',
                'ma.city_id',
                'ma.subdistrict_id',
                'p.province_name',
                's.subdistrict_name',
                'c.city_name as city',
            )
            ->where('ma.member_address_id', $id_address_user)
            ->join('province as p', 'p.province_id', 'ma.province_id')
            ->join('city as c', 'ma.city_id', 'c.city_id')
            ->join('subdistrict as s', 's.subdistrict_id', 'ma.subdistrict_id')
            ->first();
        return $data;
    }

    function getCartDetails($id_cart, $id_shop)
    {
        $query = DB::table('cart as c')
            ->select(
                'cs.id_shop',
                'seller_ma.city_id as id_kota_seller',
                'seller_ma.postal_code as code_pos_seller',
                'cr.id as id_courier',
                'cr.name as jasa_courier',
                'buyer_ma.subdistrict_id as distrik_pembeli',
                'buyer_ma.postal_code as code_pos_pembeli'
            )
            ->join('cart_shop as cs', 'cs.id_cart', 'c.id')
            ->join('shop as s', 's.id', 'cs.id_shop')
            ->join('member_address as seller_ma', 'seller_ma.member_id', 's.id_user')
            ->join('shop_courier as sc', 'sc.id_shop', 's.id')
            ->join('courier as cr', 'cr.id', 'sc.id_courier')
            ->join('member_address as buyer_ma', 'buyer_ma.member_address_id', 'c.id_address_user')
            ->where('c.id', $id_cart)
            ->where('cs.id_shop', $id_shop)
            ->where('seller_ma.active_status', 'active')
            ->where('cr.status', 'Y')
            ->get();

        return $query;
    }

    function getRates($id_cart, $id_shop)
    {
        $data = DB::table('shop_courier as sc')
            ->select([
                'c.id as courier_id',
                'c.code as courier',
                'c.service as courier_service',
                DB::raw("(SELECT ma.city_id FROM cart_shop cs LEFT JOIN member_address ma ON ma.member_address_id = cs.id_address_shop WHERE cs.id_shop = $id_shop AND id_cart = $id_cart) as id_city_origin"),
                DB::raw("(SELECT ma.subdistrict_id FROM cart LEFT JOIN member_address ma ON ma.member_address_id = cart.id_address_user WHERE cart.id = $id_cart) as id_subdistrict_dest"),
                DB::raw("(SELECT sd.zip_code_dist FROM cart LEFT JOIN member_address ma ON ma.member_address_id = cart.id_address_user LEFT JOIN subdistrict sd ON sd.subdistrict_id = ma.subdistrict_id WHERE cart.id = $id_cart) as zip_destination"),
                DB::raw("(SELECT sd.zip_code_dist FROM cart_shop cs LEFT JOIN member_address ma ON ma.member_address_id = cs.id_address_shop LEFT JOIN subdistrict sd ON sd.subdistrict_id = ma.subdistrict_id WHERE cs.id_shop = $id_shop AND id_cart = $id_cart) as zip_origin"),
                DB::raw("(SELECT sd.sap_district_code FROM cart_shop cs LEFT JOIN member_address ma ON ma.member_address_id = cs.id_address_shop LEFT JOIN subdistrict sd ON sd.subdistrict_id = ma.subdistrict_id WHERE cs.id_shop = $id_shop AND id_cart = $id_cart) as sap_origin"),
                DB::raw("(SELECT sd.sap_district_code FROM cart LEFT JOIN member_address ma ON ma.member_address_id = cart.id_address_user LEFT JOIN subdistrict sd ON sd.subdistrict_id = ma.subdistrict_id WHERE cart.id = $id_cart) as sap_destination"),
                DB::raw("(SELECT sd.destination_code FROM cart LEFT JOIN member_address ma ON ma.member_address_id = cart.id_address_user LEFT JOIN subdistrict sd ON sd.subdistrict_id = ma.subdistrict_id WHERE cart.id = $id_cart) as jne_destination"),
                DB::raw("(SELECT city.jne_dest_id FROM cart_shop LEFT JOIN member_address ma ON ma.member_address_id = cart_shop.id_address_shop LEFT JOIN city ON city.city_id = ma.city_id WHERE cart_shop.id_cart = $id_cart AND id_shop = $id_shop) as jne_origin"),
                DB::raw("(SELECT total_weight FROM cart_shop WHERE id_shop = $id_shop AND id_cart = $id_cart AND total_weight != 0) as total_weight")
            ])
            ->leftJoin('courier as c', 'c.id', '=', 'sc.id_courier')
            ->where('sc.id_shop', $id_shop)
            ->get();

        $ship_after = collect();

        foreach ($data as $d) {
            $ship_before = $this->cekShipping($d->courier_id, $d->id_city_origin, $d->id_subdistrict_dest, null);

            if (!empty($d->id_subdistrict_dest)) {
                if ($d->courier == 'sap') {
                    $this->sap_get_rates($d->zip_origin, $d->zip_destination, '1000', $d->courier, $d->courier_id, $d->id_city_origin, $d->id_subdistrict_dest, $d->sap_origin, $d->sap_destination, $d->courier_service);
                }
                if ($d->courier == 'rpx') {
                    $this->rpx_get_rates($d->zip_origin, $d->zip_destination, '1000', $d->courier, $d->courier_id, $d->id_city_origin, $d->id_subdistrict_dest, $d->courier_service);
                }
                if ($d->courier == 'jne') {
                    $this->jne_get_rates($d->zip_origin, $d->zip_destination, '1000', $d->courier, $d->courier_id, $d->id_city_origin, $d->id_subdistrict_dest, $d->sap_origin, $d->sap_destination, $d->courier_service, $d->jne_destination, $d->jne_origin);
                }
            }

            $shipping_data = $this->getShipping($d->courier_id, $d->zip_origin, $d->zip_destination, $id_shop);
            $ship_after = $ship_after->merge($shipping_data);
            $id_subdistrict_dest = $d->id_subdistrict_dest;
        }

        $ship_after = $ship_after->merge($this->getFreeShippingProvince($id_shop, $id_subdistrict_dest));

        // Sort the collection by the created_at timestamp or ID if available
        $ship_after = $ship_after->sortByDesc('created_dt');

        // Filter the results to get only unique entries based on 'deskripsi'
        $unique_ship_after = $ship_after->unique('deskripsi');

        // Reverse the array and return it
        $ship_result = $unique_ship_after->values()->all();
        // $ship_result->sum_shipping = 0;

        foreach ($ship_result as $sr) {
            $calculation = new Calculation();
            $ongkir_akhir = $calculation->OngkirSudahPPN($sr->price);
            $sum_shipping = $ongkir_akhir['ongkir_sudah_ppn_dan_pph'];
            $sr->sum_shipping = $sum_shipping;
        }

        return $ship_result;
    }

    function insertHandlingCost($id_user, $ppn = null)
    {
        $calculation = new Calculation();
        $cart = Cart::where('id_user', $id_user)->first();
        $id_cart = $cart->id;
        $cl = Lpse_config::first();
        $dataSave = [];

        $id_payment = $cart->id_payment;
        $data_tr_shop = DB::table('cart_shop')
            ->where('id_cart', $id_cart)
            ->get()
            ->toArray();

        if ($id_payment == 0 && $cart->total_ppn == 0) {
            $pay = Payment::where('active', 'Y')
                ->orderBy('id', 'asc')
                ->first();
        } else {
            $pay = Payment::find($id_payment);
        }

        $fee_nominal = $pay->fee_nominal;
        $fee_percent = $pay->fee_percent;
        $ppn = $cl->ppn;

        $tot_ppn = $cart->sum_shipping * ($cl->ppn / 100);
        $total_fee = ceil($tot_ppn);
        $id_payment = $pay->id;

        $total_handling_cost = 0;
        $total_handling_cost_non_ppn = 0;
        $total_price_gateway_fee_cut = 0;
        $total_pembayaran_final = 0;
        $ppn_total_final = 0;

        if (!empty($data_tr_shop)) {
            foreach ($data_tr_shop as $val) {
                $id_tr_shop = $val->id;
                $sum_price_non_ppn = $val->sum_price_non_ppn;
                $sum_shipping_non_ppn = $val->sum_shipping + $val->insurance_nominal;

                $sum_price_ppn = $val->sum_price_ppn;
                $ppn_shipping = $val->ppn_shipping;

                $dataArr = [
                    'fee_nominal' => $fee_nominal,
                    'fee_percent' => $fee_percent,
                    'ppn' => $ppn,
                    'sum_price' => $sum_price_non_ppn,
                    'sum_price_ppn_only' => $sum_price_ppn,
                    'sum_shipping' => $sum_shipping_non_ppn,
                ];

                if ($fee_nominal != 0 || $fee_percent != 0) {
                    $result_calc = $calculation->calc_handling_cost($dataArr);
                    $ppn_total = $result_calc['ppn_total'];
                    $total_pembayaran = $result_calc['total_pembayaran'];

                    $handling_cost = $result_calc['subtotal_mdr_fee_include'];
                    $handling_cost_non_ppn = $result_calc['subtotal_mdr_fee_exclude'];
                    $price_gateway_fee_cut = $result_calc['price_gateway_fee'];
                } else {
                    $handling_cost = 0;
                    $handling_cost_non_ppn = 0;
                    $price_gateway_fee_cut = 0;
                    $ppn_total = round(($sum_price_non_ppn + $sum_shipping_non_ppn) * ($ppn / 100));

                    $total_pembayaran = $sum_price_non_ppn + $sum_shipping_non_ppn + $ppn_total;
                }

                $total_handling_cost += $handling_cost;
                $total_handling_cost_non_ppn += $handling_cost_non_ppn;
                $total_price_gateway_fee_cut += $price_gateway_fee_cut;
                $total_pembayaran_final += $total_pembayaran;
                $ppn_total_final += $ppn_total;

                $dataSave[] = [
                    'id' => $id_tr_shop,
                    'handling_cost_fee_nominal' => $fee_nominal,
                    'handling_cost_fee_percent' => $fee_percent,
                    'handling_cost' => $handling_cost,
                    'handling_cost_non_ppn' => $handling_cost_non_ppn,
                    'handling_cost_fee_nominal_cut' => $price_gateway_fee_cut,
                    'ppn_total' => $ppn_total,
                ];
            }

            foreach ($dataSave as $data) {
                DB::table('cart_shop')
                    ->where('id', $data['id'])
                    ->update($data);
            }
        }
        $data_cart      = array(
            'id_payment' => $id_payment,
            'total_ppn' => $ppn_total_final,
            'total' => $total_pembayaran_final,
            'handling_cost_fee_nominal' => $fee_nominal,
            'handling_cost_fee_percent' => $fee_percent,

            'handling_cost' => $total_handling_cost,
            'handling_cost_non_ppn' => $total_handling_cost_non_ppn,
            'handling_cost_fee_nominal_cut' => $total_price_gateway_fee_cut,
        );
        $updatecart = CartShopTemporary::_updateCart($id_cart, $data_cart);
        $this->update_cart_temp($id_cart);
        return $data_cart;
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

    public function getShipping($id_courier, $zip_origin, $zip_destination, $id_shop)
    {
        $ship = DB::table('shipping')
            ->select('shipping.*', 'courier.code')
            ->leftJoin('courier', 'courier.id', '=', 'shipping.id_courier')
            ->where('id_courier', $id_courier)
            ->where('zip_origin', $zip_origin)
            ->where('zip_destination', $zip_destination)
            ->get();

        return $ship;
    }

    public function insertRates($data_rates)
    {
        DB::table('shipping')->insert($data_rates);
    }


    // RPX
    public function rpx_get_rates($zip_origin, $zip_destination, $weight, $courier, $courier_id, $id_city_origin, $id_subdistrict_dest, $service)
    {
        $service_array = explode(",", $service);
        $response = $this->_rpx_get_rates($zip_origin, $zip_destination, $weight);
        Log::info("response RPX", [$response]);
        if ($response) {
            foreach ($response['RPX']['DATA'] as $costs) {
                if (!empty($costs['TOT_CHARGE']) || $costs['TOT_CHARGE'] > 0) {
                    if (in_array($costs['SERVICE_TYPE_ID'], $service_array)) {
                        $data_rates = [
                            'id_courier'            => $courier_id,
                            'id_city_origin'        => $id_city_origin,
                            'id_subdistrict_dest'   => $id_subdistrict_dest,
                            'price'                 => $costs['TOT_CHARGE'],
                            'etd'                   => round($costs['ETD']),
                            'deskripsi'             => 'RPX ' . $costs['SERVICE'],
                            'service'               => $costs['SERVICE_TYPE_ID'],
                            'zip_destination'       => $zip_destination,
                            'zip_origin'            => $zip_origin,
                        ];
                        $this->insertRates($data_rates);
                    }
                }
            }
        }
    }

    public function _rpx_get_rates($zip_origin, $zip_destination, $weight)
    {
        $wsdl = "http://api.rpxholding.com/wsdl/rpxwsdl.php?wsdl";
        $client = new nusoap_client($wsdl, true);
        $env = env('ENVIRONMENT') ? env('ENVIRONMENT') : 'development';
        $rpx  = [
            'endpoint'      => 'http://api.rpxholding.com/wsdl/rpxwsdl.php?wsdl',
            'username'      => 'demo',
            'password'      => 'demo',
            'account_no'    => '234098705',
        ];

        if ($env == 'production') {
            $rpx  = [
                'endpoint'      => 'http://api.rpxholding.com/wsdl/rpxwsdl.php?wsdl',
                'username'      => 'eliteproxy',
                'password'      => '3liTePr0XyS15TEm',
                'account_no'    => '758078721',
            ];
        }

        if ($client->getError()) {
            return false; // Gagal membuat objek client, kembalikan false
        }

        $post_array = [
            'user'                      => $rpx['username'],
            'password'                  => $rpx['password'],
            'account_number'            => $rpx['account_no'],
            'origin_postal_code'        => $zip_origin,
            'destination_postal_code'   => $zip_destination,
            'weight'                    =>  $weight,
            'format'                    => 'json'
        ];

        $result = $client->call('getRates', $post_array);
        $this->insert_rpx_log_req($post_array, $result);
        if ($client->fault) {
            return false;
        } else {
            $data = json_decode($result, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return false;
            } else {
                return $data;
            }
        }
    }


    public function insert_rpx_log_req($post_array, $result)
    {
        DB::table('rpx_log')->insert([
            'payload' => json_encode($post_array),
            'response' => $result,
            'action' => 'GET_RATES'
        ]);
    }



    // JNE
    public function jne_get_rates($zip_origin, $zip_destination, $weight, $courier, $courier_id, $id_city_origin, $id_subdistrict_dest, $sap_origin, $sap_destination, $service, $jne_destination, $jne_origin)
    {
        $service_array = explode(",", $service);
        $response = $this->_jne_get_rates($jne_origin, $jne_destination, $weight);
        if ($response != null) {
            foreach ($response['price'] as $price) {
                if (!empty($price['price']) && in_array($price['service_display'], $service_array)) {
                    // Konversi harga menjadi dalam satuan yang benar
                    $convertedPrice = $price['price'];

                    $data_rates = [
                        'id_courier'            => $courier_id,
                        'id_city_origin'        => $id_city_origin,
                        'id_subdistrict_dest'   => $id_subdistrict_dest,
                        'price'                 => $convertedPrice,  // Menggunakan harga yang sudah dikonversi
                        'etd'                   => $price['etd_from'] . "-" . $price['etd_thru'],
                        'deskripsi'             => 'JNE ' . ucwords(strtolower($price['service_display'])),
                        'service'               => $price['service_code'],
                        'zip_destination'       => $zip_destination,
                        'zip_origin'            => $zip_origin,
                    ];
                    $insert = $this->insertRates($data_rates);
                }
            }
        }
    }

    public function _jne_get_rates($jne_origin, $jne_destination, $weight)
    {
        $env = env('ENVIRONMENT') ? env('ENVIRONMENT') : 'development';

        $jne = [
            'endpoint'  => 'http://apiv2.jne.co.id:10102/',
            'username'  => 'TESTAPI',
            'apikey'    => '25c898a9faea1a100859ecd9ef674548',
            'cust_no'   => 'TESTAKUN',
            'branch'    => 'CGK000',
        ];

        if ($env != 'development') {
            $jne = [
                'endpoint' => 'https://apiv2.jne.co.id:10205/',
                'username' => 'ELITEPROXY',
                'apikey'   => 'a2166df97bc330831f0c4e2cf4fb60b4',
                'cust_no'  => '11953800',
                'branch'   => 'CGK000',
            ];
        }

        $post_array = [
            'username'  => 'TESTAPI',
            'api_key'   => '25c898a9faea1a100859ecd9ef674548',
            'from'      => 'CGK10000',
            'thru'      => 'CGK10104',
            'weight'    =>  1,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $jne['endpoint'] . 'tracing/api/pricedev');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_array));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result, true);
        // print_r($result);
        // exit();

        Log::info('Response from JNE API', ['response' => $result]);
        Log::info('Payload to JNE API', ['response' => $post_array]);
        // Log::info('Parsed response from JNE API', $result);

        $this->insert_jne_log_req($post_array, $result);
        return $data;
    }

    public function insert_jne_log_req($post_array, $result)
    {
        JneLog::create([
            'payload' => json_encode($post_array),
            'response' => json_encode($result),
            'action' => 'CEK_TARIF'
        ]);
    }


    // SAP
    public function sap_get_rates($zip_origin, $zip_destination, $weight, $courier, $courier_id, $id_city_origin, $id_subdistrict_dest, $sap_origin, $sap_destination, $service)
    {
        $service_array = explode(",", $service);
        $response = $this->_sap_get_rates($sap_origin, $sap_destination, $weight);
        Log::info("response SAP", [$response]);
        if ($response) {
            foreach ($response['price_detail'] as $costs) {
                if ($costs['minimum_kilo'] == '1') {
                    if (in_array($costs['service_type_code'], $service_array)) {
                        $data_rates = array(
                            'id_courier'             => $courier_id,
                            'id_city_origin'         => $id_city_origin,
                            'id_subdistrict_dest'     => $id_subdistrict_dest,
                            'price'                 => $costs['price'],
                            'etd'                     => round($costs['sla']),
                            'deskripsi'             => 'SAP ' . ucwords(strtolower($costs['service_type_name'])),
                            'service'                 => $costs['service_type_code'],
                            'zip_destination'        => $zip_destination,
                            'zip_origin'            => $zip_origin,
                        );
                        $this->insertRates($data_rates);
                    }
                }
            }
        }
    }

    public function _sap_get_rates($sap_origin, $sap_destination, $weight)
    {
        $url = 'https://api.coresyssap.com/master/shipment_cost/publish';
        $apiKey = 'global';
        $customerCode = 'CGK057057';

        $post_array = [
            'origin'        => $sap_origin,
            'destination'   => $sap_destination,
            'weight'        => $weight,
            'customer_code' => $customerCode
        ];

        // Log::info('Sending request to SAP API', $post_array);

        try {
            $response = Http::withHeaders([
                'api_key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post($url, $post_array); // This will automatically convert the array to JSON

            if ($response->failed()) {
                Log::error('HTTP Request Failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers(),
                ]);
                return false;
            }

            $result = $response->body();

            // Log request and response
            $this->insert_sap_log_req($post_array, $result);

            $data = json_decode($result, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $json_error_msg = json_last_error_msg();
                Log::error('JSON Decode Error: ' . $json_error_msg);
                return false;
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('HTTP Request Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }


    public function insert_sap_log_req($post_array, $result)
    {
        SapRequestLog::create([
            'payload' => json_encode($post_array),
            'response' => $result
        ]);
    }


    // Free Ongkir
    public function getFreeShippingProvince($id_shop, $id_subdistrict_dest)
    {
        $district = $this->getDataDistrictById($id_subdistrict_dest);
        $id_province_shipping = $district->province_id;

        $id_province_freeshipping = FreeOngkir::where('id_shop', $id_shop)
            ->where('status', '1')
            ->pluck('id_province')
            ->toArray();

        $free_shipping = null;
        if (in_array($id_province_shipping, $id_province_freeshipping)) {
            $free_shipping = $this->_getFreeShippingProvince($id_shop);
        }

        return $free_shipping;
    }

    public function _getFreeShippingProvince($id_shop)
    {
        $query = DB::table('shipping')
            ->select('shipping.*', 'courier.code')
            ->leftJoin('courier', 'courier.id', '=', 'shipping.id_courier');

        if ($id_shop == '161') {
            $query->where('shipping.id', '99');
        } else {
            $query->where('shipping.id', '98');
        }

        $ship = $query->get();

        return $ship;
    }

    public function getDataDistrictById($id_subdistrict)
    {
        $query = DB::table('subdistrict')
            ->select('*')
            ->where('subdistrict_id', $id_subdistrict);

        return $query->first();
    }

    function update_cart_temp($id_cart)
    {
        // NOTE do update cart shop by id_cart
        $datasave_cart_shop = [];

        $cf     = Lpse_config::first();
        $ppn     = $cf->ppn / 100;
        $pph     = $cf->pph / 100;

        $get = DB::table('cart_shop_temporary')
            ->select(
                'id_shop',
                DB::raw('SUM(total) as sum_price'),
                DB::raw('SUM(IF(nominal_ppn=0, 0, total_non_ppn)) as sum_price_ppn'),
                DB::raw('SUM(total_non_ppn) as sum_price_non_ppn'),
                DB::raw('SUM(nominal_ppn) as sum_ppn'),
                DB::raw('SUM(nominal_pph) as sum_pph'),
                DB::raw('SUM(qty) as sum_qty'),
                DB::raw('SUM(total_weight) as total_weight')
            )
            ->where('id_cart', $id_cart)
            ->where('is_selected', 'Y')
            ->groupBy('id_shop')
            ->get();

        if ($get) {
            $data = $get->toArray();

            if (!empty($data)) {
                foreach ($data as $key => $val) {
                    $id_shop = $val->id_shop;

                    // Data Shop
                    $query = DB::table('cart_shop')
                        ->select('id', 'id_coupon', 'sum_price', 'sum_shipping', 'insurance_nominal', 'handling_cost', 'handling_cost_non_ppn')
                        ->where('id_cart', $id_cart)
                        ->where('id_shop', $id_shop)
                        ->first();

                    $count = $query ? 1 : 0; // Karena `first()` mengembalikan null jika tidak ada data
                    $shop  = $query;


                    $total_weight = $val->total_weight;

                    $sum_price = $val->sum_price ?? 0;
                    $sum_price_ppn = $val->sum_price_ppn ?? 0;
                    $sum_price_non_ppn = $val->sum_price_non_ppn ?? 0;


                    $sum_ppn = round($val->sum_ppn);
                    $sum_pph = round($val->sum_pph);

                    $handling_cost_exlude_ppn = $shop->handling_cost_non_ppn ?? 0;

                    if ($handling_cost_exlude_ppn > 0) {
                        $sum_ppn = round(($sum_price_ppn + $handling_cost_exlude_ppn) * $ppn);
                        $sum_pph = round(($sum_price_non_ppn + $handling_cost_exlude_ppn) * $pph);
                    } else {
                        $sum_ppn = round(($sum_price_ppn) * $ppn);
                        $sum_pph = round(($sum_price_non_ppn) * $pph);
                    }

                    // $sum_pph = round(($sum_price_non_ppn + $shop->sum_shipping + $shop->insurance_nominal + $handling_cost_exlude_ppn) * $pph);

                    $sum_qty = $val->sum_qty;

                    if ($val->total_weight == 0) {
                        $val['total_weight'] = ($sum_qty * $this->config_default_weight);
                    }

                    $id_coupon = $shop->id_coupon ?? 0;
                    $sum_shipping = $shop->sum_shipping ?? 0;
                    $insurance_nominal = $shop->insurance_nominal ?? 0;

                    $ppn_shipping = round(($sum_shipping + $insurance_nominal) * $ppn);
                    $pph_shipping = round(($sum_shipping + $insurance_nominal) * $pph);

                    $datasave_cart_shop[$id_shop] = [
                        'id_cart'   => $id_cart,
                        'id_shop'   => $id_shop,
                        'id_coupon' => $id_coupon,
                        'sum_price' => $sum_price,
                        'sum_shipping' => $sum_shipping,
                        'insurance_nominal' => $insurance_nominal,

                        // sum price product ppn only, exclude ppn
                        'sum_price_ppn' => $sum_price_ppn,
                        // sum price product ppn & non ppn, exclude ppn
                        'sum_price_non_ppn' => $sum_price_non_ppn,

                        'ppn_shipping' => $ppn_shipping,
                        'pph_shipping' => $pph_shipping,

                        'ppn_price'    => $sum_ppn,
                        'pph_price'    => $sum_pph,
                        'qty'          => $sum_qty,
                        'total_weight' => $total_weight,
                    ];
                }
            }
        }
        if (!empty($datasave_cart_shop)) {
            foreach ($datasave_cart_shop as $key => $val) {
                $id_shop = $val['id_shop'];

                $this->_updateShop2($id_cart, $id_shop, $val);
            }
        }
        $cst       = new CartShopTemporary();
        $cart_shop = new CartShop();
        $cst->updateCartShop($id_cart, $id_shop);
        $cart_shop->refreshCart($id_cart);

        return $datasave_cart_shop;
    }

    private function _updateShop2($id_cart, $id_shop, $data_shop)
    {
        // $id_cart		= $data_shop['id_cart'];
        $sum_price        = $data_shop['sum_price'];
        $voucher         = CartShopTemporary::getCartVoucherById($data_shop['id_coupon'], $sum_price);
        $cf             = Lpse_config::first();
        $ppn             = $cf->ppn / 100;
        $pph             = $cf->pph / 100;

        if ($voucher) {
            foreach ($voucher as $v) {
                $type         = $v->discount_type;
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
            DB::table('cart_shop')
                ->where('id_shop', $id_shop)
                ->where('id_cart', $id_cart)
                ->update(['id_coupon' => null]);
        } else {
            DB::table('cart_shop')
                ->where('id_shop', $id_shop)
                ->where('id_cart', $id_cart)
                ->update(['id_coupon' => $data_shop['id_coupon']]);
        }

        DB::table('cart_shop')
            ->where('id_shop', $id_shop)
            ->where('id_cart', $id_cart)
            ->update(['discount' => $discount]);

        DB::table('cart_shop')
            ->where('id_shop', $id_shop)
            ->where('id_cart', $id_cart)
            ->update($data_shop);

        DB::table('cart_shop')
            ->where('id_shop', $id_shop)
            ->where('id_cart', $id_cart)
            ->update([
                // 'ppn_shipping' => DB::raw("ROUND($ppn * (sum_shipping + insurance_nominal))"),
                // 'pph_shipping' => DB::raw("ROUND($pph * (sum_shipping + insurance_nominal))"),
                'subtotal' => DB::raw("sum_shipping + insurance_nominal + sum_price_non_ppn + handling_cost_non_ppn"),
                'total' => DB::raw("sum_price_non_ppn + ppn_price + sum_shipping + insurance_nominal + ppn_shipping + handling_cost_non_ppn - discount")
            ]);

        // Manualy update ppn & pph shipping
        // Cause on server, ppn & pph shipping not updated to rounded value
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
                    'pph_shipping' => $pph_shipping
                ]);
        }
    }

    public function migrate_checkout($data)
    {
        // Dapatkan tanggal dan waktu sekarang
        $date_now = Carbon::now()->toDateTimeString();

        $data = DB::table('cart')
            ->select('*')
            ->where($data);

        $query = $data->get();
        $count = $data->count();

        if ($count > 0) {
            $q = $query->first();
            $id_cart = $q->id;

            $migrate_cart = $this->migrate_cart($id_cart, $date_now);
            $migrate_cart_shop = $this->migrate_cart_shop($id_cart, $date_now);
            $migrate_cart_shop_detail = $this->migrate_cart_shop_detail($id_cart);

            if ($migrate_cart && $migrate_cart_shop && $migrate_cart_shop_detail) {
                $delete = $this->delete_all_temporary($id_cart);
                // $this->load->library('external/Lkpp', null, 'lkpp_lib');
                // $this->lkpp_lib->trans_report($id_cart);
                if ($delete) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    private function delete_all_temporary($id_cart)
    {
        DB::table('cart')->where('id', $id_cart)->delete();
        DB::table('cart_shop')->where('id_cart', $id_cart)->delete();
        DB::table('cart_shop_temporary')->where('id_cart', $id_cart)->delete();
        return true;
    }

    public function migrate_cart($id_cart, $date_now)
    {
        // Jalankan query
        $q = DB::table('cart')
            ->where('id', $id_cart)
            ->first();

        // Tentukan tanggal kedaluwarsa pembayaran dari batas waktu pembayaran customer
        $due_date_payment = Carbon::parse($date_now)->addDays($q->jml_top)->format('Y-m-d H:i:s');

        // Membuat array data
        $data = [
            'id' => $q->id,
            'invoice' => 'INV-' . str_replace('-', '', substr($date_now, 0, 10)) . $id_cart,
            'id_user' => $q->id_user,
            'id_address_user' => $q->id_address_user,
            'id_voucher' => $q->id_voucher,
            'handling_cost' => $q->handling_cost,
            'handling_cost_non_ppn' => $q->handling_cost_non_ppn,
            'sum_price' => $q->sum_price,
            'sum_price_ppn_only' => $q->sum_price_ppn_only,
            'sum_shipping' => $q->sum_shipping,
            'sum_discount' => $q->sum_discount,
            'coin_usage' => $q->coin_usage,
            'coin_reward' => $q->coin_reward,
            'refund_price' => 0,
            'qty' => $q->qty,
            'discount' => $q->discount,
            'id_payment' => $q->id_payment,
            'total' => $q->total,
            'payment_method' => $q->payment_method,
            'payment_req_count' => $q->payment_req_count,
            'status' => $q->status,
            'created_date' => $date_now,
            'due_date_payment' => $due_date_payment,
            'last_update' => $q->last_update,
            'total_non_ppn' => $q->total_non_ppn,
            'total_ppn' => $q->total_ppn,
            'total_pph' => $q->total_pph,
            'val_ppn' => $q->val_ppn,
            'val_pph' => $q->val_pph,
            'sum_price_non_ppn' => $q->sum_price_non_ppn,
            'sum_shipping_non_ppn' => $q->sum_shipping_non_ppn,
            'jml_top' => $q->jml_top,
            'handling_cost_fee_nominal' => $q->handling_cost_fee_nominal,
            'handling_cost_fee_percent' => $q->handling_cost_fee_percent,
            'handling_cost_fee_nominal_cut' => $q->handling_cost_fee_nominal_cut,
        ];

        // $address =$this->migrate_address($q->id_address_user, $id_cart);
        $is_migrated = $this->_migrate_cart($data);

        if ($is_migrated) {
            $complete_address = $this->migrate_address($q->id_address_user, $id_cart);
            return $is_migrated;
        } else {
            return false;
        }

        return $data;
    }

    private function _migrate_cart($data)
    {
        if (in_array($data['id_payment'], [30])) {
            $no_invoice = $data['invoice'];
            $no_invoice = explode("-", $no_invoice);
            $no_invoice = $no_invoice[1];
            $va_number = $this->generateVaNumber($no_invoice);
            $data['va_number'] = $va_number;
        }

        $insert = DB::table('complete_cart')->insert($data);

        return $insert ? true : false;
    }

    public function generateVaNumber($no_invoice)
    {
        $d_bca = DB::table('api_user')->where('user', 'BCA')->first();
        $company_code = $d_bca->company_code;
        $no_va = $company_code . $no_invoice;
        return $no_va;
    }

    public function migrate_address($id_address, $id_cart)
    {
        $ad = DB::table('member_address')
            ->select('member_address_id', 'member_id', 'address_name', 'phone', 'province_id', 'city_id', 'subdistrict_id', 'address', 'postal_code')
            ->where('member_address_id', $id_address)
            ->first();

        $mad = DB::table('member_address')
            ->select('member_address_id')
            ->where('member_id', $ad->member_id)
            ->where('is_default_billing', 'yes')
            ->first();

        $id_billing_address = $mad ? $mad->member_address_id : $id_address;

        $data_address = [
            'id_cart' => $id_cart,
            'id_address' => $id_address,
            'id_billing_address' => $id_billing_address,
            'address_name' => $ad->address_name,
            'phone' => $ad->phone,
            'province_id' => $ad->province_id,
            'city_id' => $ad->city_id,
            'subdistrict_id' => $ad->subdistrict_id,
            'address' => $ad->address,
            'postal_code' => $ad->postal_code,
        ];

        $mig_addr = $this->_migrate_address($data_address);

        // return $data_address;
    }

    private function _migrate_address($data)
    {
        DB::table('complete_cart_address')->insert($data);
        return true;
    }


    public function migrate_cart_shop($id_cart, $date_now)
    {
        $insert_cart_shop = false;

        $query = DB::table('cart_shop')
            ->select('*')
            ->where('id_cart', $id_cart)
            ->where('total', '>', 0)
            ->get();

        foreach ($query as $q) {
            $data = [
                'id_cart' => $id_cart,
                'id_shop' => $q->id_shop,
                'id_coupon' => $q->id_coupon,
                'id_address_shop' => $q->id_address_shop,
                'id_shipping' => $q->id_shipping,
                'is_insurance' => $q->is_insurance,
                'insurance_nominal' => $q->insurance_nominal,
                'base_rate' => $q->base_rate,
                'sum_price' => $q->sum_price,
                'sum_price_ppn' => $q->sum_price_ppn,
                'sum_price_non_ppn' => $q->sum_price_non_ppn,
                'ppn_price' => $q->ppn_price,
                'pph_price' => $q->pph_price,
                'sum_shipping' => $q->sum_shipping,
                'ppn_shipping' => $q->ppn_shipping,
                'pph_shipping' => $q->pph_shipping,
                'total_weight' => $q->total_weight,
                'qty' => $q->qty,
                'discount' => $q->discount,
                'subtotal' => $q->subtotal,
                'total' => $q->total,
                'note' => $q->note,
                'note_seller' => $q->note_seller,
                'file_do' => 0,
                'no_resi' => $q->no_resi,
                'last_update' => $date_now,
                'pesan_seller' => $q->pesan_seller,
                'keperluan' => $q->keperluan,
                'handling_cost_fee_nominal' => $q->handling_cost_fee_nominal,
                'handling_cost_fee_percent' => $q->handling_cost_fee_percent,
                'handling_cost' => $q->handling_cost,
                'handling_cost_non_ppn' => $q->handling_cost_non_ppn,
                'handling_cost_fee_nominal_cut' => $q->handling_cost_fee_nominal_cut,
                'ppn_total' => $q->ppn_total,
                'base_price_shipping' => $q->base_price_shipping,
                'base_price_asuransi' => $q->base_price_asuransi,
            ];
            $insert_cart_shop = $this->_migrate_cart_shop($data);
            $complete_address = $this->migrate_address_shop($id_cart, $q->id_address_shop);
        }
        if ($insert_cart_shop) {
            return true;
        } else {
            return false;
        }
        return $data;
    }

    private function _migrate_cart_shop($data)
    {
        DB::table('complete_cart_shop')->insert($data);
        return true;
    }

    public function migrate_address_shop($id_cart, $id_address_shop)
    {
        $ad_shop = DB::table('member_address')
            ->select('member_address_id', 'member_id', 'address_name', 'phone', 'province_id', 'city_id', 'subdistrict_id', 'address', 'postal_code')
            ->where('member_address_id', $id_address_shop)
            ->first();

        $data_address = [
            'id_cart' => $id_cart,
            'id_address' => $id_address_shop,
            'address_name' => $ad_shop->address_name,
            'phone' => $ad_shop->phone,
            'province_id' => $ad_shop->province_id,
            'city_id' => $ad_shop->city_id,
            'subdistrict_id' => $ad_shop->subdistrict_id,
            'address' => $ad_shop->address,
            'postal_code' => $ad_shop->postal_code,
            'is_shop_address' => '1',
        ];

        $mig_addr = $this->_migrate_address($data_address);
    }

    public function migrate_cart_shop_detail($id_cart)
    {
        $insert_cart_shop_detail = false;

        $query = DB::table('cart_shop_temporary')
            ->select('*')
            ->where('id_cart', $id_cart)
            ->where('is_selected', 'Y')
            ->get();

        foreach ($query as $q) {
            $id_nego = $q->id_nego;

            if ($id_nego != null) {
                $nego_completed = $this->negoComplete($id_nego);
            }

            $calc_fee = $q->calc_fee;
            $calc_vendor_price_fee = $q->calc_vendor_price_fee;
            $calc_vendor_price_fee_pph = $q->calc_vendor_price_fee_pph;

            $price_mp_get = $q->calc_mp_price_get;
            $price_mp_satuan = $q->calc_fee_mp_satuan_nominal;
            $price_mp_total_incl = $q->calc_fee_mp_incl_nominal;
            $price_mp_total_excl = $q->calc_fee_mp_excl_nominal;

            $data = [
                'id_cart'                    => $id_cart,
                'id_shop'                    => $q->id_shop,
                'id_product'                 => $q->id_product,
                'price'                      => $q->price,
                'qty'                        => $q->qty,
                'weight'                     => $q->weight,
                'total_weight'               => $q->total_weight,
                'total'                      => $q->total,
                'id_nego'                    => $q->id_nego,
                'input_price'                => $q->input_price,
                'harga_dasar_lpse'           => $q->harga_dasar_lpse,
                'val_ppn'                    => $q->val_ppn,
                'val_pph'                    => $q->val_pph,
                'nominal_ppn'                => $q->nominal_ppn,
                'nominal_pph'                => $q->nominal_pph,
                'fee_mp_percent'             => $q->fee_mp_percent,
                'fee_mp_nominal'             => $q->fee_mp_nominal,
                'fee_pg_nominal'             => $q->fee_pg_nominal,
                'fee_pg_percent'             => $q->fee_pg_percent,
                'total_non_ppn'              => $q->total_non_ppn,
                "calc_fee"                   => $calc_fee,
                "calc_vendor_price_fee"      => $calc_vendor_price_fee,
                "calc_vendor_price_fee_pph"  => $calc_vendor_price_fee_pph,
                "calc_mp_price_get"          => $price_mp_get,
                "calc_fee_mp_satuan_nominal" => $price_mp_satuan,
                "calc_fee_mp_incl_nominal"   => $price_mp_total_incl,
                "calc_fee_mp_excl_nominal"   => $price_mp_total_excl,
            ];
            $insert_cart_shop_detail = $this->_migrate_cart_shop_detail($data);
        }
        if ($insert_cart_shop_detail) {
            return true;
        } else {
            return false;
        }
    }

    public function negoComplete($id_nego)
    {
        DB::table('nego')
            ->where('id', $id_nego)
            ->update(['complete_checkout' => '1']);

        return true;
    }

    private function _migrate_cart_shop_detail($data)
    {
        $getLpse = Lpse_config::first();
        $getProduct = Products::getProductDetail($data['id_product']);
        $ppn = $getLpse->ppn;

        if ($getProduct->barang_kena_ppn == 0) {
            $ppn = '0';
        }

        $insertData = array_merge($data, [
            'val_ppn' => $ppn,
            'val_pph' => $getLpse->pph,
            'nama' => $getProduct->name,
            'image' => $getProduct->image,
        ]);

        DB::table('complete_cart_shop_detail')->insert($insertData);
        return true;
    }


    public function VA_BNI()
    {
        $client_id = '44867';
        $secret_key = '3c1a5a5cfd36cdce1dd1bcf9ec7cc735';
        $prefix = '988';
        $trx_id = '1230000001';
        $trx_amount = '100000';
        $billing_type = 'c';
        $virtual_account = '1111111111111111';

        // Generate a timestamp
        $timestamp = Carbon::now()->format('YmdHis');

        // Generate the signature
        $data = $client_id . $trx_id . $trx_amount . $virtual_account . $timestamp;
        $signature = hash_hmac('sha256', $data, $secret_key);

        $post_array = [
            'type' => 'createbilling',
            'client_id' => $client_id,
            'trx_id' => $trx_id,
            'trx_amount' => $trx_amount,
            'billing_type' => $billing_type,
            'virtual_account' => $virtual_account,
            'prefix' => $prefix,
            'timestamp' => $timestamp,
            'data' => $signature
        ];

        Log::info('Sending payload to BNI API', $post_array);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://apibeta.bni-ecollection.com/', $post_array);

            $result = $response->json();

            Log::info('Response from BNI API', ['response' => $response->body()]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Error sending request to BNI API', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function update_cart_after_nego($id_shop, $id_nego)
    {
        $nego           = new Nego();
        $shop           = new Shop();
        $ShopCategory   = new ShopCategory();
        $Products       = new Products();
        $ProductCategory = new ProductCategory();

        $is_cart_exist = false;

        $data_nego       = $nego->DetailNegoByid($id_nego);

        // LPSE Config
        $config         = Lpse_config::first();
        $ppn            = $config->ppn;
        $pph            = $config->pph;
        $fee_mp_percent = $config->fee_mp_percent;
        $fee_mp_nominal = $config->fee_mp_nominal;
        $fee_pg_percent = $config->fee_pg_percent;
        $fee_pg_nominal = $config->fee_pg_nominal;

        if ($data_nego) {
            // Data Nego
            $id_member = $data_nego->member_id;
            $id_product = $data_nego->id_product;
            $qty = $data_nego->qty;

            // Check PPN
            $CheckppnProduct   = $ProductCategory->check_ppn($id_product);
            $checkShop         = $shop->getShopCategory($id_shop);
            if ($checkShop) {
                $checkKategori     = $ShopCategory->getSpesialKategori($checkShop->shop_category);
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

            $getProduct     = $Products->getProductDetail($id_product);

            // Data Product
            $id_shop        = $getProduct->id_shop;
            $product_nama   = $getProduct->name;
            $product_image  = $getProduct->image;
            $product_weight = $getProduct->weight;
            $input_price    = $getProduct->price;

            // Persiapan Perhitungan
            $tot_fee_perc = $fee_mp_percent + $fee_pg_percent;
            $tot_fee_nom = $fee_mp_nominal + $fee_pg_nominal;
            $harga_dasar_lpse = 0;
            $harga_dasar_lpse_satuan = 0;

            if ($qty >= 1) {
                $harga_dasar_lpse_satuan = ($data_nego->harga_nego / $data_nego->qty);
                $harga_dasar_lpse_exc     = ($harga_dasar_lpse_satuan / (1 + ($ppn / 100)));
                $harga_dasar_lpse     = $harga_dasar_lpse_exc * $qty;
            } else {
                $harga_dasar_lpse_satuan = $data_nego->harga_nego;
                $harga_dasar_lpse     = ($data_nego->harga_nego / (1 + ($ppn / 100)));
            }

            $input_price         = $data_nego->nominal_didapat / $qty;
            $harga_tayang         = $data_nego->base_price;
            $ppn_nom            = round($harga_dasar_lpse * ($ppn / 100));
            $pph_nom            = round($harga_dasar_lpse * ($pph / 100));

            // Data
            $dataSave = [
                'id_shop' => $id_shop,
                'id_product' => $id_product,
                'nama' => $product_nama,
                'image' => $product_image,
                'weight' => $product_weight,

                'fee_mp_percent' => $fee_mp_percent,
                'fee_mp_nominal' => $fee_mp_nominal,
                'fee_pg_percent' => $fee_pg_percent,
                'fee_pg_nominal' => $fee_pg_nominal,

                'id_nego' => $data_nego->id_nego,
                'input_price' => $input_price,
                'price' => $harga_tayang,
                'qty' => $qty,
                'nominal_ppn' => $ppn_nom,
                'nominal_pph' => $pph_nom,
                'val_ppn' => $ppn,
                'val_pph' => $pph,
                'total_non_ppn' => $harga_dasar_lpse,
                'harga_dasar_lpse' => $harga_dasar_lpse_exc,
                'total' => $data_nego->harga_nego,
                'is_selected' => 'Y',
            ];


            $data_cart_detail = DB::table('cart_shop_temporary as a')
                ->select('a.id', 'a.id_cart', 'a.id_shop', 'a.id_product', 'a.qty')
                ->leftJoin('cart as b', 'a.id_cart', '=', 'b.id')
                ->where('b.id_user', $id_member)
                ->where('a.id_product', $id_product)
                ->first();

            if ($data_cart_detail) {
                $is_cart_exist = true;
            } else {
                // NOTE Data tidak ada di keranjang
                $is_cart_exist = false;
            }

            if ($is_cart_exist) {
                // NOTE Data ada di keranjang
                $id_temporary = $data_cart_detail->id;
                $id_cart = $data_cart_detail->id_cart;

                $update_temporary = DB::table('cart_shop_temporary')
                    ->where('id', $id_temporary)
                    ->update(array_merge($dataSave, [
                        'total_weight' => DB::raw('weight * qty')
                    ]));

                if ($update_temporary) {
                    // Memanggil fungsi update_cart_temp dengan parameter $id_cart
                    $this->update_cart_temp($id_cart);
                    return true;
                }
            } else {
                $id_cart = $this->getIdCartbyidmember($id_member);
                if ($id_cart) {
                    $dataSave['id_cart'] = $id_cart;
                    $dataSave['total_weight'] = DB::raw('weight * qty');
                    // Menyimpan data ke tabel cart_shop_temporary
                    $save = DB::table('cart_shop_temporary')->insert($dataSave);
                    if ($save) {
                        $savetocart = $this->update_cart_temp($id_cart);
                        return $savetocart;
                    }
                }
            }
        } else {
            return  $data_nego;
        }
    }
}
