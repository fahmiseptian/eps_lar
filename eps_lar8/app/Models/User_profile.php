<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_profile extends Model
{
    protected $table = 'user_profile';
    public $timestamps = false;
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'firstname', 'lastname', 'user_id', 'address', 'phone', 'seoname'
    ];
}