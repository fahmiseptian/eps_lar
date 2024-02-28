<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'member';
    public $timestamps = false;

    protected $fillable = [
        'email', 'nama', 'no_hp', 'npwp', 'alamat', 'member_status', 'registered_member'
    ];
}
