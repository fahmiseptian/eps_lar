<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Etalase extends Model
{
    protected $table = 'etalase';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'name','id_shop','created_dt'
    ];

    public function getProductetase($id_etalase)
    {
        return DB::table('etalase_detail as ed')
            ->join('etalase as e', 'ed.id_etalase', '=', 'e.id')
            ->where('e.id', $id_etalase)
            ->select('ed.id_product')
            ->get();
    }


    public function getEtalasetoko($id_shop)
    {
        $dataetalse = DB::table('etalase as e')
            ->join('shop as s', 'e.id_shop', '=', 's.id')
            ->where('s.id', $id_shop)
            ->where('e.display_status', 'Y')
            ->select(
                'e.name',
                'e.id'
            )
            ->get();

        return $dataetalse;
    }

    function getEtalasse($id_shop)
    {
        $query = DB::table('etalase')
            ->select('etalase.*', DB::raw('(select count(id) from etalase_detail where id_etalase = etalase.id) as cp'))
            ->where('id_shop', $id_shop)
            ->get();
        return $query;
    }
}
