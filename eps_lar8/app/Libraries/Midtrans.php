<?php

namespace App\Libraries;

use App\Models\Invoice;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Swift_TransportException;

use function PHPUnit\Framework\returnSelf;

class Midtrans
{
    function getLpseTokenReq($id_cart)
    {
        $token_lpse = DB::table('midtrans_request_log')
            ->where('id_cart', $id_cart)
            ->limit(1)
            ->value('token_lpse');
        return $token_lpse;
    }

    function getDataInvoice($id_cart)
    {
        $invoice = DB::table('complete_cart')
            ->select(
                'complete_cart.id',
                'complete_cart.invoice',
                'complete_cart.total',
                'complete_cart.status_pembayaran_top',
                'm.nama',
                'm.email',
                'm.no_hp',
                'm.instansi',
                'm.satker',
                'm.id as id_member'
            )
            ->leftJoin('member as m', 'm.id', 'complete_cart.id_user')
            ->where('complete_cart.id', $id_cart)
            ->first();

        return $invoice;
    }

    function get_trans_detail_data($id_cart)
    {
        $query = DB::table('complete_cart as a')
            ->select(
                'a.id',
                'a.invoice',
                'a.id_user',
                'a.id_address_user',
                'a.id_voucher',
                'a.qty',
                'a.total',
                'a.id_payment',
                'a.payment_method',
                'a.payment_detail',
                'a.status',
                'a.created_date',
                'a.due_date_payment',
                'a.last_update',
                'a.tanggal_bayar',
                'a.updated_status_by',
                'a.status_pembayaran_top',
                'a.va_number',
                'a.note',
                'a.jml_top',
                'b.id_address as id_address_shipping',
                'b.id_billing_address as id_address_billing',
                'a.sum_price_non_ppn',
                'a.sum_shipping',
                'a.sum_shipping_non_ppn',
                'a.total_ppn',
                'a.total_pph',
                'a.sum_discount',
                'a.handling_cost',
                'a.handling_cost_non_ppn',
                'c.name as payment_name',
                'd.status_approve as status_approve_cart',
                'cs.keperluan',
                'cs.pesan_seller',
                's.name as shop_name',
                'a.val_ppn',
                'a.val_pph'
            )
            ->leftJoin('complete_cart_address as b', function ($join) {
                $join->on('b.id_cart', '=', 'a.id')
                    ->where('b.is_shop_address', '=', 0);
            })
            ->leftJoin('complete_cart_shop as cs', 'cs.id_cart', '=', 'a.id')
            ->leftJoin('shop as s', 's.id', '=', 'cs.id_shop')
            ->leftJoin('payment_method as c', 'a.id_payment', '=', 'c.id')
            ->leftJoin('tr_approval_cart as d', 'a.id', '=', 'd.id_cart')
            ->where('a.id', $id_cart)
            ->orderBy('a.created_date', 'desc')
            ->first();


        if ($query) {
            $trans_by_user_id = $query->id_user;
            $id_address_shipping = $query->id_address_shipping;
            $id_address_billing = $query->id_address_billing ?? $id_address_shipping;

            $shop_arr = [];

            $shop_data = $this->_get_trans_data_shop($id_cart);
            $shop_detail = $this->_get_trans_data_shop_detail($id_cart);

            $address_shipping = $this->_get_trans_data_address($id_address_shipping);
            $address_billing = $this->_get_trans_data_address($id_address_billing);
            $user_data = $this->_get_trans_data_member($trans_by_user_id);

            $address_arr = [
                'shipping' => $address_shipping,
                'billing' => $address_billing,
            ];

            foreach ($shop_data as $keyz => $valz) {
                $shop_arr[$keyz]['data'] = $valz;
                $shop_arr[$keyz]['detail'] = $shop_detail[$keyz];
            }

            // TODO pengecekan pembayaran kartu kredit ke log midtrans jika ada maka buatkan tombol untuk update status transaksi

            $new_data = [
                'id_cart' => $id_cart,
                'invoice' => $query->invoice,
                'status' => $query->status,

                'created_date' => $query->created_date,
                'last_update' => $query->last_update,
                'id_payment' => $query->id_payment,
                'due_date_payment' => $query->due_date_payment,
                'va_number' => $query->va_number,
                'note' => $query->note,
                'pesan_seller' => $query->pesan_seller,
                'keperluan' => $query->keperluan,
                'status_pembayaran_top' => $query->status_pembayaran_top,

                'shop_name' => $query->shop_name,
                'jml_top' => $query->jml_top,

                'sum_shipping' => $query->sum_shipping,
                'sum_price_non_ppn' => $query->sum_price_non_ppn,
                'sum_shipping_non_ppn' => $query->sum_shipping_non_ppn,
                'sum_discount' => $query->sum_discount,
                'handling_cost' => $query->handling_cost,
                'handling_cost_non_ppn' => $query->handling_cost_non_ppn,
                'total_ppn' => $query->total_ppn,
                'total_pph' => $query->total_pph,
                'total' => $query->total,
                'val_ppn' => $query->val_ppn,
                'val_pph' => $query->val_pph,

                'payment_name' => $query->payment_name,
                'status_approve_cart' => $query->status_approve_cart,

                'user' => $user_data,
                'address' => $address_arr,
                'shop' => array_values($shop_arr),
            ];

            return $new_data;
        }
    }

