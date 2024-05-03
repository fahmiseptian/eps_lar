<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BastDetail extends Model
{
    protected $table = 'bast_detail';
    protected $fillable = [
        'id_bast',
        'id_product',
    ];

    public function cartDetail()
    {
        return $this->belongsTo(CompleteCartShopDetail::class, 'id_product', 'id');
    }

    public function bast()
    {
        return $this->belongsTo(Bast::class, 'id_bast');
    }

    public function getBAstbyIdBast($id_bast){
        return self::where('id_bast', $id_bast)
            ->select('qty', 'qty_diterima', 'qty_dikembalikan')  // Memilih kolom yang diinginkan
            ->get();

    }
}
