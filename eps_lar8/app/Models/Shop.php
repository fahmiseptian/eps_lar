<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
    protected $table = 'shop';
    public $timestamps = false;

    protected $visible = ['nama_pt','name','nik_pemilik','npwp','phone'];
    protected $fillable = [
        'status','type','is_top',
    ];
}
