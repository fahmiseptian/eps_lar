<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'member';
    public $timestamps = false;

    protected $visible = ['nama', 'no_hp', 'alamat', 'email', 'instansi', 'satker','npwp','npwp_address'];
    protected $fillable = [
        'email', 'nama', 'no_hp', 'npwp', 'alamat', 'member_status', 'registered_member'
    ];
}
