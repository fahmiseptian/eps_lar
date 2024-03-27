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

        $sellerType =Shop::where('id', $this->seller)
                    ->pluck('type')
                    ->first();
        $saldo =Saldo::where('id_shop', $this->seller)
                ->where('status', 'pending')
                ->sum('total_diterima_seller');
        // Membuat $this->data
        $this->data['title'] = 'Dashboard';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldo;
    }

    public function index()
    {
        $neworder = CompleteCartShop::where('id_shop', $this->seller)
                ->where('status', 'waiting_accept_order')
                ->count();
        $this->data['neworder'] = $neworder;
        $ondelivery = CompleteCartShop::where('id_shop', $this->seller)
                ->where('status', 'send_by_seller')
                ->count();
        $this->data['ondelivery'] = $ondelivery;
        $order = CompleteCartShop::where('id_shop', $this->seller)
                ->count();
        $this->data['order'] = $order;

        $ordercomplete = CompleteCartShop::where('id_shop', $this->seller)
                ->where('status', 'complete')
                ->count();
        if ($ordercomplete >0) {
            $persentaseorder = round(($ordercomplete / $order) * 100);
        }else{
            $persentaseorder=0;
        }
        $this->data['persentaseorder'] = $persentaseorder;
        
        $currentDay = strtolower(date('l')); 
        $dayMap = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 7
        ];
        $idDay = $dayMap[$currentDay];
        $Operational = Operational::where('id_shop', $this->seller)
                                    ->where('id_day', $idDay)
                                    ->first();
        if ($Operational) {
            $start_time = substr($Operational->start_time, 0, 5);
            $end_time = substr($Operational->end_time, 0, 5);
            if ($Operational->is_active == 'Y') {
                $this->data['Operational'] = 'Toko buka';
            } else {
                $this->data['Operational'] = 'Toko tutup';
            }
        } else {
            $start_time = null;
            $end_time = null;
            $this->data['Operational'] = 'Tidak ada data operasional untuk hari ini';
        }
        $this->data['start_time'] = $start_time;
        $this->data['end_time'] = $end_time;

        $jmlhproduct = Product::where('id_shop', $this->seller)->count();
        $this->data['jmlhproduct'] = $jmlhproduct;

        $nego = Nego::where('id_shop', $this->seller)
                ->where('status', 0)
                ->count();
        $this->data['nego'] = $nego;

        $negoSukses = Nego::where('id_shop', $this->seller)
                  ->where('status', 1)
                  ->count();
        $totalNego = Nego::where('id_shop', $this->seller)
                        ->count();
        if ($totalNego > 0) {
            $persentaseSukses = round(($negoSukses / $totalNego) * 100);
        } else {
            $persentaseSukses = 0;
        }
        $this->data['persentase_sukses'] = $persentaseSukses;



        return view('seller.home.dashboard',$this->data);
    }
}

