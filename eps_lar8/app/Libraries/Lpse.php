<?php

namespace App\Libraries;

use App\Models\Invoice;
use App\Models\Member;
use App\Services\JWTService;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Lpse
{
    protected $Config;
    private $def_is_pkp = true;
    private $max_merchant_score = 5;

    public function __construct()
    {
        $this->Config['ENVIRONMENT'] = env('ENVIRONMENT') ? env('ENVIRONMENT') : 'development';

        if ($this->Config['ENVIRONMENT'] == 'production') {
            $this->Config['x_client_id'] = 'u_elitexlpse2022';
            $this->Config['x_client_secret'] = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9';
            $this->Config['endpoint'] = 'https://tokodaring-api.lkpp.go.id/';
        } else {
            $this->Config['x_client_id'] = 'u_elitexlpse2022';
            $this->Config['x_client_secret'] = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9';
            $this->Config['endpoint'] = 'https://dev-tokodaring-api.lkpp.go.id/';
        }
    }

    // Pelaporan Transaksi 
    function report_trans($id_cart)
    {
        $endpoint = $this->Config['endpoint'] . 'report/transaksi/v3/save';

        $data = $this->getCompleteOrder($id_cart);
        if (!empty($data)) {
            $data_order_temp = $data[$id_cart];
            $id_user = $data_order_temp['id_user'];
            $buyer_email = $data_order_temp['email'];
            $buyer_username = $data_order_temp['username'];
            $invoice = $data_order_temp['invoice'];
            $final_price = $data_order_temp['total'];
            $list_kurir = [];
            $shipping_address = $data_order_temp['shipping_address'];
            $persen_ppn = $data_order_temp['val_ppn'];
            $persen_pph = $data_order_temp['val_pph'];
            $ppn_total = intval($data_order_temp['total_ppn']);
            $pph_total = intval($data_order_temp['total_pph']);
            $ongkos_kirim = $data_order_temp['sum_shipping_non_ppn'];
            $ongkos_kirim_ppn = round(($persen_ppn / 100) * $ongkos_kirim);
            $ongkos_kirim_pph = round(($persen_pph / 100) * $ongkos_kirim);
            $ongkos_kirim_ppn_persentase = $persen_ppn;
            $ongkos_kirim_pph_persentase = $persen_pph;
            $payment_method = $data_order_temp['payment_name'];

            $data_pembeli = array(
                'email'     => $data_order_temp['email'],
                'phone'     => $data_order_temp['no_hp'],
                'username'  => $data_order_temp['username']
            );

            foreach ($data_order_temp['shop'] as $key => $val) {
                $list_kurir[] = $val['deskripsi'];
                $merchant_type = 'besar';
                $merchant_score = 5;
                $merchant_id = $val['shop_id'];
                $merchant_name = $val['name'];
                $merchant_province = $val['province_name'];
                $merchant_city = $val['city_name'];
                $merchant_npwp = $val['shop_npwp'];
                $merchant_nik = $val['nik_pemilik'] ?: "0" ?? '0';

                if ($val['shop_type'] == 'silver') {
                    $merchant_type = 'kecil';
                    $merchant_score = 4;
                }

                foreach ($val['detail'] as $keyz => $valz) {
                    $is_product_available = true;
                    $is_taxable = true;
                    $is_pdn = true;
                    $product_curr_stock = $valz->stock;

                    if ($product_curr_stock < 1) {
                        $is_product_available = false;
                    }

                    if ($valz->val_ppn < 1) {
                        $is_taxable = false;
                    }

                    if ($valz->is_pdn == 0) {
                        $is_pdn = false;
                    }

                    $requiresBaseUrl = strpos($valz->image50, 'http') === false;
                    $product_image1 = $requiresBaseUrl ? "https://eliteproxy.co.id/" . $valz->image50 : $valz->image50;

                    $requiresBaseUrl = strpos($valz->image800, 'http') === false;
                    $product_image2 = $requiresBaseUrl ? "https://eliteproxy.co.id/" . $valz->image800 : $valz->image800;

                    $product_images = []; // Pastikan array diinisialisasi
                    array_push($product_images, $product_image1, $product_image2);

                    $product_thumbnail = $product_image1;
                    $product_code = $valz->product_id;
                    $product_name = str_replace(["[", "]", "{", "}", "'", '"', "."], "", $valz->product_name);
                    $list_keyword = explode(' ', $product_name);
                    $product_tags = [];
                    if (count($list_keyword) < 2) {
                        array_push($product_tags, $list_keyword[0]);
                        array_push($product_tags, 'default_tag');
                    } else {
                        if (isset($list_keyword[0])) {
                            array_push($product_tags, $list_keyword[0]);
                        }
                        if (isset($list_keyword[1])) {
                            array_push($product_tags, $list_keyword[1]);
                        }
                    }
                    $product_category_id = $valz->product_category_id;
                    $data_category = $this->get_id_lpse_cat($product_category_id);
                    $product_category_lpse_id = $data_category['id'];
                    $product_category_lpse_code = $data_category['code'];
                    $product_category = [$product_category_lpse_id => $product_category_lpse_code];

                    $product_desc = $valz->product_desc ?: '-';
                    $product_desc = str_replace(";", ",", $product_desc);

                    $product_weight = intval($valz->product_weight);
                    $product_merchant_score = $merchant_score;
                    $product_qty = intval($valz->qty);
                    $product_price = intval($valz->harga_dasar_lpse);
                    $total_price = intval($valz->total_non_ppn);
                    $ppn_percent = intval($valz->val_ppn);
                    $pph_percent = intval($valz->val_pph);
                    $product_ppn_val = intval($valz->nominal_ppn);
                    $product_pph_val = intval($valz->nominal_pph);
                    $is_pkp = $this->def_is_pkp;

                    $body_items[] = array(
                        "product_name" => $product_name,
                        "product_price" => $product_price,
                        "is_available" => $is_product_available,
                        "product_images" => $product_images,
                        "product_thumbnail" => $product_thumbnail,
                        "product_description" => $product_desc,
                        "product_category" => $product_category,
                        "product_weight" => $product_weight,
                        "tags" => $product_tags,
                        "is_PDN" => $is_pdn,
                        "is_taxable" => $is_taxable,
                        "SKU" => (string)$product_code,
                        "merchant_id" => (string)$merchant_id,
                        "merchant_name" => $merchant_name,
                        "merchant_province" => $merchant_province,
                        "merchant_city" => $merchant_city,
                        "merchant_npwp" => $merchant_npwp,
                        "merchant_nik" => $merchant_nik,
                        "is_pkp" => $is_pkp,
                        "merchant_score" => $product_merchant_score,
                        "max_merchant_score" => $this->max_merchant_score,
                        "merchant_type" => $merchant_type,
                        "kuantitas" => $product_qty,
                        "total" => $total_price,
                        "ppn" => $product_ppn_val,
                        "ppn_persentase" => $ppn_percent,
                        "pph" => $product_pph_val,
                        "pph_persentase" => $pph_percent,
                    );
                }
            }
            $lkpp = new Lkpp();
            $grand_total = intval($final_price);
            $metode_bayar = $payment_method;
            $nama_kurir = implode(', ', $list_kurir);
            $token = $lkpp->get_token($id_user);
        } else {
            return response()->json([
                'code' => 404,
                'erorr' => 'Invoice tidak ditemukan',
            ]);
        }
        // Mengambil waktu saat ini
        $timeString = now('Asia/Jakarta')->format('Y-m-d\TH:i:sP'); // Pastikan ini tidak menghasilkan array


        $body = [
            "order_id" => $invoice,
            "pembeli" => $data_pembeli,
            "items" => $body_items,
            "alamat_pengiriman" => $shipping_address,
            "ongkos_kirim" => intval($ongkos_kirim),
            "ongkos_kirim_ppn" => intval($ongkos_kirim_ppn),
            "ongkos_kirim_ppn_persentase" => intval($ongkos_kirim_ppn_persentase),
            "ongkos_kirim_pph" => intval($ongkos_kirim_pph),
            "ongkos_kirim_pph_persentase" => intval($ongkos_kirim_pph_persentase),
            "ppn_total" => $ppn_total,
            "pph_total" => $pph_total,
            "valuasi" => $grand_total,
            "tanggal" => $timeString,
            "metode_bayar" => $metode_bayar,
            "nama_kurir" => $nama_kurir,
            "token" => $token
        ];

        // hit API
        $result = $this->_report_trans($endpoint, $body);
        DB::table('api_log_report')->insert([
            'id_cart' => $id_cart,
            'payload' => json_encode($body),
            'response' => json_encode($result),
            'form_req' => 'SELF_GET',
        ]);

        if (isset($result['new_token']) && !empty($result['new_token'])) {
            $new_token = $result['new_token'];
            $update = DB::table('tr_approval_cart')->where('id_cart', $id_cart)->update(['is_lpse_report' => 1]);
            $this->renew_token($new_token, $id_user);
            return true;
        }

        return json_encode($result);
    }

    private function _report_trans($endpoint, $body)
    {
        $lpse = [
            'X-Client-Id' => $this->Config['x_client_id'],
            'X-Client-Secret' => $this->Config['x_client_secret']
        ];

        $response = Http::withHeaders($lpse)->post($endpoint, $body);

        return $response->json();
    }

    function confirm_trans($id_cart)
    {
        $endpoint = $this->Config['endpoint'] . 'report/transaksi/v3/update/transaction-done';
        $data = $this->getCompleteOrder($id_cart);
        $data_order_temp = $data[$id_cart];
        $Invoice = $data_order_temp['invoice'];

        $status = DB::table('complete_cart')->where('id', $id_cart)->value('status');

        // default Setting
        $keterangan_ppmse = 'Pesanan telah diterima oleh pembeli';
        $is_order_confirmed = true;

        if ($status == 'cancel') {
            $keterangan_ppmse = 'Pesanan batal karena tidak dibayarkan';
        } else if ($status == 'completed') {
            $keterangan_ppmse = 'Pesanan dibatalkan oleh marketplace, atas permintaan penjual atau pembeli';
        } else {
            $is_order_confirmed = false;
        }

        $body = [
            "order_id" => $Invoice,
            "konfirmasi_ppmse" => $is_order_confirmed,
            "keterangan_ppmse" => $keterangan_ppmse,
        ];

        $result = $this->_report_trans($endpoint, $body);
        DB::table('api_log_confirm')->insert([
            'id_cart' => $id_cart,
            'payload' => json_encode($body),
            'response' => json_encode($result),
        ]);

        if ($result['code'] == 200) {
            $update = DB::table('tr_approval_cart')->where('id_cart', $id_cart)->update(['is_lpse_report_confirm' => 1]);
            return true;
        }

        return json_encode($result);
    }

    function insertLogPayload($payload)
    {
        $id = DB::table('api_log_payload')
            ->insertGetId($payload);

        return $id;
    }

    public function getIdMember($data_member)
    {
        $id_instansi_lpse = $data_member['id_instansi_lpse'];
        $id_satker_lpse = $data_member['id_satker_lpse'];
        $id_bidang_lpse = $data_member['id_bidang_lpse'];

        $nm_instansi = $data_member['instansi'];
        $nm_satker = $data_member['satker'];
        $nm_bidang = $data_member['bidang'];

        $member = DB::table('member')
            ->where('email', $data_member['email'])
            ->where('id_instansi_lpse', $id_instansi_lpse)
            ->where('id_satker_lpse', $id_satker_lpse)
            ->where('id_bidang_lpse', $id_bidang_lpse)
            ->first();

        $id_instansi = $this->check_instansi($id_instansi_lpse, $nm_instansi);
        $id_satker = $this->check_satker($id_satker_lpse, $nm_satker, $id_instansi);
        $id_bidang = $this->check_bidang($id_bidang_lpse, $nm_bidang, $id_satker_lpse);

        if ($member) {
            $id = $member->id;

            if ($id_instansi) {
                DB::table('member')->where('id', $id)->update(['id_instansi' => $id_instansi]);
            }

            if ($id_satker) {
                DB::table('member')->where('id', $id)->update(['id_satker' => $id_satker]);
            }

            if ($id_bidang) {
                DB::table('member')->where('id', $id)->update(['id_bidang' => $id_bidang]);
            }

            DB::table('member')->where('id', $id)->update($data_member);
        } else {
            $data_member['id_member_type'] = 3;
            $data_member['password'] = 'lpse_password';
            $data_member['activation_key'] = 'lpse_activation_key';
            $data_member['member_status'] = 'active';

            $id = DB::table('member')->insertGetId($data_member);
        }

        return $id;
    }

    public function check_instansi($id_instansi, $nama_instansi)
    {
        $table = 'm_lpse_instansi';

        $row = DB::table($table)
            ->where('id_instansi', $id_instansi)
            ->first();

        if ($row) {
            // Data Exists
            $dataSave = [
                'nama' => $nama_instansi,
                'updated_date' => now(),
            ];

            DB::table($table)->where('id_instansi', $id_instansi)->update($dataSave);
            $id = $row->id;
        } else {
            // Data not Exists
            $dataSave = [
                'id_instansi' => $id_instansi,
                'nama' => $nama_instansi,
                'created_date' => now(),
            ];

            $id = DB::table($table)->insertGetId($dataSave);
        }

        return $id;
    }

    public function check_satker($id_satker, $nama_satker, $id_instansi)
    {
        $table = 'm_lpse_satker';

        $row = DB::table($table)
            ->where('id_satker', $id_satker)
            ->first();

        if ($row) {
            // Data Exists
            $dataSave = [
                'id_instansi' => $id_instansi,
                'nama' => $nama_satker,
                'updated_date' => now(),
            ];

            DB::table($table)->where('id_satker', $id_satker)->update($dataSave);
            $id = $row->id;
        } else {
            // Data not Exists
            $dataSave = [
                'id_satker' => $id_satker,
                'id_instansi' => $id_instansi,
                'nama' => $nama_satker,
                'created_date' => now(),
            ];

            $id = DB::table($table)->insertGetId($dataSave);
        }

        return $id;
    }

    public function check_bidang($id_bidang, $nama_bidang, $id_satker)
    {
        $table = 'm_lpse_bidang';

        $row = DB::table($table)
            ->where('id_bidang', $id_bidang)
            ->first();

        // Get id satker
        $id_satker_ = $this->get_satker_byId($id_satker) ?? null;

        if ($row) {
            // Data Exists
            $dataSave = [
                'nama' => $nama_bidang,
                'id_satker' => $id_satker_,
                'updated_date' => now(),
            ];

            DB::table($table)->where('id_bidang', $id_bidang)->update($dataSave);
            $id = $row->id;
        } else {
            // Data not Exists
            $dataSave = [
                'id_bidang' => $id_bidang,
                'id_satker' => $id_satker_,
                'nama' => $nama_bidang,
                'created_date' => now(),
            ];

            $id = DB::table($table)->insertGetId($dataSave);
        }

        return $id;
    }

    public function get_satker_byId($id_satker)
    {
        // NOTE $id_satker from LPSE
        $data = DB::table('m_lpse_satker')
            ->select('id')
            ->where('id_satker', $id_satker)
            ->first();

        if ($data) {
            return $data->id;
        }

        return false;
    }

    public function getDataById($id)
    {
        $result = DB::table('member')
            ->select('id', 'nama', 'foto', 'no_hp', 'password', 'email', 'member_status', 'id_member_type')
            ->where('id', $id)
            ->get();

        return $result;
    }

    public function checkUserAPI($data_user)
    {
        $count = DB::table('api_user')
            ->where($data_user)
            ->where('active_status', 'Y')
            ->count();

        return $count;
    }

    public function updateLogPayload($data_update, $id)
    {
        DB::table('api_log_payload')
            ->where('id', $id)
            ->update($data_update);

        return true;
    }

    public function check_token($token = null)
    {
        // Cek mode pemeliharaan
        $mainten = DB::table('site_config')->value('maintenance_mode');

        // Ambil data berdasarkan token
        $data = DB::table('api_log_payload as alp')
            ->select(
                'm.id',
                'alp.id_member',
                'pc.lpse_report_id AS id_lpse_cat',
                'alp.category',
                'alp.token_eps',
                'alp.token_lpse',
                'm.email',
                'm.username',
                'm.nama',
                'm.jenis_kelamin',
                'm.no_hp',
                'm.tgl_lahir',
                'm.foto',
                'm.npwp',
                'm.npwp_address',
                'm.instansi',
                'm.satker',
                'm.bidang',
                'm.id_instansi_lpse',
                'm.id_satker_lpse',
                'm.id_bidang_lpse',
                'm.id_instansi',
                'm.id_satker',
                'm.id_bidang',
                'm.id_member_type',
                'm.member_status',
                'm.is_email_subscribe',
                'm.activation_key',
                'm.registered_member',
                'm.last_update'
            )
            ->leftJoin('member as m', 'm.id', '=', 'alp.id_member')
            ->leftJoin('product_category as pc', 'pc.lpse_code', '=', 'alp.category')
            ->where('token_eps', $token)
            ->where('expired_dt_token', '>', now())
            ->first();

        if ($mainten == '1') {
            echo '<img src="https://eliteproxy.co.id/assets/images/maintenance.gif" width="100%">';
            exit();
        } elseif ($data) {
            return $data;
        } else {
            echo '<img src="https://eliteproxy.co.id/assets/images/default_403.gif" width="100%"></center>';
            exit();
        }
    }

    public function getCompleteOrder($id_cart, $id_shop = null)
    {
        $new_data = [];

        $data = DB::table('complete_cart as cc')
            ->select('cc.*', 'm.nama', 'm.email', 'm.no_hp', 'm.username', 'ma.address as shipping_address', 'pm.name as payment_name')
            ->leftJoin('member as m', 'm.id', '=', 'cc.id_user')
            ->leftJoin('member_address as ma', 'ma.member_address_id', '=', 'cc.id_address_user')
            ->leftJoin('payment_method as pm', 'pm.id', '=', 'cc.id_payment')
            ->where('cc.id', $id_cart)
            ->get();

        if ($data) {
            foreach ($data as $val) {
                $id = $val->id;
                $id_address = $val->id_address_user;
                $index = $id;

                $new_data[$index] = (array) $val; // Convert stdClass to array
                $new_data[$index]['shop'] = $this->getOrderShop($id, $id_address, $id_shop);
            }
        }

        return $new_data;
    }

    public function getOrderShop($id_cart, $id_address_user, $id_shop_ = null)
    {
        $dataShop = [];

        $data = DB::table('complete_cart_shop as cs')
            ->select(
                'cs.*',
                's.name',
                's.nik_pemilik',
                's.npwp as shop_npwp',
                's.id as shop_id',
                's.type as shop_type',
                'c.code as coupon_code',
                'total_weight',
                'sp.deskripsi',
                'sp.service',
                'sp.etd',
                'sp.price as price_ship',
                'courier.url_track',
                'm.email as shop_email',
                'sc.is_email_order',
                'ma.province_id',
                'ma.city_id',
                DB::raw('(select province_name from province where province_id = ma.province_id) as province_name'),
                DB::raw('(select city_name from city where city_id = ma.city_id) as city_name')
            )
            ->leftJoin('shop as s', 's.id', '=', 'cs.id_shop')
            ->leftJoin('member as m', 'm.id', '=', 's.id_user')
            ->leftJoin('member_address as ma', 'ma.member_id', '=', 's.id_user')
            ->leftJoin('shop_config as sc', 'sc.id_shop', '=', 's.id')
            ->leftJoin('coupon as c', 'c.id', '=', 'cs.id_coupon')
            ->leftJoin('shipping as sp', 'sp.id', '=', 'cs.id_shipping')
            ->leftJoin('courier', 'courier.id', '=', 'sp.id_courier')
            ->where('ma.is_shop_address', 'yes')
            ->where('ma.active_status', 'active')
            ->where('cs.id_cart', $id_cart)
            ->get();

        if ($data) {
            foreach ($data as $val) {
                $id = $val->id;
                $index = $id;

                $dataShop[$index] = (array) $val; // Convert stdClass to array
                $dataShop[$index]['address'] = $this->getAddress($id_address_user);

                $id_shop = empty($id_shop_) ? $val->id_shop : $id_shop_;
                $dataShop[$index]['detail'] = $this->getOrderShopDetail($id_cart, $id_shop);
            }
        }

        return $dataShop;
    }

    public function getOrderShopDetail($id_cart, $id_shop)
    {
        $data = DB::table('complete_cart_shop_detail as csd')
            ->select(
                'csd.*',
                'csd.id AS id_temp',
                'csd.price AS base_price',
                'pi.image50',
                'pi.image800',
                'p.id AS product_id',
                'p.sku AS product_code',
                'p.id_brand',
                'p.id_category AS product_category_id',
                'pc.lpse_report_id AS product_category_lpse_id',
                'pc.lpse_code AS product_category_lpse_code',
                'p.name AS product_name',
                'p.price_exclude',
                'p.price AS product_price',
                'p.weight AS product_weight',
                'p.description AS product_desc',
                'p.spesifikasi',
                'p.stock',
                'p.averange_rating',
                'p.count_rating',
                'p.status AS product_status',
                'p.is_pdn'
            )
            ->leftJoin('products as p', 'p.id', '=', 'csd.id_product')
            ->leftJoin('product_image as pi', function ($join) {
                $join->on('pi.id_product', '=', 'p.id')
                    ->where('pi.is_default', '=', 'yes');
            })
            ->leftJoin('product_category as pc', 'p.id_category', '=', 'pc.id')
            ->where('csd.id_cart', $id_cart)
            ->where('csd.id_shop', $id_shop)
            ->get();

        return $data->isNotEmpty() ? $data->toArray() : false;
    }

    public function getAddress($id_address)
    {
        $data = DB::table('member_address as ma')
            ->select(
                'ma.address',
                'ma.member_address_id',
                'ma.postal_code',
                'ma.address_name',
                'ma.phone',
                'p.province_name',
                'c.city_name',
                's.subdistrict_name',
                'ma.province_id',
                'ma.city_id',
                'ma.subdistrict_id',
                'ma.is_default_shipping',
                'ma.is_shop_address'
            )
            ->leftJoin('province as p', 'p.province_id', '=', 'ma.province_id')
            ->leftJoin('city as c', 'c.city_id', '=', 'ma.city_id')
            ->leftJoin('subdistrict as s', 's.subdistrict_id', '=', 'ma.subdistrict_id')
            ->where('ma.active_status', 'active')
            ->where('ma.member_address_id', $id_address)
            ->get();

        return $data->isNotEmpty() ? $data->toArray() : false;
    }

    private function get_id_lpse_cat($id_category)
    {
        $new_data = [];
        $temp_data = [];
        $parent_id = null;

        $data = DB::table('product_category')
            ->select('parent_id', DB::raw('group_concat(id) as id'))
            ->groupBy('parent_id')
            ->get();

        if ($data) {
            foreach ($data as $val) {
                $index = $val->parent_id;
                $list_id = explode(',', $val->id);
                if (in_array($id_category, $list_id)) {
                    $temp_data[$index] = [
                        'id' => $id_category,
                        'parent_id' => $val->parent_id,
                    ];
                    $parent_id = $val->parent_id;
                    break;
                }
            }
            if (!empty($parent_id)) {
                $result = $this->get_data_cat_lpse_byId($parent_id);
                if (empty($result)) {
                    return $this->get_id_lpse_cat($parent_id);
                } else {
                    return $result;
                }
            }
            return $temp_data;
        }
        return false;
    }

    private function get_data_cat_lpse_byId($id_category)
    {
        $data = DB::table('product_category')
            ->select('lpse_report_id as id', 'lpse_code as code', 'name as name')
            ->where('id', $id_category)
            ->first();

        return $data ? (array) $data : null;
    }

    function renew_token($new_token, $id_user)
    {
        $now            = date('Y-m-d H:i:s');
        // TIMESTAMP TOKEN EXPIRED
        $exp            = date('Y-m-d H:i:s', strtotime($now . ' +1 day'));
        $lkpp = new Lkpp();
        $last_data_token = $lkpp->check_token($id_user);
        $user = $lkpp->get_UserById($id_user);

        if (empty($last_data_token)) {
            $cat_lpse = 'atk';
        } else {
            $cat_lpse = $last_data_token->category;
        }

        // NOTE Generate new token
        $data_token = array(
            'id_member' => $id_user,
            'email' => $user['email'],
            'nama' => $user['username'],
            'default_cat' => $cat_lpse,
            'sess_start' => $now,
            'sess_expired' => $exp
        );

        $token_eps = JWTService::generateToken($data_token);
        // NOTE Save data to log
        $data_log = array(
            'header' => 'renew_token',
            'body' => 'renew_token',
            'token_eps' => $token_eps,
            'token_lpse' => $new_token,
            'category' => $cat_lpse,
            'expired_dt_token' => $exp,
            'id_member' => $id_user,
            'response' => '200',
        );

        $data_log2 = array(
            'id_member' => $id_user,
            'category' => $cat_lpse,
            'token' => $token_eps,
            'token_lpse' => $new_token,
            'expired_date' => $exp,
        );

        $lkpp->save_log_lpse_payload($data_log);
        $lkpp->save_token_lpse($data_log2, $id_user);
        return $token_eps;
    }
}
