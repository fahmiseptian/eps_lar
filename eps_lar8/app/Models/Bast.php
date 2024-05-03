<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bast extends Model
{
    protected $table = 'bast';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function getProductBast($id_bast, $id_cart)
{
    // Mengambil semua detail dari `bast_detail` dengan relasi `cartDetail` berdasarkan `id_cart`
    $details = BastDetail::with(['cartDetail' => function ($query) use ($id_cart) {
        $query->where('id_cart', '=', $id_cart);
    }])
    ->where('id_bast', '=', $id_bast)
    ->get();

    // Inisialisasi array untuk menyimpan output
    $outputs = [];
    
    // Variabel untuk menyimpan `created_date`
    $created_date = null;

    // Iterasi melalui semua detail yang ditemukan
    foreach ($details as $detail) {
        // Inisialisasi variabel gambar
        $gambar = null;
        $image = null;

        if ($detail->cartDetail && $detail->cartDetail->id_product) {
            // Mengambil produk terkait berdasarkan `id_product`
            $product = Products::find($detail->cartDetail->id_product);
            $detail->product = $product;

            // Jika produk memiliki gambar dan arraynya tidak kosong, ambil gambar pertama dari array
            if ($product && is_array($product->artwork_url_lg) && count($product->artwork_url_lg) > 0) {
                $gambar = $product->artwork_url_lg[0];
            } else {
                $image = $detail->cartDetail->image;
            }
        }

        // Membuat objek `stdClass` untuk output
        $output = new \stdClass();
        $output->id = $detail->id;
        $output->name = $detail->cartDetail->nama;
        $output->base_price = $detail->cartDetail->price;
        $output->qty = $detail->cartDetail->qty;
        $output->gambar = $gambar;
        $output->image = $image;

        // // Menambahkan gambar jika tersedia
        // if ($gambar !== null) {
        //     $output->gambar = $gambar;
        // }
        // if ($image !== null) {
        //     $output->image = $image;
        // }

        // Tambahkan output ke array outputs
        $outputs[] = $output;

        // Simpan `created_date` jika belum disimpan
        if ($created_date === null) {
            $created_date = $detail->created_date;
        }
    }

    // Kembalikan output dalam bentuk array
    $result = new \stdClass();
    $result->details = $outputs;
    $result->created_date = $created_date;
    return $result;
}

    
    public static function getBast($id_cart_shop)
    {
        $bast = self::select('*')
                    ->where('id_cart_shop', $id_cart_shop)
                    ->first();
        // Pastikan $bast bukan null
        if ($bast) {
            $dataBast = new \stdClass();

            $dataBast->id = $bast->id;
            $dataBast->detail= self::getProductBast($bast->id, $bast->id_cart);
            
            return $dataBast; 
        }
        return null; 
    }


    
}