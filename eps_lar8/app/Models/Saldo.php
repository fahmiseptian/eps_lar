<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    protected $visible = ['total_diterima_seller'];
    protected $table = 'revenue';
    protected $primaryKey = 'id'; 
}