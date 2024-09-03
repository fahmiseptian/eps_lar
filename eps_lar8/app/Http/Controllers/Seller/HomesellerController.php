<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Libraries\Notification;
use App\Models\User;
use App\Models\Product;
use App\Models\Shop;
use App\Models\CompleteCartShop;
use App\Models\CompleteCartShopDetail;
use App\Models\Operational;
use App\Models\Saldo;
use App\Models\Nego;
use App\Models\Products;
use Illuminate\Http\Request;

class HomesellerController extends Controller
{
    protected $user_id;
    protected $username;
    protected $seller;
    protected $data;
    protected $menu;
    protected $Model;
    protected $Libraries;

    public function __construct(Request $request)
    {
        $this->seller 	= $request->session()->get('seller_id');

        $sellerType     = Shop::getTypeById($this->seller);
        $saldoPending   = Saldo::calculatePendingSaldo($this->seller);
        $p_habis   = Products::countProductByIdShop($this->seller,['stock' => '0']);

        // Membuat $this->data
        $this->data['title'] = 'Dashboard';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldoPending;
        $this->data['product_habis'] = $p_habis;

        // Models
        $this->Model['CompleteCartShop']    = new CompleteCartShop();
        $this->Model['Products']            = new Products();
        $this->Model['Nego']                = new Nego();
        $this->Model['Saldo']               = new Saldo();
        $this->Model['ccsd']               = new CompleteCartShopDetail();

        // Libraries
        $this->Libraries['Notification'] = new Notification();

        // Get Notification
        $productReviews = $this->Libraries['Notification']->getProductReview($this->seller);
        $favorites = $this->Libraries['Notification']->getFavorite($this->seller);
        $orders = $this->Libraries['Notification']->getNotificationOrder($this->seller);
        $promos = $this->Libraries['Notification']->getNotificationPromo($this->seller);
        $vouchers = $this->Libraries['Notification']->getNotificationVoucher($this->seller);

        // Set data notification
        $this->data['notif_productReviews'] = $productReviews;
        $this->data['notif_favorites'] = $favorites;
        $this->data['notif_orders'] = $orders;
        $this->data['notif_promos'] = $promos;
        $this->data['notif_vouchers'] = $vouchers;

    }

    public function index(Request $request)
    {
        $neworder       =$this->Model['CompleteCartShop']->getCountOrderByIdshop($this->seller,'waiting_accept_order');
        $ondelivery     =$this->Model['CompleteCartShop']->getCountOrderByIdshop($this->seller,'on_packing_process');
        $order          = $this->Model['CompleteCartShop']->where('id_shop', $this->seller)->count();

        $jmlhproduct    = $this->Model['Products']->where('id_shop', $this->seller)->count();

        $newNego        = $this->Model['Nego']->JumlahNegoUlang($this->seller,['status_nego' ,'=', 0]);
        $NegoUlang      =$this->Model['Nego']->JumlahNegoUlang($this->seller,['status_nego' ,'>=', 2]);
        $jumlah_nego    = $this->Model['Nego']
                        ->where('id_shop', $this->seller)
                        ->where('status', '!=', 0)
                        ->where('complete_checkout',1)
                        ->count();

        $pendapatanHariIni  = $this->Model['Saldo']->getPendapatanHariIni($this->seller);
        $dataPendapatan     =$this->Model['Saldo']->getTotalPendapatanSeller($this->seller);

        $lastorder          =$this->Model['ccsd']->getLastProductByIdShop($this->seller);

        $this->data['neworder']     = $neworder;
        $this->data['ondelivery']   = $ondelivery;
        $this->data['order']        = $order;
        $this->data['lastOrder']        = $lastorder;

        $this->data['newNego']      = $newNego;
        $this->data['NegoUlang']    = $NegoUlang;
        $this->data['Nego']         = $jumlah_nego;

        $this->data['jmlhproduct']  = $jmlhproduct;

        $this->data['pendapatanHariIni']    = $pendapatanHariIni;
        $this->data['dataPendapatan']       = $dataPendapatan;

        // return view('seller.home.dashboard',$this->data);

        if ($request->attributes->get('isMobile')) {
            return view('seller.home.mobile-dashboard',$this->data);
        } else {
            return view('seller.home.dekstop-dashboard',$this->data);
            // return response()->json($this->data);
        }
    }

    public function notification(){
        return view('seller.home.notification',$this->data);
    }

    function test(){
        return response()->json($this->data);
    }


    public function testView(Request $request)
    {
        if ($request->attributes->get('isMobile')) {
            return view('test.mobile-index');
        } else {
            return view('test.dekstop-index');
        }
    }
}

