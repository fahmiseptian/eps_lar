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
        $this->data['title'] = 'Nego Pengadaan';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldoPending;

        // Model
        $this->Model['Shop'] = new Shop();
    }

    public function address()
    {
        $address    = $this->Model['Shop']->AddressByIdshop($this->seller);
        return view('seller.setting.address',$this->data,['address' => $address]);
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
        $delete = DB::table('member_address')->where('member_address_id', $id_address)->delete();

        if ($delete) {
            return response()->json(['message' => 'Address deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to delete address'], 500);
        }
    }


    function getAddress(Request $request) {
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
}
