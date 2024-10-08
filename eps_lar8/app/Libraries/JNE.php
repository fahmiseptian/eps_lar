<?php

namespace App\Libraries;

use App\Models\JneLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JNE
{
    protected $Config;

    public function __construct()
    {
        $this->Config['ENVIRONMENT'] = env('ENVIRONMENT') ? env('ENVIRONMENT') : 'development';

        if ($this->Config['ENVIRONMENT'] == 'production') {
            $this->Config['username'] = 'ELITEPROXY';
            $this->Config['api_key'] = 'a2166df97bc330831f0c4e2cf4fb60b4';
            $this->Config['OLSHOP_CUST'] = '11953800';
            $this->Config['endpoint'] = 'https://apiv2.jne.co.id:10205/';
        } else {
            $this->Config['username'] = 'TESTAPI';
            $this->Config['api_key'] = '25c898a9faea1a100859ecd9ef674548';
            $this->Config['OLSHOP_CUST'] = 'TESTAKUN';
            $this->Config['endpoint'] = 'http://apiv2.jne.co.id:10102/';
        }
    }

    public function generateCnote($data)
    {
        $url      = $this->Config['endpoint'].'/tracing/api/generatecnote';

        $postData = array_merge([
            'username' => $this->Config['username'],
            'api_key' => $this->Config['api_key'],
            'OLSHOP_BRANCH' => 'CGK000',
            'OLSHOP_CUST' => $this->Config['OLSHOP_CUST'],
        ], $data);

        $response = Http::asForm()
            ->withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
            ->post($url, $postData);

        Log::info('Send to JNE', ['payload' => $postData]);
        Log::info('Response from JNE API', ['response' => $response->body()]);
        Log::info('Parsed response from JNE API', ['response' => $response->json()]);

        if ($response->successful()) {
            // Handle successful response
            JneLog::create([
                'payload' => json_encode($postData),
                'response' => json_encode($response->json()),
                'action' => 'Create_RESI'
            ]);
            $cnote_no = $response->json()['detail'][0]['cnote_no'];

            if ( $this->Config['ENVIRONMENT'] == 'development') {
                $cnote_no = 5403212200022724 ;
            }
            
            return $cnote_no;
        } else {
            // Handle error response
            JneLog::create([
                'payload' => json_encode($postData),
                'response' => $response->json(),
                'action' => 'Create_RESI'
            ]);
            // Log the error or display it for debugging
            $errorMessage = isset($result['message']) ? $result['message'] : 'Failed to generate cnote';
            return false;
        }
    }

    function tracking($awb)
    {
        $url        = $this->Config['endpoint'].'/tracing/api/list/v1/cnote';

        if ($this->Config['ENVIRONMENT'] == 'production') {
            $resi   = $awb;
        } else {
            // AWB Pengetesan
            $resi   = '5403212200022724';
        }


        $url    = $url . '/' . $resi;
        $data   = [
            'username' => $this->Config['username'],
            'api_key' => $this->Config['api_key'],
        ];

        $response = Http::asForm()
            ->withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
            ->post($url, $data);

        Log::info('Send to JNE', ['Url' => $url]);
        Log::info('Response from JNE API', ['response' => $response->body()]);

        JneLog::create([
            'payload' => json_encode($url),
            'response' => json_encode($response->json()),
            'action' => 'Tracking Resi'
        ]);

        $result = $response->json();

        return $result;
    }
}
