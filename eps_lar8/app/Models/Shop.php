<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Libraries\Encryption;
use Illuminate\Support\Facades\DB;

class Shop extends Model
{
    use HasFactory;
    protected $table = 'shop';
    public $timestamps = false;
    protected $visible = ['nama_pt','name','nik_pemilik','npwp','phone','password', 'nama_pemilik','avatar'];
    protected $fillable = [
        'status','type','is_top','packing_estimation',
    ];

    protected $Encryption;

    protected static function booted()
    {
        static::retrieved(function ($model) {
            $model->Encryption = new Encryption();
        });
    }

    public static function getTypeById($id)
    {
        return self::where('id', $id)
                    ->pluck('type')
                    ->first();
    }

    public function decryptPassword($password)
    {
        if ($this->Encryption !== null) {
            $cek = $this->Encryption->decrypt($password);

            if ($cek === false) {
                return "Error";
            }

            return $cek;
        } else {
            return "Objek enkripsi tidak dikenali";
        }
    }

    public function getAddressByIdshop($id_shop) {
        return self::select(
            'shop.nama_pt',
            'shop.nama_pemilik',
            'shop.name',
            'shop.npwp',
            'mm.email',
            'ma.address',
            'ma.member_address_id',
            'ma.postal_code',
            'ma.address_name',
            'ma.phone',
            'p.province_name',
            'c.city_name',
            'ma.lat',
            'ma.lng',
            'mm.npwp_address',
            'c.jne_dest_id',
            's.subdistrict_name',
            'ma.province_id',
            'ma.city_id',
            's.sap_district_code',
            'ma.subdistrict_id',
            'ma.is_default_shipping',
            'ma.is_shop_address'
        )
        ->join('member_address as ma', 'shop.id_user', '=', 'ma.member_id')
        ->join('member as mm', 'mm.id', '=', 'ma.member_id')
        ->join('province as p', 'p.province_id', '=', 'ma.province_id')
        ->join('city as c', 'c.city_id', '=', 'ma.city_id')
        ->join('subdistrict as s', 's.subdistrict_id', '=', 'ma.subdistrict_id')
        ->where('shop.id', $id_shop)
        ->get();
    }

    public function  getShopById($id_shop) {
        $dataShop = DB::table('shop')
        ->join('member as m', 'shop.id_user', '=', 'm.id')
        ->join('member_address as ma', 'm.id', '=', 'ma.member_id')
        ->join('city as c', 'ma.city_id', '=', 'c.city_id')
        ->where('shop.id', $id_shop)
        ->select(
            'shop.*',
            'c.city_name'
            )
        ->first();

        return $dataShop;
    }

    function get_estimasiPacking($id_shop) {
        $packing = DB::table('shop')
        ->select(
            'packing_estimation'
        )
        ->where('id',$id_shop)
        ->first();
        return $packing;
    }

    function getShopCategory($id_shop) {
        $query = DB::table('shop')
        ->select('shop_category')
        ->where('id',$id_shop)
        ->first();
        return $query;
    }

    function getPinSaldo($id_shop) {
        $query = DB::table('shop')
        ->select('pin_saldo')
        ->where('id',$id_shop)
        ->first();

        $pin    = $query->pin_saldo;

        return $pin;
    }

}
