<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PenarikanDana extends Model
{
    use HasFactory;
    protected $table = 'penarikan_dana';
    public $timestamps = false;
    protected $fillable = ['id_shop','total'];

    public function getPenarikanDana($id_shop)
    {
        return self::join('rekening as r', 'penarikan_dana.id_rekening', '=', 'r.id')
                   ->join('bank as b', 'r.id_bank', '=', 'b.id')
                   ->where('penarikan_dana.id_shop', $id_shop)
                   ->orderBy('penarikan_dana.last_update', 'desc')
                   ->get([
                       'penarikan_dana.*',
                       'r.rek_owner',
                       'r.rek_number',
                       'b.name'
                   ]);
    }

    public function insertPenarikanDana($id_shop)
    {
        $penarikanDana = PenarikanDana::create([
            'id_shop' => $id_shop,
            'total' => 0
        ]);

        return $penarikanDana->id;
    }

    public function detail_penarikan($id_penarikan, $id_shop){
        $query = DB::table('penarikan_dana as pd')
        ->select(
            'pd.id as id_penarikan',
            'pd.total',
            'pd.status',
            'r.invoice',
        )
        ->join('penarikan_dana_detail as pdd', 'pd.id', '=', 'pdd.id_penarikan_dana')
        ->join('revenue as r', 'pdd.id_revenue', '=', 'r.id')
        ->where('pd.id_shop', $id_shop)
        ->where('pd.id', $id_penarikan)
        ->get();
        return $query;
    }
}
