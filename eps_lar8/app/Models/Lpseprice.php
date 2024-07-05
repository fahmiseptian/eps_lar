<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lpseprice extends Model
{
    use HasFactory;
    protected $table = 'lpse_price';
    public $timestamps = false;

    protected $visible = ['price_lpse'];
    protected $fillable = [
        'price_lpse',
        'id_product',
        'price_before_rounded',

    ];
}
