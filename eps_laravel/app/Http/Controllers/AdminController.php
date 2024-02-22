<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;

class AdminController extends Controller
{
    public function index()
    {
        // Mengambil semua data dari tabel data
        $data = Admin::all();

        // Menampilkan view data.blade.php dengan data yang diterima
        return view('admin.home.index', ['data' => $data]);
    }

    // mengambil semua data di table shop
    public function shop()
    {
        $data = Admin::all();

        return view('admin.shop.index', ['data' => $data]);
    }


    public function getdata()
    {
        // Mengambil semua data dari tabel data
        $data = Admin::all();

        // Mengembalikan response berupa data dalam format JSON
        return response()->json($data);
    }
}
