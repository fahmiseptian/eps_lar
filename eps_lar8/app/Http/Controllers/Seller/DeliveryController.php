<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use App\Models\Shop;
use App\Models\Saldo;
use App\Models\Courier;
use App\Models\Shop_courier;
use App\Models\Province;
use App\Models\FreeOngkir;
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
        $datacourier = Courier::where('status', 'Y')->get();

        foreach ($datacourier as $courier) {
            $is_checked = Shop_courier::where('id_shop', $this->seller)
                                    ->where('id_courier', $courier->id)
                                    ->exists();
            $courier->checked = $is_checked;
        }

        return view('seller.delivery.jasa-pengiriman', $this->data, ['datacourier'=>$datacourier]);
    }

    public function pengaturan_free()
    {
        $Province = Province::where('country_id', 1)->get();

        foreach ($Province as $active) {
            $is_check = FreeOngkir::where('id_shop', $this->seller)
                ->where('id_province', $active->province_id)
                ->exists();
            $active->checked = $is_check;
        }

        return view('seller.delivery.free-pengiriman', $this->data, ['Province' => $Province]);
    }



    public function addCourier(Request $request)
    {
        $courierId = $request->input('courierId');
        $shopId = $this->seller;
        $currentDateTime = Carbon::now();

        Shop_courier::create([
            'id_shop' => $shopId,
            'id_courier' => $courierId,
            'created_date' => $currentDateTime,
        ]);
        
        return response()->json(['success' => true]);
    }

    public function removeCourier(Request $request)
    {
        $courierId = $request->input('courierId');
        $shopId = $this->seller;

        Shop_courier::where('id_shop', $shopId)
            ->where('id_courier', $courierId)
            ->delete();

        return response()->json(['success' => true]);
    }

}