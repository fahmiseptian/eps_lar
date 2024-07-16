<?php

namespace App\Libraries;

use App\Models\JneLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RPX{
    protected $Config;

    public function __construct()
    {
        $this->Config['ENVIRONMENT'] = 'development';
        // $this->Config['ENVIRONMENT'] = 'production';

        $this->Config['url']        = 'http://api.rpxholding.com/wsdl/rpxwsdl.php?wsdl';
        $this->Config['order_type'] = 'MP';


        if ($this->Config['ENVIRONMENT'] == 'production') {
            $this->Config['user'] = 'eliteproxy';
            $this->Config['password'] = '3liTePr0XyS15TEm';
            $this->Config['account_number'] = '758078721';

        } else {
            $this->Config['user'] = 'demo';
            $this->Config['password'] = 'demo';
            $this->Config['account_number'] = '234098705';
        }

    }

    function send_shipmentData($data) {
        $client = new nusoap_client($this->Config['url'], true);

        if ($client->getError()) {
            return false;
        }

        $post_array = array_merge([
            'user'                      =>  $this->Config['user'],
            'password'                  =>  $this->Config['password'],
            'shipper_account'           =>  $this->Config['account_number'],
            'order_type'                =>  $this->Config['order_type'],
            'format'                    => 'json'
        ],$data);

        $result = $client->call('sendShipmentData', $post_array);

        Log::info('Send to RPX', [ $post_array ]);
        Log::info('Response from RPX API', ['response' => $result]);

        DB::table('rpx_log')->insert([
            'payload' => json_encode($post_array),
            'response' => $result,
            'action' => 'sendShipmentData'
        ]);

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

    function send_pickupRequest($data) {
        $client = new nusoap_client($this->Config['url'], true);

        if ($client->getError()) {
            return false;
        }

        $tz = 'Asia/Jakarta';
        $datetime_now = Carbon::now($tz)->format('Y-m-d H:i');
        $date_pickup = Carbon::parse($datetime_now, $tz)->addDay()->format('Y-m-d H:i:s');

        $post_array = array_merge([
            'user'                      =>  $this->Config['user'],
            'password'                  =>  $this->Config['password'],
            'shipper_account'           =>  $this->Config['account_number'],
            'format'                    => 'json',
            'order_type'                => $this->Config['order_type'],
            'pickup_account_number'     => $this->Config['account_number'],
            'pickup_ready_time'         => $date_pickup,
            'total_package'             => 1
        ], $data);

        $result = $client->call('sendPickupRequest', $post_array);

        Log::info('Send to RPX', [ $post_array ]);
        Log::info('Response from RPX API', ['response' => $result]);

        DB::table('rpx_log')->insert([
            'payload' => json_encode($post_array),
            'response' => $result,
            'action' => 'sendpickupRequest'
        ]);

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

    function tracking($awb) {
        $client = new nusoap_client($this->Config['url'], true);

        if ($client->getError()) {
            return false;
        }

        $post_array = [
            'user'                      =>  $this->Config['user'],
            'password'                  =>  $this->Config['password'],
            'awb'                       =>  $awb,
            'format'                    => 'json',
        ];

        $result = $client->call('getTrackingAWB', $post_array);

        Log::info('Send to RPX', [ $post_array ]);
        Log::info('Response from RPX API', ['response' => $result]);

        DB::table('rpx_log')->insert([
            'payload' => json_encode($post_array),
            'response' => $result,
            'action' => 'getTrackingAWB'
        ]);

        return $result;
    }
}
