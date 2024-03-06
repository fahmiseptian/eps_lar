<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lpse_config extends Model
{
    use HasFactory;
    protected $table = 'lpse_config';
    protected $fillable = [
        'ppn','pph','fee_mp_percent',
    ];
    public $timestamps = false;
}
