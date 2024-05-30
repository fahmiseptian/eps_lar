<?php

namespace App\Models;

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
            $pdfMedia = $this->getFirstMedia('file_DO'); // Ganti 'pdfs' dengan nama koleksi Anda
            if (!$pdfMedia) {
                // Jika tidak ada file PDF, kembalikan URL default atau pesan error
                return null; // Misalnya, return asset('path/to/default/pdf.png');
            } else {
                return $pdfMedia->getFullUrl();
            }
        }


    public function countAllorder($id_shop){
        return self::where('id_shop', $id_shop)
            ->count();
    }

    public function get_count_order($id_shop, $status) {
        $query = self::where('id_shop', $id_shop)
                    ->join('complete_cart as cc', 'cc.id', '=', 'complete_cart_shop.id_cart')
                    ->where('complete_cart_shop.id_shop', $id_shop);

        if ($status == 'pending') {
            $query->where('cc.status', 'pending');
        } else {
            $query->where('cc.status', '!=', 'pending');
            if ($status == 'cancel_by_seller') {
                $query->where('cc.status','cancel');
            } else {
                $query->where('complete_cart_shop.status', $status);
            }
        }
        return  $query->count();
    }

    public function getCountOrderByIdshop($id_shop, $status) {
        return self::where('id_shop', $id_shop)
                ->where('status', $status)
                ->count();
    }

    public function getDetailOrderbyId($shopId, $id_cart_shop) {
        return self::select(
            'complete_cart_shop.*',
            'complete_cart_shop.id as id_cart_shop',
            'm.id',
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
            'cc.sum_discount',
            'cc.invoice',
            'cc.val_ppn',
            'cc.status as invoice_status',
            'cc.created_date',
            'cc.status_pembayaran_top',
            'pm.name as pembayaran',
            'sp.deskripsi',
            'sp.id_courier',
            'sp.service',
            'sp.etd',
            'sp.price as price_ship',
            'sh.is_top',
            'sh.name as nama_seller'
        )
        ->join('complete_cart as cc', 'cc.id', '=', 'complete_cart_shop.id_cart')
        ->join('payment_method as pm', 'pm.id', '=', 'cc.id_payment')
        ->join('member as m', 'm.id', '=', 'cc.id_user')
        ->join('member_address as ma', 'm.id', '=', 'ma.member_id')
        ->join('shipping as sp', 'sp.id', '=', 'complete_cart_shop.id_shipping')
        ->join('shop as sh', 'sh.id', '=', 'complete_cart_shop.id_shop')
        ->join('province as p', 'p.province_id', '=', 'ma.province_id')
        ->join('city as c', 'ma.city_id', '=', 'c.city_id')
        ->join('subdistrict as s', 's.subdistrict_id', '=', 'ma.subdistrict_id')
        ->where('complete_cart_shop.id', '=', $id_cart_shop)
        ->where('complete_cart_shop.id_shop', $shopId)
        ->first();
    }
    

    public function getDetailProduct($shopId,$id_cart_shop){
        return self::select(
            'ccsd.nama as nama_produk',
            'pi.image50 as gambar_produk',
            'ccsd.price as harga_satuan_produk',
            'ccsd.total as harga_total_produk',
            'ccsd.qty as qty_produk',
            'ccsd.qty as qty_produk',
            'ccsd.price as base_price',
            'ccsd.*',
        )
        ->join('complete_cart_shop_detail as ccsd', 'ccsd.id_cart', '=', 'complete_cart_shop.id_cart')
        ->join('product_image as pi', 'pi.id_product', '=', 'ccsd.id_product')
        ->where('pi.is_default', '=', 'yes')
        ->where('complete_cart_shop.id', '=', $id_cart_shop)
        ->where('complete_cart_shop.id_shop', $shopId)
        ->get();
    }

    public function getorderbyidcartshop($shopId, $id_cart_shop) {
        return self::select(
            'complete_cart_shop.*',
            'sp.id_courier',
        )
        ->join('shipping as sp', 'sp.id', '=', 'complete_cart_shop.id_shipping')
        ->where('complete_cart_shop.id', '=', $id_cart_shop)
        ->where('complete_cart_shop.id_shop', $shopId)
        ->first();
    }
    

    public function filterorder($id_shop, $data) {
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
        ->where('cca.id_billing_address','!=',null)
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
        ->get();

        return $orders;
    }


    function getorderbyIdCart($idcart) {
        $orders = DB::table('complete_cart_shop as ccs')
                ->select(
                    's.nama_pt',
                    'ccs.id',
                    'ccs.id_shop'
                )
                ->where('id_cart',$idcart)
                ->join('shop as s','ccs.id_shop', '=','s.id')
                ->get();
        return $orders;
    }
}