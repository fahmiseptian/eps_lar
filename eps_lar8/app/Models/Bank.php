<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $visible = ['id' ,'name'];
    protected $fillable = ['name'];
    protected $table = 'bank';
    protected $primaryKey = 'id';
    public $timestamps = false;
    function sdas() {

    }
}
