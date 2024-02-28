<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ShopController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/admin', function () {
    return view('admin/home/index');
});

// Admin list Invoice
Route::get('/admin/list-inv', [InvoiceController::class, 'list_inv'] , function () {
    return view('admin.invoice.index', ['data' => $data]);
});

// Admin Shop
Route::get('/admin/shop', [ShopController::class, 'shop'] , function () {
    return view('admin.shop.index', ['data' => $data]);
});

