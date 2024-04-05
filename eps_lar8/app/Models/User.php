<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Libraries\Encryption; 

class User extends Model
{
    use HasFactory;

    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $fillable = [
        'username', 'password', 'id_admin', 'access_id',
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $Encryption;

    protected static function booted()
    {
        static::retrieved(function ($model) {
            $model->Encryption = new Encryption();
        });
    }

    public function decryptPassword($password)
    {
        if ($this->Encryption !== null) {
            $decrypted = $this->Encryption->decrypt($password);

            if ($decrypted === false) {
                return "Error";
            } 

            return $decrypted;
        } else {
            return "Encryption object is not initialized";
        }
    }

    public function encryptPassword($password)
    {
        if ($this->Encryption !== null) {
            return $this->Encryption->encrypt($password);
        } else {
            return "Encryption object is not initialized";
        }
    }
}
