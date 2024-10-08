<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    // Konfigurasi Midtrans
    private static $serverKey;
    private static $clientKey;
    private static $isProduction = true;
    private static $is3ds = true;
    private static $isSanitized = true;
    private static $curlOptions = array();
    private static $snapBaseUrl;

    protected $snap;

    // URL Midtrans
    const SANDBOX_BASE_URL = 'https://api.sandbox.midtrans.com/v2';
    const PRODUCTION_BASE_URL = 'https://api.midtrans.com/v2';
    const SNAP_SANDBOX_BASE_URL = 'https://app.sandbox.midtrans.com/snap/v1';
    const SNAP_PRODUCTION_BASE_URL = 'https://app.midtrans.com/snap/v1';

    public function __construct()
    {
        $this->snap['payment'] = config('midtrans.url_payment');
        self::$serverKey = config('midtrans.server_key');
        self::$clientKey = config('midtrans.client_key'); // Tambahkan jika client_key juga diambil dari config
        self::$is3ds = config('midtrans.is3ds', true);
        self::$isSanitized = config('midtrans.isSanitized', true);
        self::$snapBaseUrl = config('midtrans.snap_url');
    }

    // Get base URL sesuai mode (sandbox atau production)
    public static function getBaseUrl()
    {
        return self::$isProduction ? self::PRODUCTION_BASE_URL : self::SANDBOX_BASE_URL;
    }

    // Get Snap URL sesuai mode
    public static function getSnapBaseUrl()
    {
        return self::$isProduction ? self::SNAP_PRODUCTION_BASE_URL : self::SNAP_SANDBOX_BASE_URL;
    }

    /**
     * Create Snap payment page
     *
     * @param  array $params Payment options
     * @return string Snap token.
     * @throws Exception curl error or midtrans error
     */
    public function getSnapToken($params)
    {
        return $this->createTransaction($params);
    }

    /**
     * Create Snap URL payment
     *
     * @param  array $params Payment options
     * @return string Snap redirect url.
     * @throws Exception curl error or midtrans error
     */
    public function getSnapUrl($params)
    {
        return $this->createTransaction($params)->redirect_url;
    }

    /**
     * Create Snap payment page, with this version returning full API response
     *
     * @param  array $params Payment options
     * @return object Snap response (token and redirect_url).
     * @throws Exception curl error or midtrans error
     */
    protected function createTransaction($params)
    {
        $payloads = [
            'credit_card' => [
                'secure' => self::$is3ds
            ]
        ];

        // Calculate gross_amount from item_details if available
        if (isset($params['item_details'])) {
            $gross_amount = 0;
            foreach ($params['item_details'] as $item) {
                $gross_amount += $item['quantity'] * $item['price'];
            }
            $params['transaction_details']['gross_amount'] = $gross_amount;
        }

        // Optionally sanitize the request data
        if (self::$isSanitized) {
            $params = $this->sanitizeData($params);
        }

        // Merge payloads with request params
        $params = array_replace_recursive($payloads, $params);

        // Send the request to Midtrans Snap API
        $response = Http::withBasicAuth(self::$serverKey, '')
            ->post( $this->snap['payment']. '/snap/v1/transactions', $params);

            Log::info('url',[ $this->snap['payment'] . '/snap/v1/transactions']);

        // Return the response or throw an error if the request fails
        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception('Gagal melakukan permintaan: ' . $response->body());
    }

    /**
     * Sanitize the data before sending to Midtrans API.
     *
     * @param array $params
     * @return array
     */
    protected function sanitizeData($params)
    {
        // Implement your own sanitization logic if needed.
        return $params;
    }
    /**
     * Retrieve transaction status
     *
     * @param string $id Order ID or transaction ID
     *
     * @return mixed[]
     * @throws Exception
     */
    public function status($id)
    {
        return self::get(
            self::$snapBaseUrl . '/v2/' . $id . '/status',
            self::$serverKey,
            false
        );
    }

    /**
     * Send GET request
     *
     * @param string $url
     * @param string $server_key
     * @param mixed[] $data_hash
     * @return mixed
     * @throws Exception
     */
    public static function get($url, $server_key, $data_hash)
    {
        return self::remoteCall($url, $server_key, $data_hash, 'GET');
    }

    /**
     * Actually send request to API server
     *
     * @param string $url
     * @param string $server_key
     * @param mixed[] $data_hash
     * @param string $method
     * @return mixed
     * @throws Exception
     */
    public static function remoteCall($url, $data_hash = null, $post = true)
    {
        $ch = curl_init();
        Log::info('Response Midtrans Url', [$url]);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Set default curl options
        $curl_options = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic ' . base64_encode(self::$serverKey . ':')
            ),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CAINFO => dirname(__FILE__) . "/../data/cacert.pem",
        );

        // Menggabungkan dengan curlOptions tambahan jika ada
        if (!empty(self::$curlOptions)) {
            if (isset(self::$curlOptions[CURLOPT_HTTPHEADER])) {
                $mergedHeaders = array_merge($curl_options[CURLOPT_HTTPHEADER], self::$curlOptions[CURLOPT_HTTPHEADER]);
                $curl_options[CURLOPT_HTTPHEADER] = $mergedHeaders;
            }
            $curl_options = array_replace_recursive($curl_options, self::$curlOptions);
        }

        // Jika request POST
        if ($post) {
            $curl_options[CURLOPT_POST] = 1;
            $curl_options[CURLOPT_POSTFIELDS] = $data_hash ? json_encode($data_hash) : '';
        }

        curl_setopt_array($ch, $curl_options);
        $result = curl_exec($ch);
        Log::info('Response Midtrans', [$result]);

        if ($result === false) {
            $errorMessage = 'CURL Error: ' . curl_error($ch);
            error_log($errorMessage);
            throw new Exception($errorMessage, curl_errno($ch));
        } else {
            error_log('CURL Result: ' . $result); // Log hasil asli dari CURL
        }

        $result_array = json_decode($result);
        curl_close($ch);
        Log::info('Response Midtrans', [$result_array]);

        // Cek status code dari API response
        if (isset($result_array->status_code) && !in_array($result_array->status_code, array(200, 201, 202, 407))) {
            $message = 'Midtrans Error (' . $result_array->status_code . '): ' . $result_array->status_message;

            if (isset($result_array->validation_messages)) {
                $message .= '. Validation Messages: ' . implode(", ", $result_array->validation_messages);
            }
            if (isset($result_array->error_messages)) {
                $message .= '. Error Messages: ' . implode(", ", $result_array->error_messages);
            }

            throw new Exception($message, $result_array->status_code);
        }

        return $result_array;
    }
}
