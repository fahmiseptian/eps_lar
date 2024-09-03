<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Libraries\Encryption;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Shop extends Model implements HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia;

    protected $table = 'shop';
    public $timestamps = false;
    protected $visible = ['nama_pt', 'name', 'nik_pemilik', 'npwp', 'phone', 'password', 'nama_pemilik', 'avatar',];
    protected $fillable = [
        'status',
        'type',
        'is_top',
        'packing_estimation',
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

    public function getAddressByIdshop($id_shop)
    {
        $address = DB::table('shop')
            ->select(
                'shop.*',
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
            ->where('ma.active_status', 'active')
            ->where('ma.is_shop_address', 'yes')
            ->get(); // Mengambil satu baris data

        return $address;
    }

    public function  getShopById($id_shop)
    {
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

    function get_estimasiPacking($id_shop)
    {
        $packing = DB::table('shop')
            ->select(
                'packing_estimation'
            )
            ->where('id', $id_shop)
            ->first();
        return $packing;
    }

    function getShopCategory($id_shop)
    {
        $query = DB::table('shop')
            ->select('shop_category')
            ->where('id', $id_shop)
            ->first();
        return $query;
    }

    function getPinSaldo($id_shop)
    {
        $query = DB::table('shop')
            ->select('pin_saldo')
            ->where('id', $id_shop)
            ->first();

        $pin    = $query->pin_saldo;

        return $pin;
    }

    public function getIdShopByOrder($id_order_shop)
    {
        $id_shop = DB::table('complete_cart_shop')
            ->where('id', $id_order_shop)
            ->value('id_shop');

        return $id_shop;
    }

    function getIdMember($id_shop)
    {
        $id_user = DB::table('shop')
            ->where('id', $id_shop)
            ->value('id_user');

        return $id_user;
    }

    function AddressByIdshop($id_shop, $id_address = null)
    {
        $query = DB::table('shop as s')
            ->select(
                'ma.*',
                's.id_address as id_address_default',
                'p.province_name',
                'c.city_name',
                'sub.subdistrict_name',
            )
            ->join('member as m', 'm.id', 's.id_user')
            ->join('member_address as ma', 'ma.member_id', 'm.id')
            ->join('province as p', 'p.province_id', '=', 'ma.province_id')
            ->join('city as c', 'c.city_id', '=', 'ma.city_id')
            ->join('subdistrict as sub', 'sub.subdistrict_id', '=', 'ma.subdistrict_id')
            ->where('s.id', $id_shop)
            ->where('ma.active_status', 'active');
        if ($id_address != null) {
            $query->where('ma.member_address_id', $id_address);
            $address = $query->get();
        } else {
            $address = $query->get();
        }

        return $address;
    }

    function setDefaultAddress($id_shop, $id_address)
    {
        DB::transaction(function () use ($id_shop, $id_address) {
            // Set all addresses for the shop to not be default
            DB::table('member_address')
                ->join('shop', 'member_address.member_id', '=', 'shop.id_user')
                ->where('shop.id', $id_shop)
                ->update([
                    'member_address.is_shop_address' => 'no',
                    'is_default_shipping' => 'no'
                ]);

            // Set the specific address as the default address
            DB::table('member_address')
                ->where('member_address_id', $id_address)
                ->update([
                    'member_address.is_shop_address' => 'yes',
                    'is_default_shipping' => 'yes'
                ]);

            // Update the shop's default address
            DB::table('shop')
                ->where('id', $id_shop)
                ->update(['id_address' => $id_address]);
        });

        return true;
    }

    function getIdUserByid_shop($id_shop)
    {
        $id_user = DB::table('shop')
            ->where('id', $id_shop)
            ->value('id_user');
        return $id_user;
    }

    function get_chatautoReply($id_shop)
    {
        $data = DB::table('shop')->select(
            'autoreply_standar',
            'autoreply_standar_text',
            'autoreply_offline',
            'autoreply_offline_text',
        )
            ->where('id', $id_shop)
            ->first();
        return $data;
    }

    function updateData($data, $id_shop)
    {
        $update = DB::table('shop')
            ->where('id', $id_shop)
            ->update($data);

        if ($update) {
            return true;
        }
        return false;
    }

    function getShopBanner($id_shop)
    {
        $banner = DB::table('shop_banner')
            ->where('id_shop', $id_shop)
            ->orderBy('urutan', 'asc')
            ->get();

        return $banner;
    }

    public function getLampiranShop($id_shop)
    {
        $lampiran = DB::table('lampiran')
            ->where('id_shop', $id_shop)
            ->first(); // Mengambil satu baris data

        return $lampiran;
    }

    function getShop($id_shop)
    {
        $dataShop = DB::table('shop as s')
            ->select(
                's.nama_pt',
                's.npwp',
                's.nama_pemilik',
                's.nik_pemilik',
                's.name',
                's.description',
                's.avatar',
                'm.npwp_address',
                'sc.nama as category',
                's.created_date',
            )
            ->join('member as m', 's.id_user', '=', 'm.id')
            ->join('shop_category as sc', 's.shop_category', '=', 'sc.id')
            ->where('s.id', $id_shop)
            ->first();

        return $dataShop;
    }

    function updateProfile($id_shop, $collom, $value)
    {
        $update = DB::table('shop')
            ->where('id', $id_shop)
            ->update([
                $collom => $value,
            ]);

        if ($update) {
            return true;
        }
        return false;
    }

    function getFollow($id_shop, $id_user)
    {
        $follow = DB::table('shop_follower as sf')
            ->select(
                'sf.id_shop',
                DB::raw("(SELECT count(id_shop) FROM shop_follower WHERE id_member = {$id_user}) as following"),
                DB::raw("(SELECT count(id_member) FROM shop_follower WHERE id_shop = {$id_shop}) as follower")
            )
            ->where('sf.id_shop', $id_shop)
            ->groupBy('sf.id_shop')
            ->first();

        return $follow;
    }
}
