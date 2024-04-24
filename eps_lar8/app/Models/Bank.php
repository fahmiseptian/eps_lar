<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $visible = ['name'];
    protected $table = 'bank';
    protected $primaryKey = 'id';
}