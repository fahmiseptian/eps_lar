<?php

namespace App\Libraries;

use App\Models\Invoice;
use App\Models\JneLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SAP
{
    protected $Config;

    public function __construct()
    {
        $this->Config['ENVIRONMENT'] = 'development';
        // $this->Config['ENVIRONMENT'] = 'production';

        if ($this->Config['ENVIRONMENT'] == 'production') {
            $this->Config['url']        = 'https://api.coresyssap.com/';
            // $this->Config['user'] = 'eliteproxy';
            $this->Config['api_key'] = '';
            $this->Config['customer_code'] = '';
        } else {
            $this->Config['url']        = 'http://apisanbox.coresyssap.com/';
            // $this->Config['user'] = 'demo';
            $this->Config['api_key'] = 'DEV_m4rK3tPlac3#_2019';
            $this->Config['customer_code'] = 'DEV000';
        }
    }


    // Create for reference_no for SAP
    public function numHash($str, $len = null)
    {
        // Reff : https://stackoverflow.com/q/3379471/10351006

        $binhash = md5($str, true);
        $numhash = unpack('N2', $binhash);
        $hash = $numhash[1] . $numhash[2];

        if (!empty($len)) {
            $len = intval($len);
        }

        if ($len && is_int($len)) {
            $lenght = $len;
            $lenght--;
            $hash = substr($hash, 0, $lenght);
        }
        return $hash;
    }

    function pickup_order($post_array)
    {
        $url    = $this->Config['url'] . 'shipment/pickup/single_push';
        $header = [
            'api_key' => $this->Config['api_key'],
            'Content-Type' => 'application/json',
        ];

        $post_array['customer_code'] = $this->Config['customer_code'];
        $post_array['volumetric'] = $post_array['goods_volume'] ?? "1x1x1" ?: "1x1x1";
        $post_array['description_item'] = $post_array['goods_desc'] ?? "Elektronik" ?: "Elektronik";
        $post_array['special_instruction'] = $post_array['special_ins'] ?? "Fragile" ?:  "Fragile";
        $post_array['shipment_type_code'] = 'SHTPC';

        if ($post_array['insurance_flag'] == 2) {
            // NOTE admin saat ini : 2000
            $insurance_admin_cost = 2000;
            $post_array['insurance_type_code'] = "INS01";

            // NOTE admin asuransi tidak di includekan ke nominal asuransi
            // NOTE karena otomatis di tambahkan oleh pihak SAP
            $insurance_cost = $post_array['insurance_value'];
            $insurance_cost = ($insurance_cost - $insurance_admin_cost);

            $post_array['insurance_value'] = $insurance_cost;
        } else {
            if (isset($post_array['insurance_value'])) {
                unset($post_array['insurance_value']);
            }
        }

        $post_array['cod_flag'] = "1";

        $result = Http::withHeaders($header)->post($url, $post_array);

        $data_log = [
            'payload'     => json_encode($post_array),
            'response'     => $result,
            'no_resi'        => $result['data']['awb_no'] ?? null ?: null,
            'label'        => $result['data']['label'] ?? null ?: null,
            'action'        => 'REQUEST PICKUP'
        ];

        DB::table('api_courier_sap_log')->insert($data_log);
        DB::table('sap_pickup_log')->insert([
            'invoice'   => $post_array['reference_no'],
            'payload'   => json_encode($post_array), // Mengubah payload menjadi JSON
            'response'  =>  $result,     // Mengubah response menjadi JSON
        ]);

        if ($result['status'] == 'success') {
            $resi = $result['data']['awb_no'];
            $reference_no = $post_array['reference_no'];

            $r = [
                'status' => $result['status'],
                'no_resi' => $resi,
                'unique_order_code' => $reference_no,
            ];

            return $r;
        } else {
            return $result;
        }
    }

    function tracking($awb)
    {

        if ($this->Config['ENVIRONMENT'] == 'production') {
            $url    = 'https://track.coresyssap.com/shipment/tracking/awb?awb_no=' . $awb;
        } else {
            $url    = $this->Config['url'] . 'shipment/tracking/awb?awb_no=' . $awb;
        }

        $headers = [
            'api_key' => $this->Config['api_key'],
            'Content-Type' => 'application/json',
        ];

        $response = Http::withHeaders($headers)->get($url);

        DB::table('sap_request_log')->insert([
            'payload'   =>  $url,
            'response'  => $response
        ]);

        $result = $response->json();
        return $result;
    }
}
