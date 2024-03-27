<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Saldo;
use App\Models\Courier;
use Illuminate\Http\Request;

class DeliveryController extends Controller
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
        $this->data['title'] = 'Delivery';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldo;
    }

    public function pengaturan_jasa()
    {
        $datacourier = Courier::where('status', 'Y')
                        ->get();

        return view('seller.delivery.jasa-pengiriman',$this->data, ['datacourier'=>$datacourier]);
    }
    
    public function pengaturan_free()
    {
        return view('seller.delivery.free-pengiriman',$this->data);
    }

}