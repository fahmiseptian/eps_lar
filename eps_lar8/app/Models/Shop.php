<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Libraries\Encryption; 

class Shop extends Model
{
    use HasFactory;
    protected $table = 'shop';
    public $timestamps = false;
    protected $visible = ['nama_pt','name','nik_pemilik','npwp','phone','password', 'nama_pemilik'];
    protected $fillable = [
        'status','type','is_top',
    ];

    protected $Encryption;

    protected static function booted()
    {
        static::retrieved(function ($model) {
            $model->Encryption = new Encryption();
        });
    }

    public static function getTypeById($id)
    {
        return self::where('id', $id)
                    ->pluck('type')
                    ->first();
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
            return "Objek enkripsi tidak dikenali";
        }
    }
}