    private function _get_trans_data_shop($id_cart)
    {
        // Query untuk mendapatkan data dari tabel 'complete_cart_shop'
        $query = DB::table('complete_cart_shop as a')
            ->select(
                'a.id',
                'a.pmk',
                'a.setor_pph',
                'a.setor_ppn',
                'a.file_pajak',
                'a.status_pajak',
                'a.penyetuju_pajak',
                'a.id_cart',
                'a.id_shop',
                'a.id_address_shop',
                'a.id_shipping',
                'c.id_courier',
                'a.is_insurance',
                'a.id_coupon',
                'a.discount',
                'b.name as shop_name',
                'b.npwp',
                'a.total_weight',
                'a.qty',
                'a.sum_price',
                'a.note',
                'a.note_seller',
                'a.status',
                'a.no_resi',
                'a.is_bast',
                'c.deskripsi as shipping_desc',
                'c.service as shipping_service',
                'c.etd as shipping_etd',
                'd.code as code_coupon',
                'd.name as name_coupon',
                'a.insurance_nominal',
                'a.sum_shipping',
                'a.ppn_price',
                'a.pph_price',
                'a.ppn_shipping',
                'a.pph_shipping',
                'a.total as total_shop',
                'a.subtotal',
                'a.pesan_seller',
                'a.keperluan',
                'cc.val_ppn as ppn',
                'a.handling_cost',
                'a.handling_cost_non_ppn'
            )
            ->leftJoin('complete_cart as cc', 'cc.id', '=', 'a.id_cart')
            ->join('shop as b', 'b.id', '=', 'a.id_shop')
            ->leftJoin('shipping as c', 'a.id_shipping', '=', 'c.id')
            ->leftJoin('coupon as d', 'd.id', '=', 'a.id_coupon')
            ->where('a.id_cart', $id_cart)
            ->get();

        // Inisialisasi array untuk menampung data
        $new_data = [];

        // Periksa jika query menghasilkan data
        if (!empty($query)) {
            foreach ($query as $val) {
                $index = $val->id_shop;

                $new_data[$index] = [
                    'id' => $val->id,
                    'id_cart' => $val->id_cart,
                    'id_shop' => $val->id_shop,
                    'shop_name' => $val->shop_name,

                    // Total Semua Produk
                    'qty' => $val->qty,
                    'sum_price' => $val->sum_price,
                    'sum_shipping' => $val->sum_shipping,
                    'discount' => $val->discount,

                    'is_insurance' => $val->is_insurance,
                    'insurance_nominal' => $val->insurance_nominal,
                    'ppn' => $val->ppn,
                    'ppn_shipping' => $val->ppn_shipping,
                    'pph_shipping' => $val->pph_shipping,
                    'ppn_price' => $val->ppn_price,
                    'pph_price' => $val->pph_price,
                    'handling_cost' => $val->handling_cost,
                    'handling_cost_non_ppn' => $val->handling_cost_non_ppn,

                    // Grand total shop
                    'total_shop' => $val->total_shop,
                    'subtotal' => $val->subtotal,

                    'total_weight' => $val->total_weight,
                    'status' => $val->status,
                    'no_awb' => $val->no_resi,
                    'id_courier' => $val->id_courier,
                    'is_bast' => $val->is_bast,
                    'note' => $val->note,
                    'note_seller' => $val->note_seller,
                    'shipping_desc' => $val->shipping_desc,
                    'shipping_service' => $val->shipping_service,
                    'shipping_etd' => $val->shipping_etd,
                    'pesan_seller' => $val->pesan_seller,
                    'keperluan' => $val->keperluan,
                    'npwp' => $val->npwp,

                    'pmk' => $val->pmk,
                    'setor_pph' => $val->setor_pph,
                    'setor_ppn' => $val->setor_ppn,
                    'file_pajak' => $val->file_pajak,
                    'status_pajak' => $val->status_pajak,
                    'penyetuju_pajak' => $val->penyetuju_pajak,

                    'id_coupon' => $val->id_coupon,
                    'code_coupon' => $val->code_coupon,
                    'name_coupon' => $val->name_coupon,
                ];
            }
        }

        return $new_data;
    }


