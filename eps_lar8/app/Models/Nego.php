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

    function CountNegobyIdmember($id_member, $kondisi) {
        $query = self::select('nego.id')
            ->join('product_nego as b', 'nego.id', '=', 'b.id_nego')
            ->where('nego.member_id', $id_member);
    
        if ($kondisi == 'belum') {
            $query->where('b.send_by', 0)
                  ->where('nego.status', 0)
                  ->where(function($q) {
                      $q->where('nego.status_nego', 0)
                        ->orWhere('nego.status_nego', 1);
                  });
        } else if ($kondisi == 'sudah') {
            $query->where('b.send_by', 0)
                  ->where('nego.status', '!=', 0);
        } else if ($kondisi == 'ulang') {
            $query->where('nego.status', '=', 0)
                  ->where('nego.status_nego', 2)
                  ->where('b.send_by', 1);
        } else if ($kondisi == 'batal') {
            $query->where('nego.status', 2)
                  ->where('nego.status_nego', 2);
        }
        $query->groupBy('nego.id');
    
        return $query->pluck('nego.id')->count();
    }
       
}