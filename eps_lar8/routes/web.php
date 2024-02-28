<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\MemberController;

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
    return view('admin.invoice.index', ['data' => $data]);
});

// Admin Shop
Route::get('/admin/shop', [ShopController::class, 'shop'] , function () {
    return view('admin.shop.index', ['data' => $data]);
});

// Menampilkan semua anggota
Route::get('/admin/member', [MemberController::class, 'index'])->name('admin.member.index');

// Menampilkan detail anggota
Route::get('/admin/member/{id}', [MemberController::class, 'show'])->name('admin.member.show');

// Toggle status anggota (active/suspend)
Route::get('/admin/member/{id}/toggle-status', [MemberController::class, 'toggleStatus'])->name('admin.member.toggle-status');

// Menghapus anggota
Route::get('/admin/member/{id}/delete', [MemberController::class, 'delete'])->name('admin.members.delete');

