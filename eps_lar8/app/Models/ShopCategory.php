<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShopCategory extends Model
{
    public $timestamps = false;
    protected $table = 'shop_category';
    protected $primaryKey = 'id';

    function getSpesialKategori($id){
        $query = DB::table('shop_category')
        ->select('spesial_kategori')
        ->where('id',$id)
        ->first();

        return $query;
    }
}
