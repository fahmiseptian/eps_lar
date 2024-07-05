<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function getPendapatanHariIni($id_shop) {
		$date_now = date('Y-m-d');
		return self::from('revenue')
    ->where('id_shop', $id_shop)
    ->where('status', 'revenue')
    ->where('last_update', $date_now)
    ->sum('total_diterima_seller');
	}

    public function getRevenuePending($id_shop)
    {
        $data = DB::table('revenue as r')
            ->select('r.*', 'r.total_diterima_seller as total', 'm.nama', 'm.instansi as nama_instansi')
            ->leftJoin('member as m', 'm.id', '=', 'r.id_user')
            ->leftJoin('complete_cart as a', 'r.id_cart', '=', 'a.id')
            ->leftJoin('penarikan_dana_detail as pdd', 'r.id', '=', 'pdd.id_revenue')
            ->where('r.id_shop', $id_shop)
            ->where('r.status', 'pending')
            ->whereNull('pdd.id_revenue')
            ->where(function ($query) {
                $query->where('a.status', 'completed')
                    ->orWhere('a.status', 'complete_payment');
            })
            ->where('a.status_pembayaran_top', '1')
            ->orderBy('r.last_update', 'desc')
            ->get();

        return $data;
    }

    function requestRevenue($id_shop, $ids_trx){
        $ids_trx = explode(',', $ids_trx);
        $query = DB::table('revenue')
            ->select('*', 'total_diterima_seller as total')
            ->where('id_shop', $id_shop)
            ->whereIn('id', $ids_trx)
            ->get();


        $rekening    = Rekening::getDefaultRekeningByShop($id_shop);
        $id_penarikan   = PenarikanDana::insertPenarikanDana($id_shop);

        $total = 0;

        foreach ($query as $trx) {
            $update = DB::table('penarikan_dana_detail')->insert([
                'id_penarikan_dana' => $id_penarikan,
				'id_revenue'		=> $trx->id,
            ]);

			$total 		 += $trx->total;
        }

        $updatePenarikan_dana = DB::table('penarikan_dana')->where('id',$id_penarikan)->update([
            'id_shop'		=> $id_shop,
			'id_rekening'	=> $rekening->id_rekening,
			'total'		=> $total,
        ]);

        if ($updatePenarikan_dana) {
            return true;
        }
        return false;
    }
}
