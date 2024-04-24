<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VerificationCode extends Model
{
    use HasFactory;

    protected $table = 'verification_codes';
    protected $fillable = ['id_user', 'code', 'expires_at'];

    // Definisi hubungan dengan model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
