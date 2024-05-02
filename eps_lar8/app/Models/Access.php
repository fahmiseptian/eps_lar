<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    protected $table = 'access';
    protected $primaryKey = 'id'; 
    public $timestamps = false;

    protected $visible = ['id', 'name', 'code', 'active'];
    protected $fillable = ['name', 'code', 'active', 'created_by', 'created_date', 'updated_by', 'updated_date', 'deleted_by', 'deleted_date'];
}