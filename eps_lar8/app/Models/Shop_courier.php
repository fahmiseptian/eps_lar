<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop_courier extends Model
{
    public $timestamps = false;
    protected $table = 'shop_courier';
    protected $primaryKey = 'id'; 
    protected $fillable = [
        'id_shop',
        'id_courier',
        'created_date',
    ];
}