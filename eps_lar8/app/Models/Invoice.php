<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'complete_cart';
    protected $primaryKey = 'id';
    protected $visible = ['invoice'];
    public $timestamps = false;

    public function finance()
    {
        return $this->belongsTo(User::class, 'updated_status_by');
    }

    public function pajak()
    {
        return $this->belongsTo(User::class, 'pelapor_pajak');
    }

    public function completeCartShop()
    {
        return $this->hasOne(CompleteCartShop::class, 'id_cart', 'id');
    }
}