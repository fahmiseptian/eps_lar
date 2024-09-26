<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CompleteCartShop extends Model implements HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia;

    // protected $visible = ['status','delivery_start','id_courier','file_do','delivery_end'];

    protected $appends = ['file_pdf_url'];

    protected $hidden = ['media'];
    public $timestamps = false;
    protected $table = 'complete_cart_shop';
    protected $primaryKey = 'id';


    public function getFilePdfUrlAttribute()
    {
        $pdfMedia = $this->getFirstMedia('file_DO');
        if (!$pdfMedia) {
            // Jika tidak ada file PDF, kembalikan URL default atau pesan error
            return null; // Misalnya, return asset('path/to/default/pdf.png');
        } else {
            return $pdfMedia->getFullUrl();
        }
    }


    public function countAllorder($id_shop)
    {
        return self::where('id_shop', $id_shop)
            ->count();
    }

    public function get_count_order($id_shop, $status)
    {
        $query = self::where('id_shop', $id_shop)
            ->join('complete_cart as cc', 'cc.id', '=', 'complete_cart_shop.id_cart')
            ->where('complete_cart_shop.id_shop', $id_shop);

        if ($status == 'pending') {
            $query->where('cc.status', 'pending');
        } else {
            $query->where('cc.status', '!=', 'pending');
            if ($status == 'cancel_by_seller') {
                $query->where('cc.status', 'cancel');
            } else {
                $query->where('complete_cart_shop.status', $status);
            }
        }
        return  $query->count();
    }

    public function getCountOrderByIdshop($id_shop, $status)
    {
        return self::where('id_shop', $id_shop)
            ->where('status', $status)
            ->count();
    }

    public function getDetailOrderbyId($shopId, $id_cart_shop)
    {
        return self::select(
            'complete_cart_shop.*',
            'complete_cart_shop.id as id_cart_shop',
            'm.id as member_id',
            'm.email',
            'm.npwp',
            'm.instansi',
            'm.satker',
            'm.npwp_address',
            'ma.phone',
            'm.nama',
            'ma.address',
            'ma.address_name',
            'ma.postal_code',
            's.subdistrict_name',
            'p.province_name',
            'c.city_name as city',
            'cc.handling_cost',
            'cc.val_ppn',
            'cc.val_pph',
            'cc.jml_top',
            'cc.id_payment',
            'cc.va_number',
            'cc.sum_discount',
            'cc.invoice',
            'cc.val_ppn',
            'cc.status as invoice_status',
            'cc.created_date',
            'cc.status_pembayaran_top',
            'cc.due_date_payment as work_limit',
            'pm.name as pembayaran',
            'sp.deskripsi',
            'sp.id_courier',
            'sp.service',
            'sp.etd',
            'sp.price as price_ship',
            'sh.is_top',
            'sh.name as nama_seller',
            'sh.npwp as npwp_seller',
            'up.file_upload as bukti_transfer'
        )
            ->join('complete_cart as cc', 'cc.id', '=', 'complete_cart_shop.id_cart')
            ->join('complete_cart_address as cca', 'cca.id_cart', 'complete_cart_shop.id_cart')
            ->join('payment_method as pm', 'pm.id', '=', 'cc.id_payment')
            ->join('member as m', 'm.id', '=', 'cc.id_user')
            ->join('member_address as ma', 'cca.id_address', '=', 'ma.member_address_id')
            ->join('shipping as sp', 'sp.id', '=', 'complete_cart_shop.id_shipping')
            ->join('shop as sh', 'sh.id', '=', 'complete_cart_shop.id_shop')
            ->join('province as p', 'p.province_id', '=', 'ma.province_id')
            ->join('city as c', 'ma.city_id', '=', 'c.city_id')
            ->join('subdistrict as s', 's.subdistrict_id', '=', 'ma.subdistrict_id')
            ->leftjoin('upload_payment as up', 'up.invoice', 'cc.invoice')
            ->where('complete_cart_shop.id', '=', $id_cart_shop)
            ->where('complete_cart_shop.id_shop', $shopId)
            ->where('cca.id_billing_address', '!=', null)
            ->first();
    }


    public function getDetailProduct($shopId, $id_cart_shop)
    {
        return self::select(
            'ccsd.nama as nama_produk',
            'pi.image50 as gambar_produk',
            'b.name as nama_brand',
            'ccsd.price as harga_satuan_produk',
            'ccsd.total as harga_total_produk',
            'ccsd.qty as qty_produk',
            'ccsd.qty as qty_produk',
            'ccsd.price as base_price',
            'ccsd.*',
        )
            ->leftjoin('complete_cart_shop_detail as ccsd', 'ccsd.id_cart', '=', 'complete_cart_shop.id_cart')
            ->leftjoin('product_image as pi', 'pi.id_product', '=', 'ccsd.id_product')
            ->leftjoin('products as p', 'p.id', '=', 'ccsd.id_product')
            ->leftjoin('brand as b', 'b.id', 'p.id_brand')
            ->where('pi.is_default', '=', 'yes')
            ->where('complete_cart_shop.id', '=', $id_cart_shop)
            ->where('ccsd.id_shop', $shopId)
            ->get();
    }

    function getProductbytrax($id_ccs, $id_shop)
    {
        $query = DB::table('complete_cart_shop as ccs')
            ->join('complete_cart_shop_detail as ccsd', 'ccsd.id_cart', '=', 'ccs.id_cart') // Pastikan join ini benar
            ->where('ccs.id', '=', $id_ccs)
            ->where('ccsd.id_shop', '=', $id_shop)
            ->select('ccsd.*') // Memilih semua kolom dari complete_cart_shop_detail
            ->get();

        return $query;
    }

    public function getorderbyidcartshop($shopId, $id_cart_shop)
    {
        return self::select(
            'complete_cart_shop.*',
            'sp.id_courier',
        )
            ->join('shipping as sp', 'sp.id', '=', 'complete_cart_shop.id_shipping')
            ->where('complete_cart_shop.id', '=', $id_cart_shop)
            ->where('complete_cart_shop.id_shop', $shopId)
            ->first();
    }

    function getTrackforfreeshipping($id_cart_shop)
    {
        $query = DB::table('complete_cart_shop as ccs')
            ->select(
                'ccs.file_do',
                'ccs.delivery_start',
                'ccs.delivery_end',
                'ccs.status',
                'sp.id_courier',
                'ccs.no_resi'
            )
            ->join('shipping as sp', 'sp.id', '=', 'ccs.id_shipping')
            ->where('ccs.id', '=', $id_cart_shop)
            ->first();

        return $query;
    }


    public function filterorder($id_shop, $data)
    {
        $orders = DB::table('complete_cart_shop as ccs')
            ->select(
                'ccs.id',
                'cc.invoice',
                'cc.status_pembayaran_top',
                'cc.created_date',
                'm.instansi as member_instansi',
                'c.city_name as city',
                'ccs.total',
                'ccs.qty',
                'ccs.status',
            )
            ->join('complete_cart as cc', 'ccs.id_cart', '=', 'cc.id')
            ->join('complete_cart_address as cca', 'ccs.id_cart', '=', 'cca.id_cart')
            ->join('member as m', 'cc.id_user', '=', 'm.id')
            ->join('member_address as ma', 'm.id', '=', 'ma.member_id')
            ->join('city as c', 'cca.city_id', '=', 'c.city_id')
            ->where('ccs.id_shop', '=', $id_shop)
            ->where('cca.id_billing_address', '!=', null)
            ->where($data)
            ->groupBy(
                'ccs.id',
                'cc.invoice',
                'cc.status_pembayaran_top',
                'cc.created_date',
                'm.instansi',
                'c.city_name',
                'ccs.total',
                'ccs.qty',
                'ccs.status',
            )
            ->orderBy('ccs.id', 'desc')
            ->get();

        return $orders;
    }


    function getorderbyIdCart($idcart)
    {
        $orders = DB::table('complete_cart_shop as ccs')
            ->select(
                's.nama_pt',
                's.npwp',
                'ccs.id',
                'ccs.id_shop',
                'ccs.keperluan',
                'ccs.pesan_seller',
                'ccs.sum_shipping',
                'ccs.insurance_nominal',
                'ccs.handling_cost_non_ppn',
                'ccs.ppn_price',
                'ccs.ppn_shipping',
                'ccs.total',
                'ccs.discount',
                'ccs.status as status_dari_toko',
                'sh.service',
                'sh.deskripsi',
                'sh.etd'
            )
            ->where('id_cart', $idcart)
            ->leftJoin('shipping as sh', 'sh.id', '=', 'ccs.id_shipping')
            ->leftJoin('shop as s', 'ccs.id_shop', '=', 's.id')
            ->get();
        return $orders;
    }


    public function receiveOrder($order_id, $id_cart_shop)
    {
        $top_idp = ["23", "24", "25"];
        $date = Carbon::createFromFormat('Y-m-d', '2024-06-07');


        // Mengambil data cart shop yang sesuai
        $data = DB::table('complete_cart_shop as ccs')
            ->select('ccs.id as id_cart_shop', 'ccs.id_cart', 'ccs.id_shop', 'ccs.total', 'ccs.status', 'cc.id_user', 'cc.invoice', 'cc.id_payment')
            ->join('complete_cart as cc', 'cc.id', '=', 'ccs.id_cart')
            ->where('ccs.id', $id_cart_shop)
            ->where('ccs.status', '!=', 'cancel')
            ->get();

        foreach ($data as $d) {
            // Mengupdate status menjadi complete
            DB::table('complete_cart_shop')
                ->where('id', $d->id_cart_shop)
                ->update([
                    'status' => 'complete',
                    'delivery_end' => $date
                ]);

            $total_diterima_seller = $this->getTotalHargaInput($d->id_cart, $d->id_shop);

            $array_data = [
                'id_cart' => $d->id_cart,
                'id_shop' => $d->id_shop,
                'id_user' => $d->id_user,
                'note'    => ' ',
                'invoice' => $d->invoice,
                'total' => $d->total,
                'total_diterima_seller' => $total_diterima_seller,
            ];
            $insert_revenue = $this->insertRevenue($array_data);

            $count_sold = $this->countSold($d->id_cart);
        }

        // Mengecek jumlah data cart shop dan jumlah yang telah complete
        $count_data_cart_shop = DB::table('complete_cart')
            ->where('id', $order_id)
            ->count();

        $count_complete_cart_shop = DB::table('complete_cart_shop')
            ->where('id_cart', $order_id)
            ->where('status', 'complete')
            ->count();

        if ($count_data_cart_shop == $count_complete_cart_shop) {
            DB::table('complete_cart')
                ->where('id', $order_id)
                ->update(['status' => 'completed']);
        }

        return true;
    }

    private function insertRevenue($data)
    {
        $insert = DB::table('revenue')->insertGetId($data);
        return $insert;
    }

    public function countSold($id_cart)
    {
        $products = DB::table('complete_cart_shop_detail')
            ->select('id_product')
            ->where('id_cart', $id_cart)
            ->get();

        foreach ($products as $product) {
            $this->_countSold($product->id_product);
        }

        return true;
    }

    private function _countSold($id_product)
    {
        DB::table('product')
            ->where('id', $id_product)
            ->increment('count_sold', 1);

        return true;
    }

    public function getTotalHargaInput($id_cart, $id_shop)
    {
        $data = DB::table('complete_cart_shop_detail')
            ->select('input_price', 'qty')
            ->where('id_cart', $id_cart)
            ->where('id_shop', $id_shop)
            ->get();

        $total = 0;
        foreach ($data as $ds) {
            $total += $ds->input_price * $ds->qty;
        }

        return $total;
    }

    public function setRestOrder($id, $id_shop, $data)
    {
        $updated = DB::table('complete_cart_shop')
            ->where('id', $id)
            ->where('id_shop', $id_shop)
            ->update($data);

        if ($updated) {
            return true;
        } else {
            return false; // Atau sesuaikan dengan kebutuhan penanganan kesalahan
        }
    }

    function getUserById_cart_shop($id_cart_shop)
    {
        $query  = DB::table('complete_cart_shop as ccs')
            ->select(
                'm.instansi',
                'm.satker',
                'm.npwp',
                'ma.*',
                'p.province_name',
                'c.city_name as city',
                'sub.subdistrict_name',
            )
            ->join('complete_cart as cc', 'cc.id', 'ccs.id_cart')
            ->join('member as m', 'm.id', 'cc.id_user')
            ->join('member_address as ma', 'ma.member_id', 'm.id')
            ->join('province as p', 'p.province_id', '=', 'ma.province_id')
            ->join('city as c', 'c.city_id', '=', 'ma.city_id')
            ->join('subdistrict as sub', 'sub.subdistrict_id', '=', 'ma.subdistrict_id')
            ->where('ccs.id', $id_cart_shop)
            ->where('ma.is_default_billing', 'yes')
            ->first();

        return $query;
    }

    function getaddressUser($id_cart_shop)
    {
        $query  = DB::table('complete_cart_shop as ccs')
            ->select(
                'm.instansi',
                'm.satker',
                'm.npwp',
                'ma.*',
                'p.province_name',
                'c.city_name as city',
                'sub.subdistrict_name',
            )
            ->join('complete_cart as cc', 'cc.id', 'ccs.id_cart')
            ->join('member as m', 'm.id', 'cc.id_user')
            ->join('member_address as ma', 'ma.member_address_id', 'cc.id_address_user')
            ->join('province as p', 'p.province_id', '=', 'ma.province_id')
            ->join('city as c', 'c.city_id', '=', 'ma.city_id')
            ->join('subdistrict as sub', 'sub.subdistrict_id', '=', 'ma.subdistrict_id')
            ->where('ccs.id', $id_cart_shop)
            ->first();

        return $query;
    }

    function getSellerById_cart_shop($id_cart_shop)
    {
        $query  = DB::table('complete_cart_shop as ccs')
            ->select(
                's.nama_pt',
                's.npwp',
                'ma.*',
                'p.province_name',
                'c.city_name',
                'sub.subdistrict_name',
            )
            ->join('shop as s', 's.id', 'ccs.id_shop')
            ->join('member as m', 'm.id', 's.id_user')
            ->join('member_address as ma', 'ma.member_id', 'm.id')
            ->join('province as p', 'p.province_id', '=', 'ma.province_id')
            ->join('city as c', 'c.city_id', '=', 'ma.city_id')
            ->join('subdistrict as sub', 'sub.subdistrict_id', '=', 'ma.subdistrict_id')
            ->where('ccs.id', $id_cart_shop)
            ->where('ma.is_shop_address', 'yes')
            ->first();

        return $query;
    }

    function GetIdSellerAndId_memeber($id_cart_shop)
    {
        $query = DB::table('complete_cart_shop as ccs')
            ->select(
                'ccs.id_shop',
                'cc.id_user'
            )
            ->join('complete_cart as cc', 'cc.id', 'ccs.id_cart')
            ->where('ccs.id', $id_cart_shop)
            ->first();
        return $query;
    }

    function getSummaryCompleteCartShop($where = null)
    {
        // Daftar status yang diinginkan
        $statuses = [
            'complete',
            'waiting_accept_order',
            'on_packing_process',
            'send_by_seller',
            'refund',
            'cancel_manual_by_user',
            'cancel_time_by_user'
        ];

        // Membuat query menggunakan DB::table
        $query = DB::table('complete_cart_shop')
            ->select(DB::raw('SUM(total) as grandtotal, COUNT(id) as count'))
            ->whereIn('status', $statuses);

        // Menambahkan kondisi where tambahan jika diberikan
        if ($where !== null) {
            $query->where($where);
        }

        // Mendapatkan hasil
        $data = $query->first();

        return $data;
    }

    public function getCountViewShop($id_shop)
    {
        $query = DB::table('log_view_shop')
            ->where('id_shop', $id_shop);

        $data = $query->get();

        return $data->count();
    }

    public function getCountViewProduct($id_shop)
    {
        // Menyusun query
        $query = DB::table('log_last_view as llv')
            ->select(DB::raw('SUM(llv.count_view) as cv'))
            ->join('products as p', 'p.id', '=', 'llv.id_product')
            ->where('p.id_shop', $id_shop);

        $data = $query->first();
        return $data->cv;
    }
}
