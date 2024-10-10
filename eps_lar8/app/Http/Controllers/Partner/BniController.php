<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Libraries\BniEnc;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BniController extends Controller {
    //====================== Developer ==================//
    private $client_id = '44867';
    private $Prefix = '988';
    private $secret_key = '3c1a5a5cfd36cdce1dd1bcf9ec7cc735';
    private $url = 'https://apibeta.bni-ecollection.com/';

    //====================== Production ==================//
    // private $client_id = '';
    // private $Prefix = '';
    // private $secret_key = '';
    // private $url = 'https://api.bni-ecollection.com/';

    public function apiHiting(Request $request)
    {
        $json_data = $request->getContent();
        $data_asli = json_decode($json_data, true);

        $current_date = Carbon::now();
        $datetime_expired = $current_date->copy()->addYears(2);

        $hashed_string = BniEnc::encrypt($data_asli, $this->client_id, $this->secret_key);
        $data = [
            'client_id' => $this->client_id,
            'data' => $hashed_string,
        ];

        Log::info('Sending payload to BNI API: ' . json_encode($data));

        $response = Http::post($this->url, $data);
        $response_json = $response->json();

        Log::info('Response from BNI API: ' . json_encode($response_json));

        // Check if the 'status' key exists and is not empty
        $status = $response_json['status'] ?? 'unknown';
        $message = $response_json['message'] ?? 'No message received';

        Log::info('Status received from BNI API: ' . $status);
        Log::info('Message received from BNI API: ' . $message);

        if ($status === '000') {
            // If status is '000', decrypt the data
            $data_response = BniEnc::decrypt($response_json['data'], $this->client_id, $this->secret_key);
            Log::info('Decrypted response data: ' . json_encode($data_response));

            // Prepare data for database
            $db_data = [
                'payload' => json_encode($data_asli),
                'response' => json_encode($data_response), // Save decrypted response
                'type' => $data_asli['type'],
                'updated_at' => $current_date,
                'code' => $status,
            ];

            // Save to BNI Request
            DB::table('api_bni_request')->insert($db_data);

            return response()->json($data_response);
        } else {
            // Prepare data for database
            $db_data = [
                'payload' => json_encode($data_asli),
                'response' => json_encode($response_json), // Save original response
                'type' => 'test',
                'updated_at' => $current_date,
                'code' => $status,
            ];

            // Save to BNI Request
            DB::table('api_bni_request')->insert($db_data);

            return response()->json([
                'status' => 'Error',
                'message' => $message
            ]);
        }
    }


    public function createBilling(Request $request) {
        $id_cart = $request->input('id_cart');

        $MInvoice = new Invoice();
        $dataInvoice = $MInvoice->getDataInvoice($id_cart);
        $invoice = DB::table('complete_cart')
        ->select('complete_cart.id', 'invoice', 'total', 'm.nama', 'm.email', 'm.no_hp', 'm.instansi', 'm.satker', 'status_pembayaran_top')
        ->leftJoin('member as m', 'm.id', '=', 'complete_cart.id_user')
        ->where('complete_cart.id', $id_cart)
        ->first();
        return $invoice; 

        // Create VA Number
        $trx_date = substr($dataInvoice->invoice, 4, 8);
        $trx_id = substr($dataInvoice->invoice, -3);
        $virtual_account = $this->Prefix . $this->client_id . substr($trx_date . $trx_id, -8);

        // Get Date
        $current_date = date('Y-m-d H:i:s');
        $datetime_expired = date('Y-m-d H:i:s', strtotime($current_date . ' +2 years'));

        // Data yang akan dienkripsi dan dikirim ke BNI
        $data_asli = [
            'client_id' => $this->client_id,
            'trx_id' => $dataInvoice->invoice,
            'trx_amount' => $dataInvoice->total,
            'billing_type' => 'c',
            'datetime_expired' => $datetime_expired,
            'virtual_account' => $virtual_account,
            'customer_name' => $dataInvoice->nama,
            'customer_email' => $dataInvoice->email,
            'customer_phone' => '',
            'type' => 'createBilling',
        ];

        // Enkripsi data
        $hashed_string = BniEnc::encrypt($data_asli, $this->client_id, $this->secret_key);

        // Data yang akan dikirim ke API BNI
        $data = array(
            'client_id' => $this->client_id,
            'data' => $hashed_string,
        );

        // Logging data yang akan dikirim
        Log::info('Sending payload to BNI API', $data);

        // Mengirim permintaan ke API BNI
        $response = $this->get_content($this->url, json_encode($data));
        $response_json = json_decode($response, true);

        // Logging respons dari API
        Log::info('Response from BNI API', ['response' => $response]);

        // Memeriksa status respons dari API
        if ($response_json['status'] !== '000') {
            // Jika gagal, tampilkan pesan kesalahan
            return response()->json([
                'status' => $response_json['status'],
                'message' => $response_json['message']
            ], 500);
        } else {
            // Jika berhasil, dekripsi data respons
            $data_response = BniEnc::decrypt($response_json['data'], $this->client_id, $this->secret_key);

            // Prepare data for database
            $db_data = [
                'payload' => json_encode($data_asli),
                'response' => json_encode($data_response), // Save decrypted response
                'type' => $data_asli['type'],
                'updated_at' => $current_date,
                'code' => $response_json['status'],
            ];

            // Menyimpan data ke tabel api_bni_request
            DB::table('api_bni_request')->insert($db_data);

            $this->inquiryBilling($dataInvoice->invoice);

            return response()->json($data_response);
        }
    }

    public function inquiryBilling($trx_id) {
        $current_date = date('Y-m-d H:i:s');

        // Data yang akan dienkripsi dan dikirim ke BNI
        $data_asli = array(
            'type' => 'inquirybilling',
            'client_id' => $this->client_id,
            'trx_id' => $trx_id,
        );

        // Enkripsi data
        $hashed_string = BniEnc::encrypt($data_asli, $this->client_id, $this->secret_key);

        // Data yang akan dikirim ke API BNI
        $data = array(
            'client_id' => $this->client_id,
            'data' => $hashed_string,
        );

        // Logging data yang akan dikirim
        Log::info('Sending payload to BNI API for inquiryBilling', $data);

        // Mengirim permintaan ke API BNI
        $response = $this->get_content($this->url, json_encode($data));
        $response_json = json_decode($response, true);

        // Logging respons dari API
        Log::info('Response from BNI API for inquiryBilling', ['response' => $response]);

        // Memeriksa status respons dari API
        if ($response_json['status'] !== '000') {
            // Jika gagal, tampilkan pesan kesalahan
            return response()->json([
                'status' => $response_json['status'],
                'message' => $response_json['message']
            ], 500);
        } else {
            // Jika berhasil, dekripsi data respons
            $data_response = BniEnc::decrypt($response_json['data'], $this->client_id, $this->secret_key);

            // Prepare data for database
            $db_data = [
                'payload' => json_encode($data_asli),
                'response' => json_encode($data_response), // Save decrypted response
                'type' => $data_asli['type'],
                'updated_at' => $current_date,
                'code' => $response_json['status'],
            ];

            // Menyimpan data ke tabel api_bni_request
            DB::table('api_bni_request')->insert($db_data);

            // Set for Report BNI
            $status_pembayaran = $data_response['datetime_payment'] === null ? 'unpaid' : 'paid';
            $status_va = $data_response['va_status'] == 1 ? 'active' : 'inactive';
            $id_cart = substr($trx_id, -3);

            $report_data = [
                'id_cart' => $id_cart,
                'trx_id' => $trx_id,
                'total' => $data_response['trx_amount'],
                'status_pembayaran' => $status_pembayaran,
                'va_number' => $data_response['virtual_account'],
                'status_va' => $status_va,
                'created_at' => $current_date,
                'updated_at' => $current_date,
                'expired_at' => $data_response['datetime_expired'],
            ];

            $saveVA = DB::table('complete_cart')->where('id_cart', $id_cart)
            ->update([
                    'va_number' => $data_response['virtual_account']
                ]);

            // Menyimpan data ke tabel api_bni_report
            DB::table('api_bni_report')->insert($report_data);

            return response()->json($data_response);
        }
    }

    public function updateBilling(Request $request) {
        $current_date = date('Y-m-d H:i:s');

        // Data yang akan dienkripsi dan dikirim ke BNI
        $data_asli = array(
            'type' => 'updatebilling',
            'client_id' => $this->client_id,
            'trx_id' => '426468898',
            'trx_amount' => '10000',
            'customer_name' => 'Mr. X',
            // 'trx_id' => $request->trx_id, // ID Transaksi yang ingin di-update
            // 'trx_amount' => $request->trx_amount, // Jumlah transaksi baru
            // 'customer_name' => $request->customer_name, // Nama customer baru
        );

        // Tambahkan data opsional jika ada
        if ($request->has('description')) {
            $data_asli['description'] = $request->description;
        }
        if ($request->has('datetime_expired')) {
            $data_asli['datetime_expired'] = $request->datetime_expired;
        }

        // Enkripsi data
        $hashed_string = BniEnc::encrypt($data_asli, $this->client_id, $this->secret_key);

        // Data yang akan dikirim ke API BNI
        $data = array(
            'client_id' => $this->client_id,
            'data' => $hashed_string,
        );

        // Logging data yang akan dikirim
        Log::info('Sending payload to BNI API for updateBilling', $data);

        // Mengirim permintaan ke API BNI
        $response = $this->get_content($this->url, json_encode($data));
        $response_json = json_decode($response, true);

        // Logging respons dari API
        Log::info('Response from BNI API for updateBilling', ['response' => $response]);

        // Memeriksa status respons dari API
        if ($response_json['status'] !== '000') {
            // Jika gagal, tampilkan pesan kesalahan
            return response()->json([
                'status' => $response_json['status'],
                'message' => $response_json['message']
            ], 500);
        } else {
            // Jika berhasil, dekripsi data respons
            $data_response = BniEnc::decrypt($response_json['data'], $this->client_id, $this->secret_key);

            // Prepare data for database
            $db_data = [
                'payload' => json_encode($data_asli),
                'response' => json_encode($data_response), // Save decrypted response
                'type' => $data_asli['type'],
                'updated_at' => $current_date,
                'code' => $response_json['status'],
            ];

            // Menyimpan data ke tabel api_bni_request
            DB::table('api_bni_request')->insert($db_data);

            return response()->json($data_response);
        }
    }

    public function transactionPaymentNotification(Request $request)
    {
        $current_date = Carbon::now();

        // Data yang diterima dari notifikasi pembayaran
        $json_data = $request->getContent();
        $data = json_decode($json_data, true);

        if (!$data) {
            // Jika tidak ada data yang diterima
            Log::error('No data received in payment notification');
            return response()->json(['status' => 'error', 'message' => 'No data received in payment notification']);
        }

        // Logging data yang diterima
        Log::info('Received payment notification: ' . json_encode($data));

        // Mendekripsi data jika ada
        if (isset($data['data'])) {
            $decrypted_data = BniEnc::decrypt($data['data'], $this->client_id, $this->secret_key);
            // Logging data yang didekripsi
            Log::info('Decrypted payment notification: ' . json_encode($decrypted_data));
        } else {
            // Jika tidak ada data terenkripsi
            Log::error('No encrypted data received in payment notification');
            return response()->json(['status' => 'error', 'message' => 'No encrypted data received in payment notification']);
        }

        // Menyiapkan data untuk penyimpanan
        $db_data = [
            'payload' => json_encode($decrypted_data),
            'response' => json_encode($data),
            'type' => 'notification',
            'updated_at' => $current_date,
            'code' => '000',
        ];

        // Menyimpan data ke dalam database
        DB::table('api_bni_request')->insert($db_data);

        $update_data = [
            'trx_id' => $decrypted_data['trx_id'],
            'status_pembayaran' => 'paid',
            'status_va' => 'inactive',
            'datetime_payment' => $decrypted_data['datetime_payment'],
            'updated_at' => $current_date,
        ];

        // Logging sebelum update
        Log::info('Updating report for trx_id: ' . $decrypted_data['trx_id'] . ' with data: ' . json_encode($update_data));

        // Check if the transaction ID already exists
        // $existing_report = DB::table('reports')->where('trx_id', $decrypted_data['trx_id'])->first();
        // if ($existing_report) {
            $affected_rows = DB::table('api_bni_report')
                ->where('trx_id', $decrypted_data['trx_id'])
                ->update($update_data);

            $id_cart = DB::table('complete_cart')->where('invoice', $decrypted_data['trx_id'])->value('id_cart');
            $confirm_payment = DB::table('complete_cart')->where('id_cart', $id_cart)->update(
                [
                    'status' => 'complete_payment',
                    'updated_status_by' => 16,
                    'status_pembayaran_top'=>1,
                    'tanggal_bayar' => $decrypted_data['datetime_payment'],
                ]);

            Log::info('Updated report affected rows: ' . $affected_rows);
            if ($affected_rows > 0) {
                Log::info('Report successfully updated for trx_id: ' . $decrypted_data['trx_id']);
            } else {
                Log::error('Failed to update report for trx_id: ' . $decrypted_data['trx_id']);
            }
        // } else {
        //     // Insert a new record
        //     DB::table('reports')->insert($update_data);
        //     // Logging hasil insert
        //     Log::info('Inserted new report for trx_id: ' . $decrypted_data['trx_id']);
        // }

        // Mengembalikan respons sukses
        return response()->json(['status' => '000', 'message' => 'Payment notification received successfully']);
    }

    // Fungsi untuk mengirim permintaan HTTP menggunakan cURL
    private function get_content($url, $post = '') {
        $header[] = 'Content-Type: application/json';
        $header[] = "Accept-Encoding: gzip, deflate";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, 'https://ifconfig.me/ip');
        // $public_ip = curl_exec($ch);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        $rs = curl_exec($ch);

        if (empty($rs)) {
            Log::error('cURL error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        return $rs;
    }

    function descrypt_bni($data){
        $decrypted_data = BniEnc::decrypt($data, $this->client_id, $this->secret_key);
        return $decrypted_data;
    }

}