    private function _get_trans_data_shop_detail($id_cart)
    {
        $query = DB::table('complete_cart_shop_detail as a')
            ->select(
                'a.id',
                'a.id_cart',
                'a.id_shop',
                'a.id_product',
                'a.nama',
                'a.image',
                'a.price',
                'a.qty',
                'a.weight',
                'a.total_weight',
                'a.total_non_ppn',
                'a.total',
                'a.status',
                'a.val_ppn',
                'a.id_nego',
                DB::raw('IF(a.val_ppn != 0, 1, 0) as is_ppn'),
                // 'b.seoname as product_seoname'
            )
            ->leftJoin('products as b', 'b.id', '=', 'a.id_product')
            ->where('a.id_cart', $id_cart)
            ->get();

        // Inisialisasi array untuk menampung data
        $new_data = [];

        if (!empty($query)) {
            foreach ($query as $key => $val) {
                $index = $val->id_shop;

                $new_data[$index][] = [
                    'id' => $val->id,
                    'id_cart' => $val->id_cart,
                    'id_shop' => $val->id_shop,
                    'product_id' => $val->id_product,
                    'product_name' => $val->nama,
                    'product_image' => $val->image,
                    'product_price' => $val->price,
                    'product_qty' => $val->qty,
                    'total_weight' => $val->total_weight,
                    'total_non_ppn' => $val->total_non_ppn,
                    'total' => $val->total,
                    'status' => $val->status,
                    'val_ppn' => $val->val_ppn,
                    'is_ppn' => $val->is_ppn,
                    'id_nego' => $val->id_nego,

                    'product_seoname' => Fungsi::getSeoName($val->nama),
                ];
            }
        }

        return $new_data;
    }

    private function _get_trans_data_address($id_address)
    {
        $query = DB::table('member_address as a')
            ->select(
                'a.member_address_id',
                'a.address_name',
                'a.phone',
                'a.province_id',
                'a.city_id',
                'a.subdistrict_id',
                'a.address',
                'a.postal_code',
                'wil_prov.province_name',
                'wil_city.city_name',
                'wil_subdistrict.subdistrict_name'
            )
            ->leftJoin('province as wil_prov', 'wil_prov.province_id', '=', 'a.province_id')
            ->leftJoin('city as wil_city', 'wil_city.city_id', '=', 'a.city_id')
            ->leftJoin('subdistrict as wil_subdistrict', 'wil_subdistrict.subdistrict_id', '=', 'a.subdistrict_id')
            ->where('a.member_address_id', $id_address)
            ->get();

        // Inisialisasi array untuk menampung data
        $new_data = [];
        if (!empty($query)) {
            $row = $query[0];

            $new_data = [
                'address_id' => $row->member_address_id,
                'address_name' => $row->address_name,
                'address_phone' => $row->phone,
                'address_detail' => $row->address,
                'address_postal_code' => $row->postal_code,

                'address_province_id' => $row->province_id,
                'address_city_id' => $row->city_id,
                'address_subdistrict_id' => $row->subdistrict_id,

                'address_province_name' => $row->province_name,
                'address_city_name' => $row->city_name,
                'address_subdistrict_name' => $row->subdistrict_name,
            ];
        }

        return $new_data;
    }

