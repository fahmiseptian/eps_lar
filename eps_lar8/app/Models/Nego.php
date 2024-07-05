<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Nego extends Model
{
    protected $table = 'nego';
    protected $primaryKey = 'id';

    public function JumlahNegoUlang($id_shop, $where)
    {
        return self::where('id_shop', $id_shop)
            ->where($where[0], $where[1], $where[2])
            ->where('status', 0)
            ->select('id')
            ->count();
    }

    function CountNegobyIdmember($id_member, $kondisi)
    {
        $query = self::select('nego.id')
            ->join('product_nego as b', 'nego.id', '=', 'b.id_nego')
            ->where('nego.member_id', $id_member);

        if ($kondisi == 'belum') {
            $query->where('b.send_by', 0)
                ->where('nego.status', 0)
                ->where(function ($q) {
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

    function getNego($kondisi, $id_shop)
    {
        $negos = DB::table('nego as n')
            ->select(
                'n.*',
                'pn.*',
                'pn.status as kondisi_nego'
            )
            ->join('product_nego as pn', 'pn.id_nego', 'n.id')
            ->where('n.id_shop', $id_shop)
            ->where('n.complete_checkout', 0)
            ->get();

        foreach ($negos as $nego) {
            $getproduct = Products::find($nego->id_product);
            $nego->dataProduct = $getproduct;
        }

        return $negos;
    }


    public function getNegos($id_shop, $status, $status_nego, $spesial = null, $send_by = null)
    {
        $query = DB::table('nego as n')
            ->select(
                'n.id as idnego',
                'n.status as nego_status',
                'pn.id_product',
                'pn.*',
                'pn.id as idpnego',
                'pn.base_price',
                'pn.qty'
            )
            ->leftJoin('product_nego as pn', function ($join) {
                $join->on('pn.id_nego', '=', 'n.id')
                    ->whereRaw('pn.id = (SELECT MAX(id) FROM product_nego WHERE id_nego = n.id)');
            })
            ->where('n.id_shop', $id_shop)
            ->where('n.status', $status);

        if (!is_null($spesial)) {
            $query->where('n.status_nego', $status_nego, $spesial);
        } else {
            $query->where('n.status_nego', $status_nego);
        }

        if (!is_null($send_by)) {
            $query->where('send_by', $send_by);
        }
        $query->orderBy('n.id', 'DESC');

        $negos = $query->get();

        foreach ($negos as $nego) {
            $getproduct = Products::find($nego->id_product);
            $nego->dataProduct = $getproduct;
        }

        return $negos;
    }


    function DetailNego($id_shop, $id_nego)
    {
        $query = DB::table('nego as n')
            ->select(
                'n.*',
                'n.status as nego_status',
                's.name as nama_toko',
                'member.nama as nama_pembeli',
                'member.instansi',
                'member.satker',
                'pn.*',
                DB::raw('(SELECT harga_nego FROM product_nego WHERE id_nego = n.id ORDER BY id DESC LIMIT 1) as harga_nego_terbaru'),
                DB::raw('(SELECT nominal_didapat FROM product_nego WHERE id_nego = n.id ORDER BY id DESC LIMIT 1) as harga_didapat_terbaru')
            )
            ->join('product_nego as pn', 'pn.id_nego', 'n.id')
            ->join('shop as s', 's.id', 'n.id_shop')
            ->leftJoin('member', 'member.id', 'n.member_id')
            ->where('n.id_shop', $id_shop)
            ->where('n.id', $id_nego)
            ->orderBy('n.id', 'DESC');

        $nego = $query->first();

        $getproduct = Products::getDataProduct($nego->id_product);
        $nego->dataProduct = $getproduct;

        $negosProduct = $this->dataNegoProduct($id_nego);
        $nego->dataNego = $negosProduct;

        return $nego;
    }

    function dataNegoProduct($id_nego)
    {
        $negosProduct = DB::table('product_nego as pn')
            ->select(
                '*'
            )
            ->where('id_nego', $id_nego)
            ->get();

        return $negosProduct;
    }

    public function add_respon($data, $last_id)
    {
        try {
            // Update product_nego status
            $updatedProductNego = DB::table('product_nego')
                ->where('id', $last_id)
                ->update(['status' => '2']);

            if ($updatedProductNego > 0) {
                // Update nego status_nego
                $updatedNego = DB::table('nego')
                    ->where('id', $data['id_nego'])
                    ->update(['status_nego' => '2']);

                if ($updatedNego > 0) {
                    // Insert new data into product_nego
                    $inserted = DB::table('product_nego')->insert($data);

                    if ($inserted) {
                        return true;
                    } else {
                        return '1';
                    }
                } else {
                    return '2';
                }
            } else {
                return '3';
            }
        } catch (\Exception $e) {
            // Log the exception or handle it as needed
            Log::error('Error in add_respon method: ' . $e->getMessage());
            return '4';
        }
    }

    function acc_nego($id) {
        $dataNego = DB::table('product_nego')
            ->select('id')
            ->where('id_nego', $id)
            ->orderBy('id', 'desc')
            ->first();

        if ($dataNego && $dataNego->id) {
            // Update status di tabel nego
            $negoUpdated = DB::table('nego')
                ->where('id', $id)
                ->update([
                    'status' => '1',
                    'status_nego' => '2'
                ]);

            // Update status di tabel product_nego
            $productNegoUpdated = DB::table('product_nego')
                ->where('id', $dataNego->id)
                ->update(['status' => '1']);

            // Check if both updates were successful
            if ($negoUpdated && $productNegoUpdated) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function DetailNegoByid($id_nego) {
        $get_nego = DB::table('nego as n')
        ->select(
            'n.id as id_nego',
            'pn.id as id_nego_product',
            'pn.id_product',
            'n.id_shop',
            'n.member_id',
            'n.status as n_status',
            'pn.status as pn_status',
            'n.complete_checkout',
            'n.status_nego',
            'n.qty',
            'pn.harga_nego',
            'pn.base_price',
            'pn.nominal_didapat',
            'n.harga_awal_satuan',
            'n.harga_awal_total',
            'n.harga_input_seller',
            'pn.timestamp',
            'n.created_date',
            'pn.update_date'
        )
        ->join('product_nego as pn', 'pn.id_nego', '=', 'n.id', 'left')
        // ->where('n.member_id', $id_user) // Uncomment if needed
        ->where('n.status', '1')
        ->where('n.id', $id_nego)
        ->where('pn.status', '1')
        ->where('n.complete_checkout', '0')
        // ->where('pn.id_product', $id_product) // Uncomment if needed
        // ->where('pn.qty', $qty) // Uncomment if needed
        ->limit(1)
        ->first();

        return $get_nego;
    }
}
