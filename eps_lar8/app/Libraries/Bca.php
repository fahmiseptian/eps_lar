<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\returnSelf;

class Bca
{
    public function getDataUser($data_user)
    {
        $query = DB::table('api_user')
            ->where($data_user)
            ->where('active_status', 'Y')
            ->first();
        return $query;
    }

    function checkToken($token)
    {
        $query = DB::table('api_bca_payload')
            ->where('token_eps', $token)
            ->where('expired_dt_token', '>', date('Y-m-d H:i:s'))
            ->count();

        return $query;
    }


    function insertBcaPayload($data)
    {
        $id = DB::table('api_bca_payload')
            ->insertGetId($data);
        return $id;
    }

    function updateBcaPayload($data, $id)
    {
        $update = DB::table('api_bca_payload')
            ->where('id', $id)
            ->update($data);

        if ($update) {
            return true;
        }
        return false;
    }

    function getDataInvoice($id_cart)
    {
        $invoice = DB::table('complete_cart')
            ->select(
                'complete_cart.id',
                'complete_cart.invoice',
                'complete_cart.total',
                'complete_cart.status_pembayaran_top',
                'm.nama',
                'm.email',
                'm.no_hp',
                'm.instansi',
                'm.satker',
            )
            ->leftJoin('member as m', 'm.id', 'complete_cart.id_user')
            ->where('complete_cart.id', $id_cart)
            ->first();

        return $invoice;
    }

    function getAllBill($invoice)
    {
        // Mengambil informasi tagihan dari tabel complete_cart
        $bill = DB::table('complete_cart')
            ->select(
                'complete_cart.id',
                'complete_cart.invoice',
                'complete_cart.total',
                'complete_cart.status_pembayaran_top',
                'm.nama',
                'm.email',
                'm.no_hp',
                'm.instansi',
                'm.satker'
            )
            ->leftJoin('member as m', 'm.id', '=', 'complete_cart.id_user')
            ->where('complete_cart.invoice', 'INV-' . $invoice)
            ->first();

        return $bill;
    }

    public function updatePayment($id_cart)
    {
        $updated_at = now(); 
        DB::table('complete_cart')
            ->where('id', $id_cart)
            ->update([
                'updated_status_by' => null,
                'status_pembayaran_top' => 1,
                'tanggal_bayar' => $updated_at
            ]);
        return true;
    }


    function insertBcaReq($data)
    {
        $id = DB::table('api_bca_request')->insertGetId($data);
        return $id;
    }

    function getCountExternalId($data)
    {
        $count = DB::table('api_bca_request')
            ->where($data)
            ->where('date', 'like', date('Y-m-d') . '%')
            ->count();

        return $count === 0;
    }
}
