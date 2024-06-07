<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompleteCartAddress extends Model
{
    protected $table = 'complete_cart_address';
    protected $primaryKey = 'id';

    // function getaddressOrderBuyyer($id_cart) {
    //     return self::select(
    //             'complete_cart_address.address_name',
    //             'complete_cart_address.phone',
    //             'complete_cart_address.address',
    //             'complete_cart_address.postal_code',
    //             'ma.address_name as nama_biller',
    //             'ma.address',
    //             'ma.postal_code',
    //             DB::raw('select province_name from province where province_id = ma.province_id as kota_biller'),
    //             // 'p.province_name',
    //             // 'c.city_name',
    //             // 'district.subdistrict_name',
    //             'm.instansi',
    //             'm.nama'
    //         )
    //         ->leftJoin('member_address as ma', 'ma.member_address_id', '=', 'complete_cart_address.id_billing_address')
    //         ->leftJoin('member as m', 'm.id', '=', 'ma.member_id')
    //         // ->leftJoin('province as p', 'p.province_id', '=', 'complete_cart_address.province_id')
    //         // ->leftJoin('city as c', 'c.city_id', '=', 'complete_cart_address.city_id')
    //         // ->leftJoin('subdistrict as district', 'district.subdistrict_id', '=', 'complete_cart_address.subdistrict_id')
    //         ->where('complete_cart_address.id_cart', $id_cart)
    //         ->where('complete_cart_address.is_shop_address', 0)
    //         ->first();
    // }
    function getaddressOrderBuyyer($id_cart) {
        return self::select(
                'complete_cart_address.address_name as nama_penerima',
                'complete_cart_address.phone as phone_penerima',
                'complete_cart_address.address as alamat_penerima',
                'complete_cart_address.postal_code as kode_pos_penerima',
                DB::raw('(select province_name from province where province_id = complete_cart_address.province_id) as provinsi_penerima'),
                DB::raw('(select city_name from city where city_id = complete_cart_address.city_id) as kota_penerima'),
                DB::raw('(select subdistrict_name from subdistrict where subdistrict_id = complete_cart_address.subdistrict_id) as district_penerima'),
                'ma.address_name as nama_biller',
                'ma.address as address_biller',
                'ma.postal_code as kode_pos_biller',
                DB::raw('(select province_name from province where province_id = ma.province_id) as provinsi_biller'),
                DB::raw('(select city_name from city where city_id = ma.city_id) as kota_biller'),
                DB::raw('(select subdistrict_name from subdistrict where subdistrict_id = ma.subdistrict_id) as district_biller'),
                'm.instansi',
                'm.nama'
            )
            ->leftJoin('member_address as ma', 'ma.member_address_id', '=', 'complete_cart_address.id_billing_address')
            ->leftJoin('member as m', 'm.id', '=', 'ma.member_id')
            ->where('complete_cart_address.id_cart', $id_cart)
            ->where('complete_cart_address.is_shop_address', 0)
            ->first();
    }


}
