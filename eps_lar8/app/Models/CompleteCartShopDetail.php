<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompleteCartShopDetail extends Model
{
    protected $table = 'complete_cart_shop_detail';

    protected $fillable = [
        'id_cart',
        'id_product',
        'nama',
        'price',
        'image',
        'qty',
    ];

    public function bastDetail(){
        return $this->hasMany(BastDetail::class, 'id_product', 'id');
    }

    public function product(){
        return $this->belongsTo(Products::class, 'id_product', 'id');
    }

    function getProductOrder($id_shop,$id_cart){
        $carts = DB::table('complete_cart_shop_detail as ccsd')
            ->select(
                'ccsd.*',
            )
            ->where('ccsd.id_cart',$id_cart)
            ->where('ccsd.id_shop',$id_shop)
            ->where('ccsd.status','on_process')
            ->get();

        // Hitung jumlah total barang dengan PPN dan tanpa PPN
        $total_barang_dengan_PPN = 0;
        $total_barang_tanpa_PPN = 0;

        foreach ($carts as $cart) {
            if ($cart->val_ppn != 0) {
                $total_barang_dengan_PPN += $cart->total_non_ppn;
            } else {
                $total_barang_tanpa_PPN += $cart->total_non_ppn;
            }
        }

        // Tambahkan total barang dengan dan tanpa PPN ke hasil
        $result = [
            'carts' => $carts,
            'total_barang_dengan_PPN' => $total_barang_dengan_PPN,
            'total_barang_tanpa_PPN' => $total_barang_tanpa_PPN,
        ];

        return $result;
    }
}
