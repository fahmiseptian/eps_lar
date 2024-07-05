<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductCategory extends Model
{
    protected $table = 'product_category';

    public function getCategoryBydata($data)
    {
        return $this->where($data)
                    ->whereNull('deleted_by')
                    ->orderBy('name', 'asc')
                    ->get();
    }

    function getCategorybyId($id) {
        $data = DB::table('product_category')
        ->select(
            '*',
        )
        ->where('id',$id)
        ->first();

        return $data;
    }

    function check_ppn($id_product){
        $check = DB::table('products as p')
            ->select('pc.barang_kena_ppn', 'p.id_category')
            ->leftJoin('product_category as pc', 'pc.id', '=', 'p.id_category')
            ->where('p.id', $id_product)
            ->first();
        return $check ;
    }
}
