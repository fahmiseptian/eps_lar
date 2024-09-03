<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShopOperational extends Model
{
    public $timestamps = false;
    protected $table = 'shop_operational';
    protected $primaryKey = 'id';

    function getShopOperational($id_shop) {
        $query = DB::table('shop_operational')
            ->select('*')
            ->where('id_shop', $id_shop)
            ->exists();

        if (!$query) {
            for ($i = 1; $i <= 7; $i++) {
                DB::table('shop_operational')->insert([
                    'id_shop' => $id_shop,
                    'id_day' => $i
                ]);
            }

            $data = DB::table('shop_operational')
                ->select('*')
                ->where('id_shop', $id_shop)
                ->get();
        } else {
            $data = DB::table('shop_operational')
                ->select('*')
                ->where('id_shop', $id_shop)
                ->get();
        }

        return $data;
    }

}
