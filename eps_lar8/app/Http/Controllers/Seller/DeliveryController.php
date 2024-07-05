<?php

namespace App\Http\Controllers\Seller;

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

        $sellerType     = Shop::getTypeById($this->seller);
        $saldoPending   = Saldo::calculatePendingSaldo($this->seller);

        // Membuat $this->data
        $this->data['title'] = 'Delivery';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldoPending;
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
        $Province = Province::where('country_id', 1)
        ->orderBy('province_name', 'asc')
        ->get();

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

    public function addfreeCourier(Request $request) {
        $id_province = $request->input('id_province');
        $shopId = $this->seller;
        $currentDateTime = Carbon::now();

        FreeOngkir::create([
            'id_shop' => $shopId,
            'id_province' => $id_province,
            'status' => 1,
            'datetime' => $currentDateTime,
        ]);

        return response()->json(['success' => true]);
    }

    public function removefreeCourier(Request $request)
    {
        $id_province = $request->input('id_province');
        $shopId = $this->seller;

        FreeOngkir::where('id_shop', $shopId)
            ->where('id_province', $id_province)
            ->delete();

        return response()->json(['success' => true]);
    }

    function get_packingDay(){
        $id_shop = $this->seller;
        $packing = Shop::get_estimasiPacking($id_shop);
        return $packing;
    }

    public function update_packingDay(Request $request)
    {
        $id_shop = $this->seller;
        $request->validate([
            'estimasi' => 'required|integer|min:1|max:7'
        ]);
        $shop = Shop::find($id_shop);
        if (!$shop) {
            return response()->json(['message' => 'Shop tidak ditemukan'], 404);
        }
        $shop->packing_estimation = $request->input('estimasi');
        $shop->save();
        return response()->json(['message' => 'Estimasi packing berhasil diperbarui'], 200);
    }

}
