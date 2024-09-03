<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    protected $table = 'rekening';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_shop',
        'id_bank',
        'rek_owner',
        'rek_number',
        'rek_location',
        'rek_city',
        'created_dt',
        'is_default'
        // Tambahkan kolom lain yang diperlukan
    ];

    public static function getDefaultRekeningByShop($id_shop)
    {
        // Mengambil data rekening berdasarkan id_shop dan is_default = 'Y'
        return self::where('id_shop', $id_shop)
                   ->where('is_default', 'Y')
                   ->join('bank as b', 'rekening.id_bank', '=', 'b.id')
                   ->select(
                    'rekening.id as id_rekening',
                    'rekening.*',
                     'b.*')
                   ->first();
    }

    public static function getRekeningByShopAndIsDefaultN($id_shop)
    {
        // Query untuk mengambil data rekening berdasarkan id_shop dengan is_default = 'N'
        return self::where('id_shop', $id_shop)
                   ->where('is_default', 'N')
                   ->where('is_deleted', 'N')
                   ->join('bank as b', 'rekening.id_bank', '=', 'b.id')
                   ->select('rekening.*', 'b.name as bank_name') // Pilih kolom rekening dan bank_name
                   ->get();
    }

    public static function JumlahRekeningIsDefaultN($id_shop)
    {
        return self::where('id_shop', $id_shop)
                   ->where('is_deleted', 'N')
                   ->join('bank as b', 'rekening.id_bank', '=', 'b.id')
                   ->count();
    }

    public static function getRekeningById($id, $id_shop)
    {
        // Mengambil data rekening berdasarkan id dan id_shop
        return self::where('rekening.id', $id)
                ->where('rekening.id_shop', $id_shop)
                ->join('bank as b', 'rekening.id_bank', '=', 'b.id')
                ->select('rekening.*', 'b.name as bank_name')
                ->first();
    }
}
