<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Saldo;
use App\Models\CompleteCartShop;
use App\Models\CompleteCartAddress;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrederController extends Controller
{
    protected $seller;
    protected $getOrder;
    protected $data;

    public function __construct(Request $request)
    {
        $this->seller 	= $request->session()->get('seller_id');

        $this->getOrder =DB::table('complete_cart_shop as ccs')
                    ->select('ccs.id', 'cc.invoice', 'cc.status_pembayaran_top','cc.created_date', 'm.instansi as member_instansi', 'c.city_name as city', 'ccs.total', 'ccs.qty','ccs.status')
                    ->join('complete_cart as cc', 'ccs.id_cart', '=', 'cc.id')
                    ->join('member as m', 'cc.id_user', '=', 'm.id')
                    ->join('member_address as ma', 'm.id', '=', 'ma.member_id')
                    ->join('city as c', 'ma.city_id', '=', 'c.city_id');

        $sellerType =Shop::where('id', $this->seller)
                    ->pluck('type')
                    ->first();

        $saldo =Saldo::where('id_shop', $this->seller)
                ->where('status', 'pending')
                ->sum('total_diterima_seller');

        $this->data['title'] = 'Order';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldo;
    }

    public function index()
    {
        $id_seller=$this->seller;
        $orders= $this->getOrder
                ->where('ccs.id_shop', '=', $id_seller)
                ->orderBy('cc.invoice', 'desc')
                ->get();

        return view('seller.order.index',$this->data,['orders' => $orders]);
    }

    public function filterOrder($status_order){
        $status = $status_order;
        $shopId = $this->seller;

        if ($status=='done') {
            $filterorders = $this->getOrder
                ->where('ccs.id_shop', '=', $shopId)
                ->where('ccs.status','=','complete')
                ->where('cc.status_pembayaran_top','=',1)
                ->orderBy('cc.invoice', 'desc')
                ->get();
        }elseif($status=='complete'){
            $filterorders = $this->getOrder
                ->where('ccs.id_shop', '=', $shopId)
                ->where('ccs.status','=','complete')
                ->where('cc.status_pembayaran_top','=',0)
                ->orderBy('cc.invoice', 'desc')
                ->get();
        }else {
            $filterorders = $this->getOrder
                ->where('ccs.id_shop', '=', $shopId)
                ->where('ccs.status','=',$status)
                ->orderBy('cc.invoice', 'desc')
                ->get();
        }
        return view('seller.order.index',$this->data,['orders' => $filterorders]);
    }

    public function detailOrder($id_cart_shop){
        $shopId = $this->seller;
        $detailOrder=DB::table('complete_cart_shop as ccs')
        ->select(
            'ccs.*',
            'ccs.id as id_cart_shop',
            'm.id', 
            'm.email', 
            'm.npwp', 
            'ma.phone', 
            'm.nama', 
            'ma.address', 
            'ma.address_name',
            'ma.postal_code', 
            's.subdistrict_name',
            'p.province_name', 
            'c.city_name as city', 
            'cc.val_ppn',
            'cc.val_pph',
            'cc.jml_top',
            'cc.sum_discount',
            'cc.invoice',
            'cc.created_date',
            'cc.status_pembayaran_top',
            'pm.name as pembayaran',
            'sp.deskripsi',
            'sp.id_courier', 
            'sp.service', 
            'sp.etd', 
            'sp.price as price_ship',
            'sh.is_top',
        )
        ->join('complete_cart as cc', 'cc.id', '=', 'ccs.id_cart')
        ->join('payment_method as pm', 'pm.id', '=', 'cc.id_payment')
        ->join('member as m', 'm.id', '=', 'cc.id_user')
        ->join('member_address as ma', 'm.id', '=', 'ma.member_id')
        ->join('shipping as sp', 'sp.id', '=', 'ccs.id_shipping')
        ->join('shop as sh', 'sh.id','=', 'ccs.id_shop')
        ->join('province as p', 'p.province_id', '=', 'ma.province_id')
        ->join('city as c', 'ma.city_id', '=', 'c.city_id')
        ->join('subdistrict as s', 's.subdistrict_id', '=', 'ma.subdistrict_id')
        ->where('ccs.id', '=', $id_cart_shop)
        ->where('ccs.id_shop', $shopId)
        ->first();

        $detailProductorder= DB::table('complete_cart_shop as ccs')
        ->select(
            'ccsd.nama as nama_produk',
            'pi.image50 as gambar_produk',
            'ccsd.price as harga_satuan_produk',
            'ccsd.total as harga_total_produk',
            'ccsd.qty as qty_produk',
        )
        ->join('complete_cart_shop_detail as ccsd', 'ccsd.id_cart', '=', 'ccs.id_cart')
        ->join('product_image as pi', 'pi.id_product', '=', 'ccsd.id_product')
        ->where('pi.is_default', '=', 'yes')
        ->where('ccs.id', '=', $id_cart_shop)
        ->where('ccs.id_shop', $shopId)
        ->get();


        return view('seller.order.detail',$this->data,['detailOrder'=>$detailOrder, 'detailProductOrder'=>$detailProductorder]);
    }

    public function acceptOrder(Request $request) {
        $id_cart_shop = $request->input('id_cart_shop');
        $estimation_packing=Shop::where('id', $this->seller)
                        ->pluck('packing_estimation')
                        ->first();
        $order = CompleteCartShop::find($id_cart_shop);
        $current_date= date('Y-m-d H:i:s');
        $due_date_packing = date('Y-m-d H:i:s', strtotime($current_date . ' +' . $estimation_packing . ' day'));
        
        if ($order && $estimation_packing ) {
            $order->status = 'on_packing_process';
            $order->receive_date = $current_date;
            $order->due_date_packing = $due_date_packing; 
            $order->save();
            return "Pesanan berhasil diterima dan sedang diproses packing.";
        } else {
            return "Pesanan tidak ditemukan.";
        }
    }

    public function cancelOrder(Request $request) {
        $id_cart_shop = $request->input('id_cart_shop');
        $note = $request->input('note');

        $order = CompleteCartShop::find($id_cart_shop)->where('id_shop', $this->seller);
        $current_date= date('Y-m-d H:i:s');
        
        if ($order) {
            $order->status = 'cancel_by_seller';
            $order->note_seller =  $note;
            $order->receive_date = $current_date;
            $order->save();
            return "Pesanan berhasil diterima dan sedang diproses packing.";
        } else {
            return "Pesanan tidak ditemukan.";
        }
    }
    
}