    private function _get_trans_data_member($id_user)
    {
        $query = DB::table('member as a')
            ->select(
                'a.id',
                'a.email',
                'a.username',
                'a.nama',
                'a.no_hp',
                'a.npwp',
                'a.instansi',
                'a.satker',
                'a.bidang',
                'a.id_member_type',
                'b.nama AS nm_instansi',
                'c.nama AS nm_satker',
                'd.nama AS nm_bidang'
            )
            ->leftJoin('m_lpse_instansi as b', 'b.id', '=', 'a.id_instansi')
            ->leftJoin('m_lpse_satker as c', 'c.id', '=', 'a.id_satker')
            ->leftJoin('m_lpse_bidang as d', 'd.id', '=', 'a.id_bidang')
            ->where('a.id', $id_user)
            ->get();


        // Inisialisasi array untuk menampung data
        $new_data = [];
        if (!empty($query)) {
            $row = $query[0];

            $new_data = [
                'email' => $row->email,
                'username' => $row->username,
                'nama' => $row->nama,
                'no_hp' => $row->no_hp,
                'npwp' => $row->npwp,
                'nm_instansi' => $row->nm_instansi ?? $row->instansi ?? null,
                'nm_satker' => $row->nm_satker ?? $row->satker ?? null,
                'nm_bidang' => $row->nm_bidang ?? $row->bidang ?? null,
            ];
        }

        return $new_data;
    }

    function insertMidRequest($data)
    {
        $id_cart = $data['id_cart'];
        $query = DB::table('midtrans_request_log')
            ->where('id_cart', $id_cart)->count();
        if ($query < 1) {
            DB::table('midtrans_request_log')->insert($data);
        } else {
            DB::table('midtrans_request_log')->where('id_cart', $id_cart)->update($data);
        }
        return true;
    }

    function get_snapToken($data_params)
    {
        $Snap = new MidtransService();
        $midtrans_payment = $Snap->getSnapToken($data_params);
        $snapToken = $midtrans_payment['token'];
        $redirect_url = $midtrans_payment['redirect_url'];

        return [
            'token' => $snapToken,
            'redirect_url' => $redirect_url,
            'client_key' => 'SB-Mid-client-BifLmcHRgJ2C-ich',
            'server_key' => 'SB-Mid-server-nQTkR2H2s3RtpF8Lv3b2h_8M',
        ];
    }

