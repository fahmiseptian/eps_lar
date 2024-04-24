<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreeOngkir extends Model
{
    public $timestamps = false;
    protected $table = 'free_ongkir';   
    protected $fillable = [
        'id_shop',
        'id_province',
        'status',
        'datetime'
    ];
}