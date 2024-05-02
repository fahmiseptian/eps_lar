<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    protected $visible = ['name'];
    protected $table = 'access';
    protected $primaryKey = 'id'; 
}