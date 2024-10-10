<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Libraries\Encryption;
use Illuminate\Support\Facades\DB;

class Member extends Model
{
    protected $table = 'member';
    public $timestamps = false;

    protected $visible = ['nama', 'no_hp', 'alamat', 'email', 'instansi', 'satker', 'npwp', 'npwp_address', 'id_member_type'];
    protected $fillable = [
        'email',
        'nama',
        'no_hp',
        'npwp',
        'alamat',
        'member_status',
        'registered_member'
    ];

    public function checkemail($email)
    {
        $data = self::select('member_status')
            ->where('email', $email)
            ->where('registered_member', '1')
            ->get();
        if ($data) {
            $count = $data->count();
            if ($count > 0) {
                $row = $data->first();
                return $row->member_status;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function checkpassword($email)
    {
        $data = self::select('password')
            ->where('email', $email)
            ->where('member_status', 'active')
            ->get();
        $passwords = $data->pluck('password')->toArray();
        return $passwords;
    }

    public static function getInstansiBYeamil($email)
    {
        $new_data = [];

        $data = self::where('email', $email)
            ->pluck('id_instansi');

        if ($data->isNotEmpty()) {
            $new_data = $data->toArray();
        }

        return $new_data;
    }

    public function get_instansi($list_instansi = [])
    {
        $new_data = [];

        $query = DB::table('m_lpse_instansi')
            ->select('id as id_instansi', 'nama as nama_instansi')
            ->whereNotNull('nama')
            ->where('nama', '!=', '');

        if (!empty($list_instansi)) {
            $query->whereIn('id', $list_instansi);
        }

        $query->orderBy('nama', 'ASC');
        $data = $query->get();

        if ($data->isNotEmpty()) {
            $new_data = $data->toArray();
        }

        return ['success' => $data->isNotEmpty(), 'data' => $new_data];
    }

    public function get_satker($instansi)
    {
        $new_data = [];

        $query = DB::table('m_lpse_satker')
            ->select('id as id_satker', 'nama as nama_satker')
            ->where('id_instansi', $instansi)
            ->whereNotNull('nama')
            ->where('nama', '!=', '')
            ->orderBy('nama', 'ASC');

        $data = $query->get();

        if ($data->isNotEmpty()) {
            $new_data = $data->toArray();
        }

        return ['success' => $data->isNotEmpty(), 'data' => $new_data];
    }

    public function get_bidang($satker)
    {
        $new_data = [];

        $query = DB::table('m_lpse_bidang')
            ->select('id as id_bidang', 'nama as nama_bidang')
            ->where('id_satker', $satker)
            ->whereNotNull('nama')
            ->where('nama', '!=', '')
            ->orderBy('nama', 'ASC');

        $data = $query->get();

        if ($data->isNotEmpty()) {
            $new_data = $data->toArray();
        }

        return ['success' => $data->isNotEmpty(), 'data' => $new_data];
    }

    public function checkAccount($email, $instansi, $satker, $bidang)
    {

        $data = self::select('member_status')
            ->where('email', $email)
            ->where('id_instansi', $instansi)
            ->where('id_satker', $satker)
            ->where('id_bidang', $bidang)
            ->where('registered_member', '1')
            ->get();
        if ($data) {
            $count = $data->count();
            if ($count > 0) {
                $row = $data->first();
                return $row->member_status;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function checkDecPass($email, $instansi, $satker, $bidang)
    {
        $result = DB::table('member')
            ->select('password')
            ->where(function ($query) use ($email) {
                $query->where('email', $email)
                    ->orWhere('no_hp', $email);
            })
            ->where(function ($query) use ($instansi) {
                $query->where('instansi', $instansi)
                    ->orWhere('id_instansi', $instansi);
            })
            ->where(function ($query) use ($satker) {
                $query->where('satker', $satker)
                    ->orWhere('id_satker', $satker);
            })
            ->where(function ($query) use ($bidang) {
                $query->where('bidang', $bidang)
                    ->orWhere('id_bidang', $bidang);
            })
            ->where('member_status', 'active')
            ->first();

        if ($result) {
            return $result->password;
        }
    }

    public function updateDate($email, $instansi, $satker, $bidang)
    {
        // Lakukan pembaruan data
        return self::where('email', $email)
            ->where('id_instansi', $instansi)
            ->where('id_satker', $satker)
            ->where('id_bidang', $bidang)
            ->update(['last_update' => now()]); // Atau gunakan Carbon untuk mengatur waktu sekarang
    }

    public function getDataByEmail($email, $instansi, $satker, $bidang)
    {
        $data = DB::table('member')
            ->select('id', 'nama', 'foto', 'no_hp', 'password', 'email', 'member_status', 'id_member_type', 'instansi', 'satker', 'bidang')
            ->where(function ($query) use ($email) {
                $query->where('email', $email)
                    ->orWhere('no_hp', $email);
            })
            ->where('id_instansi', $instansi)
            ->where('id_satker', $satker)
            ->where('id_bidang', $bidang)
            ->get()
            ->toArray();
        return $data;
    }

    public static function getWishlist($member_id)
    {
        $result = DB::table('member_wishlist')
            ->select('id_product')
            ->where('id_member', $member_id)
            ->get();

        $data = [];
        foreach ($result as $w) {
            $data[] = $w->id_product;
        }

        return $data;
    }

    function getaddressDefaultbyIdMember($id_member)
    {
        $data = DB::table('member as m')
            ->select(
                'ma.member_address_id',
                'ma.phone',
                'ma.address_name',
                'ma.address',
                'ma.postal_code',
                'p.province_name',
                's.subdistrict_name',
                'c.city_name as city',
            )
            ->join('member_address as ma', 'm.id', 'ma.member_id')
            ->join('province as p', 'p.province_id', 'ma.province_id')
            ->join('city as c', 'ma.city_id', 'c.city_id')
            ->join('subdistrict as s', 's.subdistrict_id', 'ma.subdistrict_id')
            ->where('m.id', $id_member)
            ->where('ma.is_default_shipping', 'yes')
            ->first();
        return $data;
    }
    function getaddressbyIdMember($id_member)
    {
        $data = DB::table('member as m')
            ->select(
                'ma.*',
                'p.province_name',
                's.subdistrict_name',
                'c.city_name as city',
            )
            ->join('member_address as ma', 'm.id', 'ma.member_id')
            ->join('province as p', 'p.province_id', 'ma.province_id')
            ->join('city as c', 'ma.city_id', 'c.city_id')
            ->join('subdistrict as s', 's.subdistrict_id', 'ma.subdistrict_id')
            ->where('m.id', $id_member)
            ->where('ma.active_status', 'active')
            ->orderBy('ma.member_address_id', 'desc')
            ->get();
        return $data;
    }

    public function getDataMember($id)
    {
        $result = DB::table('member as a')
            ->select('a.id AS id_member', 'a.nama', 'a.email', 'b.id AS id_instansi', 'c.id AS id_satker', 'd.id AS id_bidang', 'b.id_instansi AS id_instansi_lpse', 'c.id_satker AS id_satker_lpse', 'd.id_bidang AS id_bidang_lpse', 'b.nama AS nm_instansi', 'c.nama AS nm_satker', 'd.nama AS nm_bidang', 'a.id_member_type', 'e.created_user', 'e.limit_start', 'e.limit_end')
            ->leftJoin('m_lpse_instansi as b', 'a.id_instansi_lpse', '=', 'b.id_instansi')
            ->leftJoin('m_lpse_satker as c', 'a.id_satker_lpse', '=', 'c.id_satker')
            ->leftJoin('m_lpse_bidang as d', 'a.id_bidang_lpse', '=', 'd.id_bidang')
            ->leftJoin('member_satker as e', 'a.id', '=', 'e.id_member')
            ->where('a.id', $id)
            ->first();

        return $result;
    }

    function checkUser($email)
    {
        $check = DB::table('member')
            ->where('email', $email)
            ->first();

        if ($check) {
            return true;
        }
        return false;
    }

    function getaddressbyId($address)
    {
        $address = DB::table('member as m')
            ->select(
                'ma.*',
                'p.province_name',
                's.subdistrict_name',
                'c.city_name as city',
            )
            ->join('member_address as ma', 'm.id', 'ma.member_id')
            ->join('province as p', 'p.province_id', 'ma.province_id')
            ->join('city as c', 'ma.city_id', 'c.city_id')
            ->join('subdistrict as s', 's.subdistrict_id', 'ma.subdistrict_id')
            ->where('ma.member_address_id', $address)
            ->first();
        return $address;
    }

    function get_member_satker($id_member, $role)
    {
        $query = DB::table('member_satker as ms')
            ->select(
                'm.*',
                'ms.id as id_ms'
            )
            ->join('member as m', 'm.id', 'ms.id_member')
            ->where('ms.created_user', $id_member)
            ->where('m.id_member_type', $role)
            ->where('m.member_status', '!=', 'delete')
            ->get();

        return $query;
    }

    function getlimitppk($id_member)
    {
        $query =  DB::table('member_satker as ms')
            ->select(
                'ms.limit_start as batas_awal',
                'ms.limit_end as batas_akhir'
            )
            ->join('member as m', 'm.id', 'ms.id_member')
            ->where('ms.created_user',  $id_member)
            ->where('m.id_member_type', 4)
            ->where('m.member_status', 'active')
            ->first();
        return $query;
    }
}
