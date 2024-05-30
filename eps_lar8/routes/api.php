<?php

use App\Http\Controllers\Member\CartController;
use App\Http\Controllers\Member\HomememberController;
use App\Http\Controllers\Member\LoginmemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/kategori/{id}', [HomememberController::class, 'GetKategoryProductByIdshop']);
Route::get('/kategoriProduct/{id_ketegori}/{id_shop}', [HomememberController::class, 'GetProductByKategoriandIdShop']);

Route::post('/login/getinstansi', [LoginmemberController::class, 'get_instansi']);
Route::post('/login/getsatker', [LoginmemberController::class, 'get_satker']);
Route::post('/login/getbidang', [LoginmemberController::class, 'get_bidang']);

Route::get('/dashboard', [HomememberController::class, 'dashboard']);
Route::get('/transaksi/{kondisi}', [HomememberController::class, 'transaksi']);

Route::group(['middleware' => 'member'], function () {
    
Route::post('/update-quantity', [CartController::class, 'updateQuantity']);
Route::post('/add-cart', [CartController::class, 'addCart']);
Route::delete('/cart/{id_temporary}/{id_shop}', [CartController::class, 'deleteCart']);
Route::get('/member/getaddress', [CartController::class, 'getaddress']);
Route::get('/updateAddressCart/{member_address_id}', [CartController::class, 'updateAddressCart']);
Route::get('/shipping/{id_shipping}/{id_cs}', [CartController::class, 'getOngkir']);
Route::get('/insurance/{id_shop}/{id_courier}/{idcs}/{status}', [CartController::class, 'insurance']);
});