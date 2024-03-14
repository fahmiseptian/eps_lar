<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lpseprice extends Model
{
    use HasFactory;
    protected $table = 'lpseprice';
    public $timestamps = false;

    protected $visible = ['price_lpse'];
    protected $fillable = [
        'price_lpse',
        // Kolom untuk di edit
    ];
}
