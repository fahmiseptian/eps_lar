<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Libraries\Encryption; 

class User extends Model
{
    use HasFactory;

    protected $table = 'user';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'username','password', 'id_admin', 'access_id', 'active', 'created_by', 'updated_by', 'updated_date', 'deleted_by', 'deleted_date'
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
            $cek = $this->Encryption->decrypt($password);

            if ($cek === false) {
                return "Error";
            } 

            return $cek;
        } else {
            return "Encryption object is not initialized";
        }
    }

    public function encryptPassword($password)
    {
        $encryption = new Encryption(); // Membuat instance objek Encryption
        return $encryption->encrypt($password);
    }

    public function access()
    {
        return $this->belongsTo(Access::class, 'access_id');
    }

    public function profile()
    {
        return $this->belongsTo(User_profile::class, 'id');
    }
}
