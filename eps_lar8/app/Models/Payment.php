<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment_method';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $visible = ['name', 'code', 'image', 'fee_nominal', 'fee_percent', 'active', 'is_show', 'flag', 'device', 'is_deleted'];
    protected $fillable = [
        'name', 'code', 'image', 'fee_nominal', 'fee_percent', 'active', 'is_show', 'flag', 'device', 'is_deleted', 'created_by', 'created_date', 'updated_by', 'updated_date'
    ];
}