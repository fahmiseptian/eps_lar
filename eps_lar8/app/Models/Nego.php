<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nego extends Model
{
    protected $table = 'nego';
    protected $primaryKey = 'id'; 

    public function JumlahNegoUlang($id_shop,$where){
        return self::where('id_shop', $id_shop)
                    ->where($where[0], $where[1], $where[2])
                    ->where('status',0)
                    ->select('id')
                    ->count();
    }
}