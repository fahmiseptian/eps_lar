<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Seller\LoginSellerController;
use App\Http\Controllers\Seller\HomesellerController;
use App\Http\Controllers\Seller\DeliveryController;

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

    // Admin Menu
    Route::get('/admin/menu/', [MenuController::class, 'menu'])->name('admin.menu');
    Route::get('/admin/menu/create', [MenuController::class, 'create'])->name('admin.menu.create');
    Route::post('/admin/menu/store', [MenuController::class, 'store'])->name('admin.menu.store');
    Route::get('/admin/menu/{id}', [MenuController::class, 'detail'])->name('admin.menu.detail');
    Route::post('/admin/menu/{id}/edit', [MenuController::class, 'edit'])->name('admin.menu.edit');
    Route::get('/admin/menu/{id}/delete', [MenuController::class, 'delete'])->name('admin.menu.delete');

    // Admin User
    Route::get('/admin/user/', [UserController::class, 'user'])->name('admin.user');
    Route::get('/admin/user/add', [UserController::class, 'add'])->name('admin.user.add');
    Route::post('/admin/user/store', [UserController::class, 'store'])->name('admin.user.store');
    Route::get('/admin/user/{id}', [UserController::class, 'detail'])->name('admin.user.detail');
    Route::post('/admin/user/{id}/edit', [UserController::class, 'edit'])->name('admin.user.edit');
    Route::get('/admin/user/{id}/delete', [UserController::class, 'delete'])->name('admin.user.delete');

    // Admin Access
    Route::post('/admin/access/add', [UserController::class, 'add_access'])->name('admin.access.add');
    Route::get('/admin/access/available/{id}', [UserController::class, 'getAvailableAccess'])->name('admin.access.available');
    Route::post('/admin/access/{id}/edit', [UserController::class, 'edit_access'])->name('admin.access.edit');
    Route::post('/admin/access/{id}/delete', [UserController::class, 'delete_access'])->name('admin.access.delete');

    // Admin list Invoice
    Route::get('/admin/list-inv', [InvoiceController::class, 'list_inv'])->name('admin.list-inv');
    Route::get('/admin/list-cancelled-inv', [InvoiceController::class, 'inv_cancelled'])->name('admin.list-cancelled-inv');
    Route::get('/admin/invoice/{id}', [InvoiceController::class, 'detail'])->name('admin.invoice.detail');
    Route::post('/admin/invoice/upload/cancel/{id}', [InvoiceController::class, 'upload_cancel'])->name('admin.invoice.upload.cancel');
    Route::post('/admin/invoice/reupload/cancel/{id}', [InvoiceController::class, 'reupload_cancel'])->name('admin.invoice.reupload.cancel');
    Route::get('/admin/invoice/view/cancel/{id}', [InvoiceController::class, 'view_cancel'])->name('admin.invoice.view.cancel');

    // Admin Shop
    Route::get('/admin/shop', [ShopController::class, 'shop'])->name('admin.shop');
    Route::get('/admin/shop/lpse-config', [ShopController::class, 'lpse_config'])->name('admin.shop.lpse');
    Route::get('/admin/update-is-top/{id}', [ShopController::class, 'updateIsTop']);
    Route::get('/admin/shop/{id}', [ShopController::class, 'detail'])->name('admin.shop.detail');
    Route::post('/admin/update-formula', [ShopController::class, 'updateFormula']);
    Route::get('/admin/update-product-lpse/{id}', [ShopController::class, 'updateProduct']);
    Route::get('/admin/formula-lpse', [ShopController::class, 'formulaLpse'])->name('admin.formula-lpse');
    Route::get('/admin/shop/{id}/update-status', [ShopController::class, 'updateStatus'])->name('admin.shop.update');
    Route::get('/admin/shop/{id}/delete', [ShopController::class, 'delete'])->name('admin.shop.delete');
    Route::get('/admin/shop/{id}/product', [ShopController::class, 'getProduct']);
    Route::get('/admin/shop/{id}/update-type-up', [ShopController::class, 'updateTypeUp'])->name('admin.shop.update-type-up');
    Route::get('/admin/shop/{id}/update-type-down', [ShopController::class, 'updateTypeDown'])->name('admin.shop.update-type-down');

    //Admin Brand 
    Route::get('/admin/brand', [BrandController::class, 'index'])->name('admin.brand');

    //Admin Payment 
    Route::get('/admin/payment', [PaymentController::class, 'payment'])->name('admin.payment');
    Route::post('/admin/payment/add', [PaymentController::class, 'add'])->name('admin.payment.add');
    Route::get('/admin/payment/{id}', [PaymentController::class, 'detail'])->name('admin.payment.detail');
    Route::get('/admin/payment/{id}/status', [PaymentController::class, 'status'])->name('admin.payment.status');
    Route::post('/admin/payment/{id}/edit', [PaymentController::class, 'edit'])->name('admin.payment.edit');
    Route::get('/admin/payment/{id}/delete', [PaymentController::class, 'delete'])->name('admin.payment.delete');

    // Admin Member
    Route::get('/admin/member', [MemberController::class, 'index'])->name('admin.member.index');
    Route::get('/admin/member/{id}', [MemberController::class, 'show'])->name('admin.member.show');
    Route::post('/admin/member/{id}/toggle-status', [MemberController::class, 'toggleStatus'])->name('admin.member.toggle-status');
    Route::get('/admin/member/{id}/delete', [MemberController::class, 'delete'])->name('admin.members.delete');
});


Route::group(['middleware' => 'seller'], function () {
    Route::get('/seller', [HomesellerController::class, 'index'])->name('seller');
    Route::get('/seller/delivery', [DeliveryController::class, 'pengaturan_jasa'])->name('seller.delivery');
    Route::get('/seller/delivery/free', [DeliveryController::class, 'pengaturan_free'])->name('seller.delivery.free-ongkir');
});