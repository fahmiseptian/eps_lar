<?php

use App\Http\Controllers\Member\CartController;
use App\Http\Controllers\Member\CheckoutController;
use App\Http\Controllers\Member\HomememberController;
use App\Http\Controllers\Member\LoginmemberController;
use App\Http\Controllers\Partner\BniController;
use App\Http\Controllers\Seller\DeliveryController;
use App\Http\Controllers\Seller\FinanceController;
use App\Http\Controllers\Seller\NegoController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\PromotionController;
use App\Models\ProductCategory;
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

Route::post('/bni/hit', [BniController::class, 'apiHiting']);
Route::post('/bni/create-billing', [BniController::class, 'createBilling']);
Route::post('/bni/inquiry-billing', [BniController::class, 'inquiryBilling']);
Route::post('/bni/update-billing', [BniController::class, 'updateBilling']);
Route::post('/bni/payment-notification', [BniController::class, 'transactionPaymentNotification']);

Route::get('/bni/test/{data}', [BniController::class, 'descrypt_bni']);

Route::get('/kategori/{id}', [HomememberController::class, 'GetKategoryProductByIdshop']);
Route::get('/kategoriProduct/{id_ketegori}/{id_shop}', [HomememberController::class, 'GetProductByKategoriandIdShop']);

Route::post('/login/getinstansi', [LoginmemberController::class, 'get_instansi']);
Route::post('/login/getsatker', [LoginmemberController::class, 'get_satker']);
Route::post('/login/getbidang', [LoginmemberController::class, 'get_bidang']);

Route::get('/dashboard', [HomememberController::class, 'dashboard']);
Route::get('/transaksi/{kondisi}', [HomememberController::class, 'transaksi']);

// Seller
Route::group(['middleware' => 'seller'], function () {
// Peritungan
    Route::post('/seller/calcHargaTayang', [ProductController::class, 'calcHargaTayang']); //Butuh id_product dan harga_input

// pemgiriman
    Route::get('/seller/get-packingDay', [DeliveryController::class, 'get_packingDay']);
    Route::post('/seller/update-packingDay', [DeliveryController::class, 'update_packingDay']);

// product
    Route::post('/seller/deleteProduct', [ProductController::class, 'deleteProduct']);
    Route::post('/seller/editStatusProduct', [ProductController::class, 'editStatusProduct']);
    Route::get('/seller/DetailCategory/{id}', [ProductController::class, 'CheckDetailCategory']);
    Route::get('/seller/product/', [ProductController::class, 'getProductSeller']);
    Route::get('/seller/product/price/{id}', [ProductController::class, 'getPrice']);

// Finance
    Route::post('/seller/finance/updatePin',[FinanceController::class,'savePin']);
    Route::get('/seller/finance/getTraxPending',[FinanceController::class,'getTraxPending']);
    Route::post('/seller/finance/requestrevenue',[FinanceController::class,'RequestRevenue']);

// Nego
    Route::get('/seller/nego/{kondisi}', [NegoController::class, 'GetDataNego']);
    Route::get('/seller/nego/detail/{id}', [NegoController::class, 'getDetailNego']);
    Route::post('/seller/calcNego',[NegoController::class,'calcNego']);
    Route::post('/seller/nego/add_respon',[NegoController::class,'add_respon']);
    Route::post('/seller/nego/acc_nego',[NegoController::class,'acc_nego']);
    Route::post('/seller/nego/tolak_nego',[NegoController::class,'tolak_nego']);

    Route::get('/seller/nego/Test_nego/{id}',[NegoController::class,'Test_nego']);

// Promo
    Route::post('/seller/promo/product',[PromotionController::class,'getProductPromo']);
    Route::get('/seller/kategoripromo/', [PromotionController::class, 'getKategoriPromo']);
    Route::post('/seller/promo/add-promo',[PromotionController::class,'addPromotionProduct']);
    Route::post('/seller/promo/delete-promo',[PromotionController::class,'deleteProductPromo']);

});

// Member
Route::group(['middleware' => 'member'], function () {
Route::post('/update-quantity', [CartController::class, 'updateQuantity']);
Route::post('/add-cart', [CartController::class, 'addCart']);
Route::get('/update-top/{top}', [CartController::class, 'updateTOP']);
Route::post('/update-payment', [CartController::class, 'updatePayment']);
Route::delete('/cart/{id_temporary}/{id_shop}', [CartController::class, 'deleteCart']);
Route::get('/member/getaddress', [CartController::class, 'getaddress']);
Route::get('/updateAddressCart/{member_address_id}', [CartController::class, 'updateAddressCart']);
Route::get('/shipping/{id_shipping}/{id_cs}', [CartController::class, 'getOngkir']);
Route::get('/insurance/{id_shop}/{id_courier}/{idcs}/{status}', [CartController::class, 'insurance']);
Route::get('/getorder/{id_cart}',[CheckoutController::class,'getOrder']);
Route::post('/upload-payment',[CheckoutController::class,'uploadPayment']);
Route::post('/finishCheckout',[CartController::class,'finish_checkout']);
Route::post('/sumbitBast',[CheckoutController::class,'sumbit_bast']);
Route::get('/lacak_pengiriman/{id_seller}/{id_cart_shop}',[CheckoutController::class,'lacak_pengiriman']);
Route::get('/get_detail_transaksi/{id_shop}/{id_cart_shop}',[CheckoutController::class,'get_detail_product']);
Route::post('/updateIsSelectProduct',[CartController::class,'updateIsSelectProduct']);
Route::post('/updateqtyCart',[CartController::class,'updateqtyCart']);
// Route::get('/bni/create-billing', [BniController::class, 'createBilling']);

});
