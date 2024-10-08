<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Libraries\Bca;
use App\Services\JWTService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class BcaController extends Controller
{
    protected $data;
    protected $libraries;

    public function __construct()
    {
        // Load BCA Library
        $this->libraries['BCA'] = new Bca();

        // Development
        $this->data['endpoint_origin']   = 'https://alphagc.proyek.web.id/eliteproxy.co.id/';
        $this->data['endpoint_bca_get_token']   = 'https://devapi.klikbca.com/openapi/v1.0/access-token/b2b';
        $this->data['endpoint_bca_va_status']   = 'https://devapi.klikbca.com/openapi/v1.0/transfer-va/status';

        // Production
        // $this->data['endpoint_origin']   = 'https://eliteproxy.co.id/';
        // $this->data['endpoint_bca_get_token']   = 'https://api.klikbca.com/openapi/v1.0/access-token/b2b';
        // $this->data['endpoint_bca_va_status']   = 'https://api.klikbca.com/openapi/v1.0/transfer-va/status';

        $this->data['path_eps_inquiry']         = '/openapi/v1.0/transfer-va/inquiry';
        $this->data['path_eps_payment']         = '/openapi/v1.0/transfer-va/payment';
        $this->data['path_eps_va_status']       = '/openapi/v1.0/transfer-va/status';

        error_reporting(0);
        date_default_timezone_set('Asia/Jakarta');
        header('Content-Type: application/json; charset=utf-8');
    }

    function payment(Request $request)
    {
        $id_cart        = $request->input('id_cart');

        $head               = $request->headers->all();          // Mengambil semua header dari request
        $post_value         = $request->getContent();            // Mengambil raw input stream dari body request        
        $payload            = json_decode($post_value, true);

        if (isset($head['Authorization'])) {
            $aut = explode(" ", $head['Authorization']);

            if (count($aut) === 2) {
                $token = $aut[1];
                $signature = $head['X-Signature'] ?? null;
                $external_id = $head['X-External-Id'] ?? null;
                $token_check = $this->libraries['BCA']->checkToken($token);
            } else {
                return response()->json(['error' => 'Invalid Authorization format'], 401);
            }
        } else {
            $dataInvoice = $this->libraries['BCA']->getDataInvoice($id_cart);
            $invoice = $dataInvoice->invoice;
            $user_bca       = $this->libraries['BCA']->getDataUser(['user' => 'BCA']);
            $data = $this->getTokenEps();

            // set payload
            $payload = [
                'virtualAccountNo'      => $user_bca->company_code . str_replace("INV-", "", $invoice),
                'virtualAccountName'    => $dataInvoice->nama,
                'partnerServiceId'      => $user_bca->company_code,
                'customerNo'            =>  str_replace("INV-", "", $invoice),
                'paymentRequestId'      =>  str_replace("INV-", "", $invoice) . '30031234500001136964',
                'channelCode'           =>  6011,
                'flagAdvise'            =>  'N',
                'paidAmount'            =>  [
                    "value" => $dataInvoice->total . '.00',
                    "currency" => "IDR"
                ],
            ];

            $post_value     = json_encode($payload);
            $current_date   = date('Y-m-d H:i:s');
            $timestamp      = str_replace(" ", "T", $current_date) . '+07:00';

            // AUTHORIZATION
            $token                  = $data['token'];
            $signature              = $data['signature'];
            $external_id            = $data['external_id'];
            $head['Authorization']  = 'Bearer ' . $token;
            $token_check            = $this->libraries['BCA']->checkToken($token);
        }
        // Hit BCA
        $r              = $this->_payment($token, $signature, $external_id, $head, $payload, $post_value);

        $response = $r['original']['response'];
        $data_req       = array(
            'token'         => $token,
            'header'        => json_encode($head),
            'payload'       => json_encode($payload),
            'response'      => json_encode($r['response']),
            'request_id'    => $payload['paymentRequestId'],
            'action'        => 'payment',
            'code'          => $r['code'],
            'total'         => $r['total'],
            'id_cart'       => $r['id_cart'],
            'va_number'     => $r['va_number'],
            'external_id'   => $external_id,
            'debug'         => $r['debug'],
        );
        $insert             =  $this->libraries['BCA']->insertBcaReq($data_req);

        if ($r['code'] == 2002500) {
            DB::table('complete_cart')->where('id', $id_cart)->update(['va_number' => $r['va_number']]);
        }
        // save VA Number
        return response()->json($r);
    }

    private function _payment($token, $signature, $external_id, $head, $payload, $post_value)
    {
        $invoice            = $payload['customerNo'];
        $data_auth          = ['user' => 'BCA'];
        $data_external_id   = ['external_id' => $external_id];
        $valid_external_id  = $this->libraries['BCA']->getCountExternalId($data_external_id);
        $user               = $this->libraries['BCA']->getDataUser($data_auth);
        $bill               = $this->libraries['BCA']->getAllBill($invoice);
        $va_system          = $user->company_code . str_replace("INV-", "", $bill->invoice);
        $c_total            = $bill->total;
        $c_id               = $bill->id;

        $token_check        = $this->libraries['BCA']->checkToken($token);
        $flag               = '01';
        $check_json_payload = $this->json_validator($post_value);
        $timestamp          = $head['X-Timestamp'];

        //====================== UNAUTHORIZED SIGNATURE ====================//
        $pv             = json_decode($post_value, true);
        $bodyrequest    = json_decode(json_encode($pv), true);
        $method         = 'POST';
        $url            = $this->data['path_eps_payment'];
        $signature_valid = $this->validateServiceSignature($user->client_secret, $method, $url, $token, $timestamp, $bodyrequest, $signature);
        $gt             = str_replace(".00", "", $payload['paidAmount']['value']);

        $signature_sym = $head['X-Signature'];

        if ($token_check < 1) {
            // VALIDASI TOKEN EPS //
            $code       = '4012501';
            $flag       = "-";
            $reason_id  = "Token tidak valid";
            $reason_en  = "Access Token Invalid";
        } elseif (!$signature_valid) {
            $code       = '4012500';
            $reason_id  = "Kesalahan pada Signature";
            $reason_en  = "Unauthorized [Signature]";
            //======================END UNAUTHORIZED SIGNATURE ==================//
        } elseif (!$check_json_payload) {
            // VALIDASI JSON FORMAT //
            $code       = '4002400';
            $reason_id  = "Parsing gagal";
            $reason_en  = "Request Parsing Error";
        } elseif (!$valid_external_id) {
            // VALIDASI KONFLIK EXTERNAL_ID //
            $code       = '4092401';
            $reason_id  = "Konflik";
            $reason_en  = "Conflict";
        } elseif (!$user) {
            // VALIDASI API USER //
            $code       = '4012401';
            $flag       = "-";
            $reason_id  = "User tidak dikenali";
            $reason_en  = "Unauthorized [Unknown client]";
        } elseif ($payload['partnerServiceId'] == '' || $payload['partnerServiceId'] == null) {
            $code       = '4002502';
            $reason_id  = "partnerServiceId kosong";
            $reason_en  = "Missing Mandatory Field partnerServiceId";
        } elseif ($payload['partnerServiceId'] != '' && $payload['partnerServiceId'] != $user->company_code) {
            // VALIDASI FIELD partnerServiceId //
            $code       = '4002501';
            $reason_id  = "Format partnerServiceId tidak sesuai";
            $reason_en  = "Invalid Field format partnerServiceId";
        } elseif ($payload['virtualAccountNo'] != '' && is_numeric((str_replace("   ", "", $payload['virtualAccountNo']))) == FALSE) {
            $code       = '4002501';
            $reason_id  = "Format virtualAccountNo tidak sesuai";
            $reason_en  = "Invalid Field format virtualAccountNo";
        } elseif ($payload['customerNo'] != '' && is_numeric($payload['customerNo']) == FALSE) {
            $code       = '4002501';
            $reason_id  = "Format customerNo tidak sesuai";
            $reason_en  = "Invalid Field format customerNo";
        } elseif ($payload['customerNo'] == '' || $payload['customerNo'] == null) {
            $code       = '4002502';
            $reason_id  = "customerNo kosong";
            $reason_en  = "Missing Mandatory Field customerNo";
        } elseif ($payload['virtualAccountNo'] == '' || $payload['virtualAccountNo'] == null) {
            $code       = '4002502';
            $reason_id  = "virtualAccountNo kosong";
            $reason_en  = "Missing Mandatory Field virtualAccountNo";
        } elseif ($payload['virtualAccountName'] == '' || $payload['virtualAccountName'] == null) {
            $code       = '4002502';
            $reason_id  = "virtualAccountName kosong";
            $reason_en  = "Missing Mandatory Field virtualAccountName";
        } elseif ($payload['paymentRequestId'] == '' || $payload['paymentRequestId'] == null) {
            $code       = '4002502';
            $reason_id  = "paymentRequestId kosong";
            $reason_en  = "Missing Mandatory Field paymentRequestId";
        } elseif ($payload['channelCode'] == '' || $payload['channelCode'] == null) {
            $code       = '4002502';
            $reason_id  = "channelCode kosong";
            $reason_en  = "Missing Mandatory Field channelCode";
        } elseif ($payload['paidAmount'] == '' || $payload['paidAmount'] == null) {
            $code       = '4002502';
            $reason_id  = "paidAmount kosong";
            $reason_en  = "Missing Mandatory Field paidAmount";
        } elseif ($payload['flagAdvise'] == '' || $payload['flagAdvise'] == null) {
            $code       = '4002502';
            $reason_id  = "flagAdvise kosong";
            $reason_en  = "Missing Mandatory Field flagAdvise";
        } elseif (!in_array($payload['flagAdvise'], array("Y", "N"))) {
            $code       = '4002501';
            $reason_id  = "flagAdvise tidak sesuai format";
            $reason_en  = "Invalid Field Format flagAdvise";
        } elseif ($bill && $bill->total != $gt) {
            $code       = '4042513';
            $reason_id  = "Amount tidak sesuai " . $bill->total . '!= ' . $gt;
            $reason_en  = "Invalid Amount";
        } elseif ($bill && $bill->status_pembayaran_top == '0') {
            $code       = '2002500';
            $flag       = "00";
            $reason_id  = "Sukses";
            $reason_en  = "Success";
            $c_total    = $bill->total;
            $c_id       = $bill->id;
        } elseif ($bill && $bill->status_pembayaran_top == '1') {
            $code       = '4042514';
            $reason_id  = "transaksi ini sudah lunas";
            $reason_en  = "Bill has been paid";
            $c_total    = $bill->total;
            $c_id       = $bill->id;
        } elseif (!$bill) {
            $code       = '4042512';
            $reason_id  = "transaksi tidak ditemukan";
            $reason_en  = "Bill not found";
        }

        $billDetails      = array();
        $freeTexts[]      = array(
            "english" => "",
            "indonesia" => ""
        );

        if (in_array($code, array("2002500", "4042514", "4042512"))) {
            $response = array(
                "responseCode" => $code,
                "responseMessage" => $reason_en,
                "virtualAccountData" => array(
                    "paymentFlagReason" => array(
                        "english" => $reason_en,
                        "indonesia" => $reason_id
                    ),
                    "partnerServiceId" => $payload['partnerServiceId'],
                    "customerNo" => $payload['customerNo'],
                    "virtualAccountNo" => $payload['virtualAccountNo'],
                    "virtualAccountName" => $payload['virtualAccountName'],
                    "virtualAccountEmail" => "",
                    "virtualAccountPhone" => "",
                    "trxId" => $payload['trxId'],
                    "paymentRequestId" => $payload['paymentRequestId'],
                    "paidAmount" => array(
                        "value" => $c_total . '.00',
                        "currency" => "IDR"
                    ),
                    "paidBills" => "",
                    "totalAmount" => array(
                        "value" =>  $c_total . '.00',
                        "currency" => "IDR"
                    ),
                    "trxDateTime" => $head['X-Timestamp'],
                    "referenceNo" => $payload['referenceNo'],
                    "journalNum" => "",
                    "paymentType" => "",
                    "flagAdvise" => $payload['flagAdvise'],
                    "paymentFlagStatus" => $flag,
                    "billDetails" => $billDetails,
                    "freeTexts" => $freeTexts,
                ),
                "additionalInfo" => new stdClass(),
            );
        } else {
            $response = array(
                "responseCode" => $code,
                "responseMessage" => $reason_en,
            );
        }
        return [
            'response'  => $response,
            'debug'     => "", 
            'code'      => $code, 
            'total'     => $c_total, 
            'id_cart'   => $c_id, 
            'va_number' => $va_system
        ];
    }

    public function getTokenEps()
    {
        $now = now();
        $exp = $now->addDay();
        $data_log = [
            'header' => '',
            'body' => '',
            'expired_dt_token' => $exp,
        ];
        $id_log =  $this->libraries['BCA']->insertBcaPayload($data_log);

        $data_token = [
            'nama' => 'BCA',
            'sess_start' => $now,
            'sess_expired' => $exp
        ];
        $token_eps = JWTService::generateToken($data_token);

        // UPDATE LOG_PAYLOAD (HEADER, BODY, TOKEN LPSE)
        $code = '200';
        $data_update = [
            'token_eps' => $token_eps,
            'response' => $code,
            'category' => 'req_token'
        ];
        $update_log =  $this->libraries['BCA']->updateBcaPayload($data_update, $id_log);

        $current_date = now();
        $timestamp = $current_date->format('Y-m-d\TH:i:sP');
        $user_bca = $this->libraries['BCA']->getDataUser(['user' => 'BCA']);
        $client_key = $user_bca->partner_client_id; //'90e932b8-49fb-4a81-95a2-14116bdba225';
        $stringtosign = $client_key . '|' . $timestamp;

        $algo = "SHA256";
        $signature = $this->signData($stringtosign, $algo);

        $external_id = $user_bca->company_code . $current_date->format('YmdHis');
        // $external_id = $current_date->format('His');
        return [
            "token" => $token_eps,
            "signature" => $signature,
            "external_id" => $external_id
        ];
    }

    public function signData($stringtosign, $algo)
    {
        $privateKey = <<<EOD
        -----BEGIN PRIVATE KEY-----
        MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQCaiGrtKdJKJ/oH
        a2+1KeT7Sd2xh/0wXUszKQP/ae9tzJd/pVdMtOj8X5EwgQ7IrgfSB40eFP0qVEvU
        i9JMb90jPH+e9iUcD9EJsb0fiBfuaj2ybbbZ9gAsV2Zr67D+c0iCwkKa9bYMN95m
        MKZ+mUafJinwWZR25H7MyBmg+U3jyp3eh+qLW+sjM7WdOjCLT7X2NyEV1J4DWzq2
        V6waYceO7g++zXIHpzT8DbZIJ2KIqy2VJBwjvQFIpz2MWCnutjkZB2iCWInTc5In
        k6fnHKiOCtzY4E3VKMZfdB9jwfXjKTaFfOGy9UuPvnEcIolpfKZQWloZci85v/rw
        wSt8owiXAgMBAAECggEBAJQ34i8lnNCJtXQmZRejXkBz4dJkt8EKypUAcxgo+IAc
        6vaAlNI69vkRhMW8E30CBvg5S+4dfZF7Ftx/W6764GTqoxHJz2Ax/3LH6rjypNmF
        RzX7q5U1MYdWMSO0BaiY1GSuhInywLDJaWQkp5zn0OfXCATDNvYuRTyPdJ2EYWPo
        tqT0KLMoSv91BKpE2LLXc54JegfSFA6UzfahFPbcQqqZpE/qRsy2j5J8D9s1q68+
        KYpGXSVQX5iXkncbrrNmtxijFFVDqlrEh9tZWVJdZvN7Z6sRd0GanhIai3soFwZ3
        bN/iLHhh20wWX9DRi5iZo45AqFWEr2m3dn/muCQNU2kCgYEAxvZQPe2e/dRraWZW
        3WqP2SQlKetH7CVmTfbGZDapkQBt27XWD3Y1mkoTND7Qpoxl74Iv8c7ve7vGwm1l
        eA2/0EMcCvsKNfDl+nSxV9ddGfmSLVuMznI8kCPzSlnsyiibS9aT8PlZkve2mo7z
        oij/D7BjSktdBXYEDHlC5JamQ0MCgYEAxtV42YDOZM2ZaMdL5a2J85zsGikxZlA7
        B0eScGh7SOZ1rx+L6xHbDRH0gOhCSk6ehNu1kVMhhQI/rH+cWi7XX80X5TXJ5nfy
        8etymCXX4Z/9y0I8dSGdaYxlGn5t2iPSPtvTlhITgYINViU+p4wv6jxlqcgK/GkP
        5UPooxEFTh0CgYEAms1ky3pJvTb8R6qfpXDW8V0FKWNtt1e2DK0X2TsKnc5Wq58E
        KU2RETXXUUwabatJWJvTj/GxNXV5hSc2zrzr5C+C7yw52pRPa5pFrZHcV2xuBqp3
        mN0bMA84qT3kVbpYch5HRzPLNOVVh1X4S9BX+64C4vhWLPyQ09+5Yz+vpx8CgYEA
        v9WqGT964iqzPjI6ecgq9s2JxdvEe5Agw288TBOiDr27AVEQb6X0j/Go0s5DVunv
        awOHdESebHO09zrPoPrcdOOtkEmLGD7WOK4PC9hHJrpz5K1tIx1hgDoiOaONXQ9+
        g4MX6wxZoXPWZizc/E321Kmc9Ge6objDy1DvnJSJZ8kCgYAJvuwXfZ8YekGOgYtU
        5Jj+ZtlqX5in8nA8grqI5qcKb5PCfViZA+PmRk7TCXVhr7hp/Ili6MFN6PgF6vq+
        wkDu8ZVsxS/USBwzciVe8CjXK9wDWdUKJQQOqlZSMIOUn0RB0pNU0S+hypFb8wFV
        5c1sTwx2FJu3ggzxMi/puBQlyQ==
        -----END PRIVATE KEY-----
        EOD;

        // Memuat kunci privat
        $privateKeyResource = openssl_get_privatekey($privateKey);

        if (!$privateKeyResource) {
            throw new Exception("Failed to load private key");
        }
        // Menandatangani data menggunakan SHA-256
        openssl_sign($stringtosign, $stringtosign, $privateKeyResource, $algo);
        $sign_asym  = base64_encode($stringtosign);
        return $sign_asym;
    }

    function json_validator($data)
    {
        if (!empty($data)) {
            json_decode($data);
            return (json_last_error() === JSON_ERROR_NONE);
        }
        return false;
    }

    public function generateServiceSignature($client_secret, $method, $url, $auth_token, $isoTime, $bodyToHash = [])
    {
        $signature['hash'] = hash("sha256", "");
        if (is_array($bodyToHash)) {
            $encoderData = json_encode($bodyToHash, JSON_UNESCAPED_SLASHES);
            $signature['hash'] = $this->hashbody($encoderData);
        }
        $signature['bodyjson'] = $bodyToHash;
        $signature['client_secret'] = $client_secret;
        $signature['stringToSign'] = $method . ":" . $this->getRelativeUrl($url) . ":" . $auth_token . ":" . $signature['hash'] . ":" . $isoTime;
        $signature['sign']  = base64_encode(hash_hmac('sha512', $signature['stringToSign'], $client_secret, true));
        return $signature;
    }


    public function validateServiceSignature($client_secret, $method, $url, $auth_token, $isoTime, $bodyToHash, $signature)
    {
        $is_valid = false;
        $signatureStr = $this->generateServiceSignature($client_secret, $method, $url, $auth_token, $isoTime, $bodyToHash);
        if (strcmp($signatureStr, $signature) == 0) {
            $is_valid = true;
        }
        return $is_valid;
    }


    private function hashbody($body)
    {
        if (empty($body)) {
            $body = '';
        } else {
        }
        return strtolower(hash('sha256', $body));
    }

    private function getRelativeUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (empty($path)) {
            $path = '/';
        }

        $query = parse_url($url, PHP_URL_QUERY);
        if ($query) {
            parse_str($query, $parsed);
            ksort($parsed);
            $query = '?' . http_build_query($parsed);
        }
        $formatedUrl = $path . $query;
        return $formatedUrl;
    }
}
