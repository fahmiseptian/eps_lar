<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Shop;

class ShopController extends Controller
{
    public function shop()
    {
        $data = Shop::all();

        return view('admin.shop.index', ['data' => $data]);
    }
}
