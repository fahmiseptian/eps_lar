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
use App\Http\Controllers\Seller\FinanceController;
use App\Http\Controllers\Seller\OrederController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\seller\ShophealthController;
use Illuminate\Support\Facades\Artisan;

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
    
// Route::get('/foo', function () {
//     Artisan::call('storage:link');
// });

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

    // pengiriman
    Route::get('/seller/delivery', [DeliveryController::class, 'pengaturan_jasa'])->name('seller.delivery');
    Route::get('/seller/delivery/free', [DeliveryController::class, 'pengaturan_free'])->name('seller.delivery.free-ongkir');
    Route::post('/seller/add-courier', [DeliveryController::class, 'addCourier']);
    Route::post('/seller/remove-courier', [DeliveryController::class, 'removeCourier']);
    Route::get('/seller/add-free-courier', [DeliveryController::class, 'addfreeCourier']);
    Route::get('/seller/remove-free-courier', [DeliveryController::class, 'removefreeCourier']);

    // order
    Route::get('/seller/order', [OrederController::class, 'index'])->name('seller.order');
    Route::post('/seller/order/accept', [OrederController::class, 'acceptOrder']);
    Route::post('/seller/order/cancel', [OrederController::class, 'cancelOrder']);
    Route::get('/seller/order/detail/{id_cart_shop}', [OrederController::class, 'detailOrder'])->name('seller.order.detail');
    Route::get('/seller/order/filter/{status_order}', [OrederController::class, 'filterOrder'])->name('seller.order.filter');

    // Product
    Route::get('/seller/product/', [ProductController::class, 'index'])->name('seller.product');
    Route::get('/seller/product/violation', [ProductController::class, 'showViolation'])->name('seller.product.violation');
    Route::get('/seller/product/add', [ProductController::class, 'showaddProduct'])->name('seller.product.add');
    Route::post('/seller/product/addProduct', [ProductController::class, 'addProduct'])->name('seller.product.addProduct');
    Route::get('/seller/product/{status}', [ProductController::class, 'filterProduct'])->name('seller.product.filter');
    Route::get('/seller/product/category/level2/{id_level1}', [ProductController::class, 'getCategoryLevel2']);

    // Finance
    Route::get('/seller/finance/', [FinanceController::class, 'index'])->name('seller.finance');
    Route::get('/seller/finance/saldo', [FinanceController::class, 'showSaldo'])->name('seller.finance.saldo');
    Route::get('/seller/finance/rekening', [FinanceController::class, 'showRekening'])->name('seller.finance.rekening');
    Route::get('/seller/finance/pembayaran', [FinanceController::class, 'showPembayaran'])->name('seller.finance.pembayaran');
    Route::get('/seller/finance/getRekening/{id}', [FinanceController::class, 'getRekeningById']);
    Route::post('/seller/finance/updateRekening', [FinanceController::class, 'updateRekening']);
    Route::post('/seller/finance/deleteRekening/{id}', [FinanceController::class, 'deleteRekening']);
    Route::post('/seller/finance/addRekening', [FinanceController::class, 'addRekening']);
    Route::post('/seller/finance/updateDefaultRekening', [FinanceController::class, 'updateDefaultRekening']);
    Route::post('/seller/finance/sendVerificationCode', [FinanceController::class, 'sendVerificationCode']);
    Route::post('/seller/finance/verifyCode', [FinanceController::class, 'verifyCode']);
    Route::post('/seller/finance/updateNewPin', [FinanceController::class, 'updatePin']);

    // Shop health
    Route::get('/seller/health/', [ShophealthController::class, 'index'])->name('seller.health');

    // Pengetesan
    Route::get('/seller/product/test/{id}', [ProductController::class, 'test']); //Test Get Json Product
    Route::get('/seller/product/la/product/{id}', [ProductController::class, 'productAll']);
    Route::get('/seller/product/get/product/{id}', [ProductController::class, 'addOldProduct']); //ngambil Data Product di table yang lama
});

