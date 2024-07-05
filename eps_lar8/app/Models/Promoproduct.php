<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Promoproduct extends Model
{
    protected $table = 'promo_product';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function getProductPromobyId_shop($id_shop, $id_category_promo = null)
    {
        $query = DB::table('promo_product as pp')
            ->select(
                'pp.*',
                'p.price',
                'p.name'
                )
            ->join('products as p','p.id','pp.id_product')
            ->where('pp.id_shop', $id_shop)
            ->where('pp.is_active', 'Y');

        if ($id_category_promo != null) {
            $query->where('pp.id_category_promo', $id_category_promo);
        }

        $result = $query->get();
        return $result;
    }


}
