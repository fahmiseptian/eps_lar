<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Products;

class HomememberController extends Controller
{
    public function index() {
        $products= Products::getproduct();

        // return response()->json(["products"=>$products]);
        return view('member.home.index',["products"=>$products]);
    }

    public function getDetailproduct() {
        return view('member.home.detail');
    }
    
}