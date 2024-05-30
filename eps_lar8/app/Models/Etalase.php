<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Etalase extends Model
{
    protected $table = 'etalese';

    public function getProductetase($id_etalase)
    {
        return DB::table('etalase_detail as ed')
            ->join('etalase as e', 'ed.id_etalase', '=', 'e.id')
            ->where('e.id', $id_etalase)
            ->select('ed.id_product')
            ->get();
    }


    public function getEtalasetoko($id_shop){
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
}