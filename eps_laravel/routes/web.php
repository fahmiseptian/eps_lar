<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ShopController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AdminController::class, 'index'] , function () {
    return view('admin.home.index', ['data' => $data]);
});


// Admin Shop
Route::get('/shop', [ShopController::class, 'shop'] , function () {
    return view('admin.shop.index', ['data' => $data]);
});
