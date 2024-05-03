<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompleteCartShopDetail extends Model
{
    // Nama tabel dalam basis data
    protected $table = 'complete_cart_shop_detail';

    // Tentukan kolom-kolom yang dapat diisi
    protected $fillable = [
        'id_cart',
        'id_product',
        'nama',
        'price',
        'image',
        'qty',
        // Tambahkan kolom lain yang sesuai dengan tabel `complete_cart_shop_detail`
    ];

    // Tentukan relasi antara CompleteCartShopDetail dan BastDetail
    public function bastDetail()
    {
        return $this->hasMany(BastDetail::class, 'id_product', 'id');
    }

    // Relasi dengan model `Products`
    public function product()
    {
        return $this->belongsTo(Products::class, 'id_product', 'id');
    }
    // Tambahkan relasi lain sesuai kebutuhan
}
