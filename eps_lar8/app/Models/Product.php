<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'product';
    public $timestamps = false;

    protected $visible = ['id','name','stock','last_update','status_lpse'];
    protected $fillable = [
        'status_lpse'
    ];
}
