<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Invoice extends Model implements HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia;

    protected $table = 'complete_cart';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $visible = ['invoice', 'id_cart'];

    public function finance()
    {
        return $this->belongsTo(User::class, 'updated_status_by');
    }

    public function pajak()
    {
        return $this->belongsTo(User::class, 'pelapor_pajak');
    }

    public function completeCartShop()
    {
        return $this->hasOne(CompleteCartShop::class, 'id_cart', 'id');
    }

    function CountPesananUserbyIduser($id_user, $status)
    {
        $query = DB::table('complete_cart')
            ->select(
                'id',
                'invoice',
                'id_user',
                'id_payment',
                'payment_method',
                'payment_detail',
                'status',
                'status_pembayaran_top',
                'status_pelaporan_pajak',
                'note',
                'created_date',
                'due_date_payment',
                'last_update',
                'updated_status_by'
            );

        switch ($status) {
            case 'baru':
                $query->where(function ($q) {
                    $q->where('status', 'pending')
                        ->orWhere('status', 'waiting_approve_by_ppk');
                });
                break;
            case 'belumbayar':
                $query->where('status', 'pending');
                break;
            case 'pengiriman':
                $query->where('status', 'on_delivery');
                break;
            case 'selesai':
                $query->where('status', 'completed');
                break;
            case 'batal':
                $query->where(function ($q) {
                    $q->where('status', 'expired')
                        ->orWhere('status', 'cancel')
                        ->orWhere('status', 'cancel_part');
                });
                break;
            default:
                return 0;
        }
        $query->where('id_user', $id_user);
        return $query->count();
    }

    function getOrderByIdmember($idmember, $kondisi)
    {
        $query = DB::table('complete_cart as cc')
            ->select(
                'cc.id as id_transaksi',
                'cc.invoice',
                'cc.total',
                'cc.id_payment',
                'cc.created_date as pembuatan_pesanan',
                'cc.jml_top',
                'cc.status_pembayaran_top as status_pembayaran',
                'cc.qty as jmlh_qty',
                'up.file_upload'
            )
            ->join('complete_cart_shop as ccs', 'cc.id', '=', 'ccs.id_cart')
            ->leftJoin('upload_payment as up', 'up.invoice', '=', 'cc.invoice')
            ->where('cc.id_user', $idmember);
        if ($kondisi != null) {
            $query->where('cc.status', $kondisi);
        }
        $query->orderBy('cc.created_date', 'desc')
            ->groupBy('cc.id', 'cc.invoice', 'cc.total', 'cc.id_payment', 'cc.created_date', 'cc.jml_top', 'cc.status_pembayaran_top', 'cc.qty', 'up.file_upload');

        return $query->paginate(7);
    }

    function getOrder($id_cart)
    {
        $order = DB::table('complete_cart as cc')
            ->select(
                'cc.id as id_cart',
                'cc.invoice',
                'cc.status_pembayaran_top',
                'cc.jml_top',
                'cc.created_date',
                'cc.total',
                'up.file_upload',
                'pm.name as pembayaran',
                'pm.id as id_pembayaran'
            )
            // ->join('complete_cart_shop as ccs', 'cc.id', '=', 'ccs.id_cart')
            ->leftJoin('upload_payment as up', 'up.invoice', '=', 'cc.invoice')
            ->leftJoin('payment_method as pm', 'pm.id', '=', 'cc.id_payment')
            ->where('cc.id', $id_cart)
            ->first();


        return $order;
    }

    function migrate_cart_checkout_cond($id_user, $id_cart, $is_approval = false)
    {
        $carts = new Cart();
        $curr_date = date('Y-m-d H:i:s');
        $data_user = Member::getDataMember($id_user);

        if (!empty($id_cart)) {
            if ($data_user) {
                $id_instansi = $data_user->id_instansi;
                $id_satker = $data_user->id_satker;
                $id_bidang = $data_user->id_bidang;
            }

            $data         = array('id_user' => $id_user, 'id' => $id_cart);
            $migrate_checkout = $carts->migrate_checkout($data);

            if ($is_approval) {
                // NOTE need approval by PPK
                $status = 'waiting_approve_by_ppk';

                DB::table('complete_cart_shop')
                    ->where('id_cart', $id_cart)
                    ->update([
                        'status' => $status,
                        'last_update' => $curr_date,
                    ]);

                DB::table('complete_cart')
                    ->where('id', $id_cart)
                    ->update([
                        'status' => $status,
                        'last_update' => $curr_date,
                    ]);

                $dataSave = [
                    'id_cart' => $id_cart,
                    'id_member' => $id_user,
                    'id_instansi' => $id_instansi,
                    'id_satker' => $id_satker,
                    'id_bidang' => $id_bidang,
                    'status_approve' => 0,
                    'approved_by' => null,
                    'created_user' => $id_user,
                    'created_date' => $curr_date,
                ];
            } else {
                $dataSave = [
                    'id_cart' => $id_cart,
                    'id_member' => $id_user,
                    'id_instansi' => $id_instansi,
                    'id_satker' => $id_satker,
                    'id_bidang' => $id_bidang,
                    'status_approve' => 1,
                    'approved_by' => $id_user,
                    'created_user' => $id_user,
                    'created_date' => $curr_date,
                ];
            }


            $check_data = DB::table('tr_approval_cart')
                ->select('id')
                ->where('id_cart', $id_cart)
                ->first();
            if (!empty($check_data)) {
                // NOTE Update data approval
                $id_tr_approval = $check_data->id;
                $save = DB::table('tr_approval_cart')
                    ->where('id', $id_tr_approval)
                    ->update($dataSave);
            } else {
                // NOTE Save data approval
                $save = DB::table('tr_approval_cart')->insert($dataSave);
            }

            return $save;
        }
        return false;
    }

    public function getDataInvoice($id_cart)
    {
        $invoice = DB::table('complete_cart')
            ->select('complete_cart.id', 'invoice', 'total', 'm.nama', 'm.email', 'm.no_hp', 'm.instansi', 'm.satker', 'status_pembayaran_top')
            ->leftJoin('member as m', 'm.id', '=', 'complete_cart.id_user')
            ->where('complete_cart.id', $id_cart)
            ->first();

        return $invoice;
    }

    function getTransaction($id_user)
    {
        $transactions = DB::table('complete_cart as a')
            ->select('a.id', 'a.invoice', 'b.status', 'a.total', 'a.created_date', 'b.id as id_cart_shop', 'b.qty',  'a.status_pembayaran_top as payment', 's.name as nama_pt', 's.id as id_shop', 'a.status as status_invoice', 'due_date_payment_top as batas_pembayaran_top', 'status_pembayaran_top as status_pembayaran', 'due_date_payment as batas_pembayaran', 'jml_top', 'm.nama', 'up.file_upload as bukti_transfer')
            ->join('complete_cart_shop as b', 'b.id_cart', '=', 'a.id')
            ->leftJoin('shop as s', 's.id', '=', 'b.id_shop')
            ->leftjoin('upload_payment as up', 'up.invoice', 'a.invoice')
            ->leftJoin('member as m', 'm.id', '=', 'a.id_user')
            ->where('a.id_user', $id_user)
            ->orderBy('a.created_date', 'desc')
            ->get();

        return $transactions;
    }

    public function getCompleteOrder($id_cart, $id_shop = null)
    {
        $query = DB::table('complete_cart as cc')
            ->select('cc.*', 'm.nama', 'm.email', 'm.no_hp', 'm.username', 'ma.address as shipping_address', 'ap.payload')
            ->leftJoin('api_log_report as ap', 'ap.id_cart', '=', 'cc.id')
            ->leftJoin('member as m', 'm.id', '=', 'cc.id_user')
            ->leftJoin('member_address as ma', 'ma.member_address_id', '=', 'cc.id_address_user')
            ->where('cc.id', $id_cart)
            ->get();

        $cart = [];

        foreach ($query as $data) {
            $cart[$data->id] = $data;
            $cart[$data->id]->shop = $this->getOrderShop($data->id, $data->id_address_user, $id_shop);
        }

        return $cart;
    }

    public function getOrderShop($id_cart, $id_address_user, $id_shop)
    {
        $query = DB::table('complete_cart_shop as cs')
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

        $dataShop = [];

        foreach ($query as $data) {
            $dataShop[$data->id] = $data;
            $dataShop[$data->id]->address = $this->getAddress($id_address_user);
            if ($id_shop == null) {
                $dataShop[$data->id]->detail = $this->getOrderShopDetail($id_cart, $data->id_shop);
            } else {
                $dataShop[$data->id]->detail = $this->getOrderShopDetail($id_cart, $id_shop);
            }
        }

        return $dataShop;
    }

    public function getAddress($id_address)
    {
        $result = DB::table('member_address as ma')
            ->select('address', 'member_address_id', 'postal_code', 'address_name', 'phone', 'province_name', 'city_name', 'subdistrict_name', 'ma.province_id', 'ma.city_id', 'ma.subdistrict_id', 'is_default_shipping', 'is_shop_address')
            ->leftJoin('province as p', 'p.province_id', '=', 'ma.province_id')
            ->leftJoin('city as c', 'c.city_id', '=', 'ma.city_id')
            ->leftJoin('subdistrict as s', 's.subdistrict_id', '=', 'ma.subdistrict_id')
            ->where('ma.active_status', 'active')
            ->where('ma.member_address_id', $id_address)
            ->get();

        return $result;
    }

    public function getOrderShopDetail($id_cart, $id_shop)
    {
        $data_detail = DB::table('complete_cart_shop_detail as csd')
            ->select(
                'csd.*',
                'csd.id as id_temp',
                'csd.price as base_price',
                'p.*',
                DB::raw('(SELECT image50 FROM product_image WHERE id_product = csd.id_product AND is_default = "yes") as image50'),
                DB::raw('(SELECT image800 FROM product_image WHERE id_product = csd.id_product AND is_default = "yes") as image800')
            )
            ->leftJoin('products as p', 'p.id', '=', 'csd.id_product')
            ->where('id_cart', $id_cart)
            ->where('csd.id_shop', $id_shop)
            ->get();

        return $data_detail;
    }

    public function getPopularSearch()
    {
        $query = DB::table('log_search as ls')
            ->select(
                'ls.*',
                'p.id',
                'p.id_shop',
                'p.seoname',
                DB::raw('(SELECT image800 FROM product_image WHERE id_product = p.id AND is_default = "yes" LIMIT 1) as default_image')
            )
            ->leftJoin('product as p', 'p.id', '=', 'ls.first_product')
            ->where('first_product', '>', '0')
            ->orderBy('search_count', 'desc')
            ->limit(5)
            ->get();

        return $query;
    }
}
