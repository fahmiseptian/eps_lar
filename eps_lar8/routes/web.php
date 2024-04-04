<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Seller\LoginSellerController;
use App\Http\Controllers\Seller\HomesellerController;
use App\Http\Controllers\Seller\DeliveryController;
use App\Http\Controllers\Seller\OrederController;


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

Route::get('/', function () {
    return view('welcome');});

// login Admin
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Login Seller
Route::get('/seller/login', [LoginSellerController::class, 'showLoginForm'])->name('seller.login');
Route::post('/seller/login', [LoginSellerController::class, 'login']);
Route::get('/seller/logout', [LoginSellerController::class, 'logout'])->name('seller.logout');

// Routes Milik Admin
Route::group(['middleware' => 'admin'], function () {
    Route::get('/admin', [HomeController::class, 'index'])->name('admin');

    // membuat Menu
    Route::get('/admin/menu/create', [MenuController::class, 'create'])->name('admin.menu.create');
    Route::post('/admin/menu/store', [MenuController::class, 'store'])->name('admin.menu.store');

    // Admin list Invoice
    Route::get('/admin/list-inv', [InvoiceController::class, 'list_inv'])->name('admin.list-inv');
    Route::get('/admin/list-cancelled-inv', [InvoiceController::class, 'inv_cancelled'])->name('admin.list-cancelled-inv');
    Route::get('/admin/invoice/{id}', [InvoiceController::class, 'detail'])->name('admin.invoice.detail');

    // Admin Shop
    Route::get('/admin/shop', [ShopController::class, 'shop'])->name('admin.shop');
    Route::get('/admin/shop/lpse-config', [ShopController::class, 'lpse_config'])->name('admin.shop.lpse');
    Route::get('/admin/update-is-top/{id}', [ShopController::class, 'updateIsTop']);
    Route::get('/admin/shop/{id}', [ShopController::class, 'detail'])->name('admin.shop.detail');
    Route::post('/admin/update-formula', [ShopController::class, 'updateFormula']);
    Route::get('/admin/update-product-lpse/{id}', [ShopController::class, 'updateProduct']);
    Route::get('/admin/formula-lpse', [ShopController::class, 'formulaLpse'])->name('admin.formula-lpse');
    Route::get('/admin/shop/{id}/update-status', [ShopController::class, 'updateStatus']);
    Route::get('/admin/shop/{id}/delete', [ShopController::class, 'delete'])->name('admin.shop.delete');
    Route::get('/admin/shop/{id}/product', [ShopController::class, 'getProduct']);
    Route::get('/admin/shop/{id}/update-type-up', [ShopController::class, 'updateTypeUp'])->name('admin.shop.update-type-up');
    Route::get('/admin/shop/{id}/update-type-down', [ShopController::class, 'updateTypeDown'])->name('admin.shop.update-type-down');

    //Admin Brand 
    Route::get('/admin/brand', [BrandController::class, 'index'])->name('admin.brand');

    // Admin Member
    Route::get('/admin/member', [MemberController::class, 'index'])->name('admin.member.index');
    Route::get('/admin/member/{id}', [MemberController::class, 'show'])->name('admin.member.show');
    Route::post('/admin/member/{id}/toggle-status', [MemberController::class, 'toggleStatus'])->name('admin.member.toggle-status');
    Route::get('/admin/member/{id}/delete', [MemberController::class, 'delete'])->name('admin.members.delete');
});


Route::group(['middleware' => 'seller'], function () {
    Route::get('/seller', [HomesellerController::class, 'index'])->name('seller');

    // pengiriman
    Route::get('/seller/delivery', [DeliveryController::class, 'pengaturan_jasa'])->name('seller.delivery');
    Route::get('/seller/delivery/free', [DeliveryController::class, 'pengaturan_free'])->name('seller.delivery.free-ongkir');
    Route::get('/seller/add-courier', [DeliveryController::class, 'addCourier']);
    Route::get('/seller/remove-courier', [DeliveryController::class, 'removeCourier']);
    Route::get('/seller/add-free-courier', [DeliveryController::class, 'addfreeCourier']);
    Route::get('/seller/remove-free-courier', [DeliveryController::class, 'removefreeCourier']);

    // order
    Route::get('/seller/order', [OrederController::class, 'index'])->name('seller.order');
    Route::post('/seller/order/accept', [OrederController::class, 'acceptOrder']);
    Route::post('/seller/order/cancel', [OrederController::class, 'cancelOrder']);
    Route::get('/seller/order/detail/{id_cart_shop}', [OrederController::class, 'detailOrder'])->name('seller.order.detail');
    Route::get('/seller/order/filter/{status_order}', [OrederController::class, 'filterOrder'])->name('seller.order.filter');
});