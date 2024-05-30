<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Shop;
use App\Models\CompleteCartShop;
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
    }

    public function index()
    {
        $neworder =CompleteCartShop::getCountOrderByIdshop($this->seller,'waiting_accept_order');
        $ondelivery =CompleteCartShop::getCountOrderByIdshop($this->seller,'on_packing_process');
        $order = CompleteCartShop::where('id_shop', $this->seller)->count();
        $jmlhproduct = Products::where('id_shop', $this->seller)->count();
        $newNego = Nego::JumlahNegoUlang($this->seller,['status_nego' ,'=', 0]);
        $NegoUlang = Nego::JumlahNegoUlang($this->seller,['status_nego' ,'>=', 2]);
        $jumlah_nego = Nego::where('id_shop', $this->seller)
                  ->where('status', '!=', 0)
                  ->where('complete_checkout',1) 
                  ->count();
        $pendapatanHariIni = Saldo::getPendapatanHariIni($this->seller);
        
        $this->data['neworder'] = $neworder;
        $this->data['ondelivery'] = $ondelivery;
        $this->data['order'] = $order;
        $this->data['newNego'] = $newNego;
        $this->data['NegoUlang'] = $NegoUlang;
        $this->data['Nego'] = $jumlah_nego;
        $this->data['jmlhproduct'] = $jmlhproduct;
        $this->data['pendapatanHariIni'] = $pendapatanHariIni;

        return view('seller.home.dashboard',$this->data);
    }
}

