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

    function getCategorybyId($id)
    {
        $data = DB::table('product_category')
            ->select(
                '*',
            )
            ->where('id', $id)
            ->first();

        return $data;
    }

    function check_ppn($id_product)
    {
        $check = DB::table('products as p')
            ->select('pc.barang_kena_ppn', 'p.id_category')
            ->leftJoin('product_category as pc', 'pc.id', '=', 'p.id_category')
            ->where('p.id', $id_product)
            ->first();
        return $check;
    }

    function kategorilv1()
    {
        $query = DB::table('product_category')
            ->where('level', 1)
            ->where('active', 'Y')
            ->where('lpse_report_id', !null)
            ->get();

        return $query;
    }

    function get_rank_category_byIdshop($id_shop)
    {
        $query = DB::table('product_category as c')
            ->select([
                'c.id',
                'c.name',
                DB::raw('(SELECT SUM(qty)
                  FROM complete_cart_shop_detail
                  LEFT JOIN product ON product.id = complete_cart_shop_detail.id_product
                  WHERE product.id_category = c.id) as count')
            ])
            ->leftJoin('products as p', 'p.id_category', '=', 'c.id')
            ->where('p.status_delete', 'N')
            ->where('p.id_shop', $id_shop)
            ->orderBy('count', 'desc')
            ->distinct()
            ->get();

        return $query;
    }

    function jenisProduct($id_category)
    {
        // Ini berfungsi untuk mengecek apakah produk termasuk barang atau jasa
        // id Jasa = 1590, 1597, dan 1600
        // Hasil: 1 == Jasa atau ruangan || 0 == Barang

        $query = DB::table('product_category as a')
            ->select('id')
            ->where('a.id', $id_category)
            ->where(function ($query) {
                $query->where('a.parent_id', 1590)
                    ->orWhere('a.parent_id', 1597)
                    ->orWhere('a.parent_id', 1600);
            })
            ->count();

        return $query;
    }
}