    function get_status($id_cart)
    {
        $order_id = DB::table('midtrans_request_log')->where('id_cart', $id_cart)->value('id_order');
        $Snap = new MidtransService();
        $response = $Snap->status($order_id);
        $new_data = [];

        $result = json_decode(json_encode($response), true);

        $transaction = $result['transaction_status'];
        $type = $result['payment_type'];
        $fraud = $result['fraud_status'];
        $status_code = $result['status_code'];
        $status_message = $result['status_message'];
        $st_lpse = '';
        $orderid_exp = explode("-", $order_id);

        if ($transaction == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $payment_status = 'pending';
                    $st_lpse = '';
                } else {
                    $payment_status = 'complete_payment';
                    $st_lpse = '1';

                    $send_email = $this->send_email($id_cart);
                }
            }
        } elseif ($transaction == 'settlement') {
            $payment_status = 'complete_payment';
            $st_lpse = '1';
            $send_email = $this->send_email($id_cart);
        } elseif ($transaction == 'pending') {
            $payment_status = 'pending';
            $st_lpse = '';
        } elseif ($transaction == 'deny') {
            $payment_status = 'expired';
            $st_lpse = '0';
        } elseif ($transaction == 'expire') {
            $payment_status = 'expired';
            $st_lpse = '0';
        } elseif ($transaction == 'cancel') {
            $payment_status = 'cancel';
            $st_lpse = '0';
        }

        $new_data['transaction'] = $transaction;
        $new_data['payment_status'] = $payment_status;
        $new_data['payment_method'] = $type;
        $new_data['status_message'] = $status_message;
        $new_data['st_lpse'] = $st_lpse;
        return $new_data;
    }

    function send_email($id_cart)
    {
        $Minvoice           = new Invoice();
        $data_cart          = $Minvoice->getCompleteOrder($id_cart);
        $data_popularsearch = $Minvoice->getPopularSearch();

        $content_pop = '';
        $content_pop .= '<tbody style="background-color:white">
							<tr><td colspan="5" align="center"><h4>PENCARIAN POPULAR</h4></td></tr><tr>';

        foreach ($data_popularsearch as $purr => $ps) {

            $requiresBaseUrl = strpos($ps->default_image, 'http') === false;
            $pc_image = $requiresBaseUrl ? "https://eliteproxy.co.id/" . $ps->default_image : $ps->default_image;

            $content_pop .= '<td width="20%" center-align bor1">
						<a href="' . env('APP_URL') . '/find/' . $ps->keyword . '" class="black-text">
							<img height="72px" width="72px" src="' . $pc_image . '">
							<br><span class="grey-text smfont"><small>' . number_format($ps->search_count) . 'x dicari<small></span>
						</a>
						</td>';
        }

        $content_pop .= '</tr></tbody>';

        foreach ($data_cart as $data) {
            $invoice     = $data->invoice;
            $user_id     = $data->id_user;
            $email         = $data->email;
            $nama         = $data->nama;
            $pesanan     = 'Rp ' . str_replace(',', '.', number_format($data->sum_price));
            $ongkir     = 'Rp ' . str_replace(',', '.', number_format($data->sum_shipping));
            $tanggal    = $data->created_date;
            $penanganan = 'Rp ' . str_replace(',', '.', number_format($data->handling_cost));
            $total         = 'Rp ' . str_replace(',', '.', number_format($data->total));
            //DETAIL ORDER BY ID_CART AND ID_SHOP
            $content_product     = '';
            $shop_name             = '';
            $product             = '';

            foreach ($data->shop as $shop) {

                // if ($shop->is_email_order == 'Y') {
                //     $send_email_seller = $this->send_seller($id_cart, $shop->id_shop);
                // }

                $shop_name = $shop->name;
                $content_product .= '<p><b><a href="' . env('APP_URL') . ('toko/detail/' . $shop->id_shop) . '">' . $shop_name . '</a></b></p>';
                //DETAIL PRODUCT ORDER BY ID_CART AND ID_SHOP
                foreach ($shop->detail as $detail) {
                    $requiresBaseUrl = strpos($detail->image50, 'http') === false;
                    $image_produk = $requiresBaseUrl ? "https://eliteproxy.co.id/" . $detail->image50 : $detail->image50;
                    $content_product .= '<p><a href="' . env('APP_URL') . ('/product/' . $detail->id_product) . '"><img src="' . $image_produk . '" width="80" height="80">' . $detail->qty . ' x ' . $detail->name . '</a></p>';
                }
            }
        }

        $subject     = "Pesanan " . $invoice . " telah dibayar";
        $to         = $email;
        $content =  '<div bgcolor="#fafafa" style="font-family:"Open-Sans", sans-serif">
				<table style="background-color: #fff; margin: 5% auto; width: 100%; max-width: 600px" cellspacing="0" cellpadding="0" border="0" bgcolor="#fff" align="center">
					<tbody>
						<tr>
							<td>
								<table style="padding: 15px; font-size: 14px; width: 100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#1b252f" align="center">
									<tbody>
										<tr>
											<td align="center">
												<img src="https://eliteproxy.co.id/logo.png" class="center" height="50">
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table style="padding: 25px 15px 10px; font-size: 14px; width: 100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#eee">
									<tbody>
										<tr>
											<td style="padding-bottom: 10px;">
												<strong style="font-size: 16px; line-height: 20px; font-weight: bold">Hi ' . $nama . ',</strong>
											</td>
										</tr>
										<tr>
										</tr>
										<tr>
											<td style="padding-bottom: 10px;">
												<p>Pesanan <a href="' . $invoice . ' sudah dibayar, pesanan akan segera dikirimkan oleh penjual dan sampai sesuai estimasi pengiriman.</p>
												<p>Mohon menerima dan mengkonfirmasi pesanan setelah pesanan diterima. Setelah dikonfirmasi, pembayaran akan dilepas ke <a href="' . env('APP_URL') . ('toko/detail/' . $shop->id_shop) . '">' . $shop_name . '</a>. Jika tidak ada konfirmasi dalam waktu yang telah ditentukan, pembayaran akan ditransfer secara otomatis.</p>
												<p><b>RINCIAN PESANAN</b></p>
												<p>
		<pre>No. Pesanan:		<a>' . $invoice . '</a>
		Tanggal Pemesanan:	' . $tanggal . '</pre>
														' . $content_product . '
		<pre>Total Pesanan:		' . $pesanan . '
		Biaya Penanganan: 	' . $penanganan . '
		Ongkos Kirim:		' . $ongkir . '
		Total Pembayaran:	' . $total . '</pre>
														<p>Semoga kamu senang belanja di Elite Proxy.</p>
														</p>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td style="padding: 10px 15px 10px; background: #eee">
										<table style="width: 100%; font-size: 14px;" cellspacing="0" cellpadding="0" border="0">
											<tbody>
												<tr>
													<td style="padding-bottom: 10px"  colspan="5">
														For more information, please login to the
														<a href="' . env('APP_URL') . '">Eliteproxy.co.id</a>
													</td>
												</tr>
												<tr>
													<td  colspan="5" style="padding: 15px 0 5px">Best Regards,</td>
												</tr>
												<tr>
													<td  colspan="5"><small>Power Sistem Customer Service Team</small></td>
												</tr>
												<tr>
													<td><br>' . $content_pop . '</br></td>
												</tr>
												<tr align="center">
													<td colspan="5"><small>
													<a href="' . env('APP_URL') . ('info-contact-us') . '">Hubungi Kami</a> |
													<a href="' . env('APP_URL') . ('info-privacy-policy') . '">Kebijakan Privasi</a> |
													<a href="' . env('APP_URL') . ('info-term-and-condition') . '">Syarat Layanan</a>
													</small></td>
												</tr>
													<td colspan="5"><small>Ini adalah email otomatis. Mohon untuk tidak membalas email ini.
													Tambahkan info@powersistem.co.id pada daftar kontak untuk memastikan email dari Power Sistem masuk ke inbox-mu.</small>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
				</div>';

        $flag = $this->send($to, $subject, $content);
        if ($flag) {
            return response()->json([
                "status" => true,
                'flag' => $flag,
                "message" => "Email sent successfully"
            ]);
        }
        return response()->json([
            "status" => false,
            "message" => "Email sent failed"
        ]);
    }
    private function send($to, $subject, $content)
    {
        if (env('ENVIRONMENT') == 'development') {
            return "Email tidak dikirim Karena Develoment";
        }
        try {
            Mail::send([], [], function ($message) use ($to, $subject, $content) {
                $message->to($to)
                    ->subject($subject)
                    ->setBody($content, 'text/html');
            });
            return "Email berhasil dikirim ke $to";
        } catch (Swift_TransportException $e) {
            return "Gagal mengirim email: " . $e->getMessage();
        } catch (\Exception $e) {
            return "Terjadi kesalahan: " . $e->getMessage();
        }
    }

    public function finalChangePaymentStatus($id_cart, $data_update)
    {
        $status = strtolower($data_update['status']);
        $payment_detail = strtolower($data_update['payment_detail']);
        $curr_date = now(); 
        if ($status == 'expired') {
            $payment_status = 'cancel';
            DB::table('complete_cart_shop')
                ->where('id_cart', $id_cart)
                ->update(['status' => 'cancel_time_by_user']);
        }

        if ($status == 'complete_payment') {
            $payment_status = 'complete_payment';
            DB::table('complete_cart')
                ->where('id', $id_cart)
                ->update([
                    'tanggal_bayar' => $curr_date,
                    'status_pembayaran_top' => '1'
                ]);
        }

        DB::table('complete_cart')
            ->where('id', $id_cart)
            ->update([
                'status' => $payment_status,
                'payment_method' => 'midtrans',
                'payment_detail' => $payment_detail
            ]);

        return true;
    }
}
