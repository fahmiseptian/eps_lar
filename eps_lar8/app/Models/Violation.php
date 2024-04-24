<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    protected $table = 'product_violation_report';
    protected $primaryKey = 'id';

    public function countViolation($id_shop, $type = null, $where = null)
    {
        $query = self::where('id_shop', $id_shop)
                    ->join('product_violation as pv', 'pv.id', '=', 'product_violation_report.id_violation');
        if ($type !== null) {
            $query = $query->where('pv.type', $type);
        }
        if ($where !== null) {
            $query = $query->where($where);
        }
        return $query->count();
    }   
}
