<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\AuthController;

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
    return view('welcome');
});

Route::get('/admin', function () {
    return view('admin/home/index');
});

// Admin list Invoice
Route::get('/admin/list-inv', [InvoiceController::class, 'list_inv'] , function () {
});
Route::get('/admin/list-cancelled-inv', [InvoiceController::class, 'inv_cancelled'] , function () {
});
Route::get('/admin/invoice/{id}', [InvoiceController::class, 'detail'])->name('admin.invoice.detail');


// Admin Shop
Route::get('/admin/shop', [ShopController::class, 'shop'] , function () {
});
Route::get('/admin/shop/{id}', [ShopController::class, 'detail'])->name('admin.shop.detail');
Route::get('/admin/shop/{id}/update-status', [ShopController::class, 'updateStatus'])->name('admin.shop.update-status');
Route::get('/admin/shop/{id}/delete', [ShopController::class, 'delete'])->name('admin.shop.delete');
Route::get('/admin/shop/{id}/update-type-up', [ShopController::class, 'updateTypeUp'])->name('admin.shop.update-type-up');
Route::get('/admin/shop/{id}/update-type-down', [ShopController::class, 'updateTypeDown'])->name('admin.shop.update-type-down');

// Admin Member
Route::get('/admin/member', [MemberController::class, 'index'])->name('admin.member.index');
Route::get('/admin/member/{id}', [MemberController::class, 'show'])->name('admin.member.show');
Route::get('/admin/member/{id}/toggle-status', [MemberController::class, 'toggleStatus'])->name('admin.member.toggle-status');
Route::get('/admin/member/{id}/delete', [MemberController::class, 'delete'])->name('admin.members.delete');

// login
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login']); 
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
