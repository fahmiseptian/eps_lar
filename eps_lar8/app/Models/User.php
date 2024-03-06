<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';

    protected $guard = 'admin';

    // Implementasi metode yang diperlukan dari Authenticatable
    public function getAuthIdentifierName()
    {
        return 'id'; // Kolom yang digunakan sebagai identifier (biasanya 'id')
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }
}
