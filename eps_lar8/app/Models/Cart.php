<?php

namespace App\Models;

use App\Libraries\nusoap_client;
use CurlHandle;
use Illuminate\Database\Eloquent\Model;
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

    public function getIdCartbyidmember($id_user) {
        $data = self::select('id')
            ->where('complete_checkout', 'N')
            ->where('id_user', $id_user)
            ->first();

        if (!empty($data->id)) {
            return $data->id;
        } else {
            $cart = Cart::create([
                'id_user' => $id_user
            ]);
            return $cart->id;
        }
    }

    function getCart($id_user){
        return self::select('*')
        ->where('complete_checkout', 'N')
        ->where('id_user', $id_user)
        ->first();

    }
    
    function getaddressCart($id_address_user){
        $data = DB::table('member_address as ma')
        ->select(
            'ma.member_address_id',
            'ma.phone',
            'ma.address_name',
            'ma.address',
            'ma.postal_code',
            'p.province_name',
            's.subdistrict_name',
            'c.city_name as city',
        )
        ->where('ma.member_address_id',$id_address_user)
        ->join('province as p', 'p.province_id','ma.province_id')
        ->join('city as c', 'ma.city_id','c.city_id')
        ->join('subdistrict as s', 's.subdistrict_id','ma.subdistrict_id')
        ->first();
        return $data;
    }

    function getCartDetails($id_cart, $id_shop) {
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

    function getRates($id_cart, $id_shop) {
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
    
        return $ship_result;
    }
    
    
    
    
        
    public function cekShipping($id_courier, $id_city_origin, $id_subdistrict_dest, $limit = null, $id_shop = null) {
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

    public function getShipping($id_courier, $zip_origin, $zip_destination, $id_shop) {
        $ship = DB::table('shipping')
            ->select('shipping.*', 'courier.code')
            ->leftJoin('courier', 'courier.id', '=', 'shipping.id_courier')
            ->where('id_courier', $id_courier)
            ->where('zip_origin', $zip_origin)
            ->where('zip_destination', $zip_destination)
            ->get();
    
        return $ship;
    }

    public function insertRates($data_rates) {
        DB::table('shipping')->insert($data_rates);
    }


    // RPX
    public function rpx_get_rates($zip_origin, $zip_destination, $weight, $courier, $courier_id, $id_city_origin, $id_subdistrict_dest, $service) {
        $service_array = explode(",", $service);
        $response = $this->_rpx_get_rates($zip_origin, $zip_destination, $weight);

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

    public function _rpx_get_rates($zip_origin, $zip_destination, $weight) {
        $wsdl = "http://api.rpxholding.com/wsdl/rpxwsdl.php?wsdl";
        $client = new nusoap_client($wsdl, true);
    
        if ($client->getError()) {
            return false; // Gagal membuat objek client, kembalikan false
        }
    
        $post_array = [
            'user'                      => 'demo',
            'password'                  => 'demo',
            'account_number'            => '234098705',
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
    

    public function insert_rpx_log_req($post_array, $result) {
        DB::table('rpx_log')->insert([
            'payload' => json_encode($post_array),
            'response' => $result,
            'action' => 'GET_RATES'
        ]);
    }

    

    // JNE
    public function jne_get_rates($zip_origin, $zip_destination, $weight, $courier, $courier_id, $id_city_origin, $id_subdistrict_dest, $sap_origin, $sap_destination, $service, $jne_destination, $jne_origin) {
        $service_array = explode(",", $service);
        $response = $this->_jne_get_rates($jne_origin, $jne_destination, $weight);
        Log::info('Raw API Response:', $response);
        if ($response) {
            foreach ($response['price'] as $price) {
                if (!empty($price['price']) && in_array($price['service_display'], $service_array)) {
                    // Konversi harga menjadi dalam satuan yang benar
                    $convertedPrice = $price['price'] / 1000;
        
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
    
    public function _jne_get_rates($jne_origin, $jne_destination, $weight) {
        $post_array = [
            'username' => 'TESTAPI',
            'api_key' => '25c898a9faea1a100859ecd9ef674548',
            'from' => $jne_origin,
            'thru' => $jne_destination,
            'weight' =>  $weight,
        ];
    
        // Log::info('Sending payload to JNE API', $post_array);
    
        $response = Http::asForm()
            ->withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
            ->post('http://apiv2.jne.co.id:10102/tracing/api/pricedev', $post_array);
    
        $result = $response->json();
    
        // Log::info('Response from JNE API', ['response' => $response->body()]);
        // Log::info('Parsed response from JNE API', $result);
    
        $this->insert_jne_log_req($post_array, $result);
        return $result;
    }
    
    public function insert_jne_log_req($post_array, $result) {
        JneLog::create([
            'payload' => json_encode($post_array),
            'response' => json_encode($result),
            'action' => 'CEK_TARIF'
        ]);
    }


    // SAP
    public function sap_get_rates($zip_origin, $zip_destination, $weight, $courier, $courier_id, $id_city_origin, $id_subdistrict_dest, $sap_origin, $sap_destination, $service) {
        $service_array = explode(",", $service);
        $response = $this->_sap_get_rates($sap_origin, $sap_destination,$weight);
        if ($response) {
            foreach ($response['price_detail'] as $costs) {
                if ($costs['minimum_kilo'] == '1') {
                    if (in_array($costs['service_type_code'], $service_array)) {
                        $data_rates = array(
                            'id_courier' 			=> $courier_id,
                            'id_city_origin' 		=> $id_city_origin,
                            'id_subdistrict_dest' 	=> $id_subdistrict_dest,
                            'price' 				=> $costs['price'],
                            'etd' 					=> round($costs['sla']),
                            'deskripsi' 			=> 'SAP ' . ucwords(strtolower($costs['service_type_name'])),
                            'service' 				=> $costs['service_type_code'],
                            'zip_destination'		=> $zip_destination,
                            'zip_origin'			=> $zip_origin,
                        );
                        $insert = $this->insertRates($data_rates);
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

    public function _getFreeShippingProvince($id_shop) {
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

    public function getDataDistrictById($id_subdistrict) {
        $query = DB::table('subdistrict')
        ->select('*')
        ->where('subdistrict_id', $id_subdistrict);

        return $query->first();
	}

}
