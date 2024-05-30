<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'complete_cart';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $visible = ['invoice','id_cart'];

    public function finance()
    {
        return $this->belongsTo(User::class, 'updated_status_by');
    }

    public function pajak()
    {
        return $this->belongsTo(User::class, 'pelapor_pajak');
    }

    public function completeCartShop()
    {
        return $this->hasOne(CompleteCartShop::class, 'id_cart', 'id');
    }

    function CountPesananUserbyIduser($id_user, $status) {
        $query = DB::table('complete_cart')
            ->select(
                'id',
                'invoice', 
                'id_user', 
                'id_payment', 
                'payment_method', 
                'payment_detail', 
                'status', 
                'status_pembayaran_top', 
                'status_pelaporan_pajak', 
                'note', 
                'created_date', 
                'due_date_payment', 
                'last_update', 
                'updated_status_by'
            );
    
        switch ($status) {
            case 'baru':
                $query->where(function ($q) {
                    $q->where('status', 'pending')
                      ->orWhere('status', 'waiting_approve_by_ppk');
                });
                break;
            case 'belumbayar':
                $query->where('status', 'pending');
                break;
            case 'pengiriman':
                $query->where('status', 'on_delivery');
                break;
            case 'selesai':
                $query->where('status', 'completed');
                break;
            case 'batal':
                $query->where(function ($q) {
                    $q->where('status', 'expired')
                      ->orWhere('status', 'cancel')
                      ->orWhere('status', 'cancel_part');
                });
                break;
            default:
                return 0;
        }
        $query->where('id_user', $id_user);
        return $query->count();
    }

    function getOrderByIdmember($idmember,$kondisi) {
        $query = DB::table('complete_cart as cc')
            ->select(
                'cc.id as id_transaksi',
                'cc.invoice',
                'cc.total',
                'cc.id_payment',
                'cc.created_date as pembuatan_pesanan',
                'cc.jml_top',
                'cc.status_pembayaran_top as status_pembayaran',
                'cc.qty as jmlh_qty',
            )
            ->join('complete_cart_shop as ccs', 'cc.id', '=', 'ccs.id_cart')
            ->where('cc.id_user', $idmember);
            if ($kondisi != null) {
                $query->where('cc.status',$kondisi);
            }
            $query->orderBy('cc.created_date', 'desc')
            ->groupBy('cc.id', 'cc.invoice', 'cc.total', 'cc.id_payment', 'cc.created_date', 'cc.jml_top', 'cc.status_pembayaran_top', 'cc.qty');
        
        return $query->paginate(7);
    }
    
    
    
}    