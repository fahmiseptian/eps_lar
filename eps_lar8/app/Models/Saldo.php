<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    protected $visible = ['total_diterima_seller'];
    protected $table = 'revenue';
    protected $primaryKey = 'id'; 

    public static function calculatePendingSaldo($id_shop)
    {
        return self::where('id_shop', $id_shop)
                    ->where('status', 'pending')
                    ->whereNotIn('id', function($query) {
                        $query->select('id_revenue')
                              ->from('penarikan_dana_detail');
                    })
                    ->sum('total_diterima_seller');
    }

    // Metode untuk menghitung saldo success berdasarkan id_shop
    public static function calculateSuccessSaldo($id_shop)
    {
        return self::where('id_shop', $id_shop)
                    ->where('status', 'success')
                    ->sum('total_diterima_seller');
    }

    // Metode untuk menghitung saldo pending berdasarkan id_shop
    public static function Pendingsaldo($id_shop)
    {
        return self::join('member', 'member.id', '=', 'revenue.id_user')
                    ->leftJoin('complete_cart', 'revenue.id_cart', '=', 'complete_cart.id')
                    ->leftJoin('penarikan_dana_detail as pdd', 'revenue.id', '=', 'pdd.id_revenue')
                    ->where('revenue.id_shop', $id_shop)
                    ->where('revenue.status', 'pending')
                    ->whereNull('pdd.id_revenue')
                    ->where(function ($query) {
                        $query->where('complete_cart.status', 'completed')
                              ->orWhere('complete_cart.status', 'complete_payment');
                    })
                    ->where('complete_cart.status_pembayaran_top', '1')
                    ->orderBy('revenue.invoice', 'desc')
                    ->get(['revenue.*', 'revenue.total_diterima_seller as total', 'member.nama', 'member.instansi as nama_instansi']);
    }

    // Metode untuk menghitung saldo sukses berdasarkan id_shop
    public static function Successsaldo($id_shop)
    {
        return self::join('penarikan_dana_detail as pdd', 'revenue.id', '=', 'pdd.id_revenue')
                    ->join('penarikan_dana as pd', 'pdd.id_penarikan_dana', '=', 'pd.id')
                    ->join('member as m', 'revenue.id_user', '=', 'm.id')
                    ->where('pd.id_shop', $id_shop)
                    ->where('revenue.status', 'success')
                    ->orderBy('revenue.last_update', 'desc')
                    ->get([
                        'revenue.*',
                        'revenue.total_diterima_seller as total',
                        'm.nama',
                        'pd.status as status_pengajuan',
                        'm.instansi as nama_instansi'
                    ]);
    }
}