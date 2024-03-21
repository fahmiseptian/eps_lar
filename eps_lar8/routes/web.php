<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\MenuController;


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

    // Admin Member
    Route::get('/admin/member', [MemberController::class, 'index'])->name('admin.member.index');
    Route::get('/admin/member/{id}', [MemberController::class, 'show'])->name('admin.member.show');
    Route::post('/admin/member/{id}/toggle-status', [MemberController::class, 'toggleStatus'])->name('admin.member.toggle-status');
    Route::get('/admin/member/{id}/delete', [MemberController::class, 'delete'])->name('admin.members.delete');
});

