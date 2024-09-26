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
use App\Models\ShopOperational;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    protected $seller;
    protected $data;
    protected $Model;

    public function __construct(Request $request)
    {
        $this->seller 	= $request->session()->get('seller_id');
        $sellerType     = Shop::getTypeById($this->seller);
        $saldoPending   = Saldo::calculatePendingSaldo($this->seller);

        // Membuat $this->data
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldoPending;

        // Model
        $this->Model['Shop'] = new Shop();
        $this->Model['ShopOperational'] = new ShopOperational();
    }

    public function index()
    {
        $operationals = $this->Model['ShopOperational']->getShopOperational($this->seller);
        return view('seller.setting.index',$this->data,['operationals'=>$operationals]);
    }

    function getOprasional() {
        $config_shop = DB::table('shop_config')->select('*')->where('id_shop',$this->seller)->first();
        $operationals = $this->Model['ShopOperational']->getShopOperational($this->seller);

        return response()->json(['operationals'=>$operationals,'config_shop'=>$config_shop]);
    }

    function updateConfig_cuti(Request $request) {
        $update = DB::table('shop_config')
        ->where('id_shop', $this->seller)
        ->update(['is_libur' => $request->is_active]);

        return response()->json(200);
    }

    public function address()
    {
        $address    = $this->Model['Shop']->AddressByIdshop($this->seller);
        return view('seller.setting.address',$this->data,['address' => $address]);
    }

    function getaddress() {
        $address    = $this->Model['Shop']->AddressByIdshop($this->seller);
        return response()->json($address);

    }

    function setDefaultAddress(Request $request) {
        $id_address = $request->id_address;
        $update     = $this->Model['Shop']->setDefaultAddress($this->seller,$id_address);

        if ($update) {
            return response()->json(['message' => 'Default address updated successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to update default address'], 500);
        }
    }

    function deleteAddress(Request $request) {
        $id_address = $request->id_address;
        $delete = DB::table('member_address')->where('member_address_id', $id_address)->update(['active_status'=>'inactive']);

        if ($delete) {
            return response()->json(['message' => 'Address deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to delete address'], 500);
        }
    }


    function getDetailAddress(Request $request) {
        $id_address = $request->id_address;
        $address    = $this->Model['Shop']->AddressByIdshop($this->seller,$id_address);
        return response()->json(['address' => $address], 200);
    }

    function addAddress(Request $request){
        $nama          = $request->name;
        $telepon       = $request->telp;
        $id_provinsi   = $request->provinsi;
        $id_kota       = $request->kota;
        $id_kecamatan  = $request->kecamatan;
        $kd_pos        = $request->kd_pos;
        $address       = $request->detail_address;
        $id_address    = $request->id_address;

        $id_user    = $this->Model['Shop']->getIdUserByid_shop($this->seller);

        $data          = [
            'member_id'=>$id_user,
            'address_name'=> $nama,
            'phone'=> $telepon,
            'province_id'=> $id_provinsi,
            'city_id'=> $id_kota,
            'subdistrict_id'=> $id_kecamatan,
            'address'=> $address,
            'postal_code'=> $kd_pos,
            'created_dt'=> Carbon::now(),
            'last_updated_dt'=> Carbon::now(),
        ];

        if ($id_address != null) {
            $DB = DB::table('member_address')->where('member_address_id',$id_address)->update($data);
        }else {
            $DB = DB::table('member_address')->insert($data);
        }

        if ($DB) {
            return response()->json(['message' => 'Menambahkan Alamat Berhasil'], 200);
        } else {
            return response()->json(['message' => 'Gagal Menambahkan Alamat'], 500);
        }
    }

    public function updateOprasional(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'operasional.*.id' => 'required|integer|exists:shop_operational,id',
            'operasional.*.day' => 'required|string',
            'operasional.*.is_active' => 'required|in:Y,N',
        ]);

        // Retrieve the data from the request
        $operasionalData = $request->input('operasional');

        // Loop through each record and update the database
        foreach ($operasionalData as $data) {
            $operasional = ShopOperational::find($data['id']);
            if ($operasional) {
                $operasional->start_time = $data['start_time'];
                $operasional->end_time = $data['end_time'];
                $operasional->is_active = $data['is_active'];
                $operasional->save();
            }
        }

        // Return a response
        return response()->json(['message' => 'Operasional updated successfully!'], 200);
    }
}
