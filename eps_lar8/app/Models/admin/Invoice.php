<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'complete_cart';
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_status_by');
    }
}