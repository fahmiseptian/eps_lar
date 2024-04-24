<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompleteCartShop extends Model
{
    protected $visible = ['status'];
    public $timestamps = false;
    protected $table = 'complete_cart_shop';
    protected $primaryKey = 'id'; 


    public function countAllorder($id_shop){
        return self::where('id_shop', $id_shop)
            ->count();
    }

    public function get_count_order($id_shop, $status) {
        $query = self::where('id_shop', $id_shop)
                    ->join('complete_cart as cc', 'cc.id', '=', 'complete_cart_shop.id_cart')
                    ->where('complete_cart_shop.id_shop', $id_shop);

        if ($status == 'pending') {
            $query->where('cc.status', 'pending');
        } else {
            $query->where('cc.status', '!=', 'pending');
            if ($status == 'cancel_by_seller') {
                $query->where('cc.status','cancel');
            } else {
                $query->where('complete_cart_shop.status', $status);
            }
        }
        return  $query->count();
    }
    
}