<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Promo_category extends Model
{
    protected $table = 'promo_category';
    protected $primaryKey = 'id';

    public function getPromoCategory()
    {
        return DB::table($this->table)
            ->select('*')
            ->where('active', 'Y')
            ->whereNull('deleted_by')
            ->get();
    }

}
