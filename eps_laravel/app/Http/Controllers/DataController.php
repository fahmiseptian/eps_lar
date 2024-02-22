<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function index()
    {
        // Mengambil semua data dari tabel data
        $data = Data::all();

        // Menampilkan view data.blade.php dengan data yang diterima
        return view('admin.home.index', ['data' => $data]);
    }

    // mengambil semua data di table shop
    public function shop()
    {
        $data = Data::all();

        return view('admin.shop.index', ['data' => $data]);
    }


    public function getdata()
    {
        // Mengambil semua data dari tabel data
        $data = Data::all();

        // Mengembalikan response berupa data dalam format JSON
        return response()->json($data);
    }
}
