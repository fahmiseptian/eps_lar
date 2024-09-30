<?php

use App\Http\Controllers\Member\CartController;
use App\Http\Controllers\Member\CheckoutController;
use App\Http\Controllers\Member\HomememberController;
use App\Http\Controllers\Member\LoginmemberController;
use App\Http\Controllers\Member\ProfilememberController;
use App\Http\Controllers\Partner\BniController;
use App\Http\Controllers\Partner\KurirController;
use App\Http\Controllers\Member\SearchController;
use App\Http\Controllers\Seller\DeliveryController;
use App\Http\Controllers\Seller\FinanceController;
use App\Http\Controllers\Seller\LoginSellerController;
use App\Http\Controllers\Seller\NegoController;
use App\Http\Controllers\Seller\OrederController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\PromotionController;
use App\Http\Controllers\Seller\SettingController;
use App\Http\Controllers\Seller\ShophealthController;
use App\Http\Controllers\Seller\ShopSettingController;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

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
Route::get('/quick-search', [SearchController::class, 'quickSearch'])->name('quick.search');
Route::get('/refresh-hits', [HomememberController::class, 'refreshHits']);
Route::get('/transaksi/{kondisi}', [HomememberController::class, 'transaksi']);
Route::post('/filter-searching', [SearchController::class, 'filterSearching']);
Route::get('/more-product', [SearchController::class, 'more_product']);
Route::get('/shop/search-product', [SearchController::class, 'filterProductwithIdshop']);

// For Login Seller
Route::get('/getShop/kategori', [LoginSellerController::class, 'getKategori']);
Route::post('/register', [LoginSellerController::class, 'register']);

// Seller
Route::group(['middleware' => 'seller'], function () {
    // Peritungan
    Route::post('/seller/calcHargaTayang', [ProductController::class, 'calcHargaTayang']); //Butuh id_product dan harga_input
    Route::post('/seller/calcHarga', [ProductController::class, 'calcHarga']);

    // pemgiriman
    Route::get('/seller/get-packingDay', [DeliveryController::class, 'get_packingDay']);
    Route::post('/seller/update-packingDay', [DeliveryController::class, 'update_packingDay']);
    Route::get('/seller/delivery/jasa-ongkir', [DeliveryController::class, 'jasaPengiriman']);
    Route::get('/seller/delivery/free-ongkir', [DeliveryController::class, 'freePengiriman']);

    // product
    Route::post('/seller/deleteProduct', [ProductController::class, 'deleteProduct']);
    Route::post('/seller/editStatusProduct', [ProductController::class, 'editStatusProduct']);
    Route::get('/seller/DetailCategory/{id}', [ProductController::class, 'CheckDetailCategory']);
    Route::get('/seller/product/', [ProductController::class, 'getProductSeller']);
    Route::get('/seller/product/price/{id}', [ProductController::class, 'getPrice']);
    Route::get('/seller/product/satuan', [ProductController::class, 'getsatuanProduk']);
    Route::get('/seller/product/brands', [ProductController::class, 'getBrands']);
    Route::get('/seller/product/getCategorylv1', [ProductController::class, 'getCategorylv1']);
    Route::get('/seller/product/getCategorylv2/{id_level1}', [ProductController::class, 'getCategoryLevel2']);
    Route::post('/seller/product/save', [ProductController::class, 'addProduct']);
    Route::post('/seller/product/update', [ProductController::class, 'updateProduct']);

    // Order
    Route::post('/seller/getKontrak', [OrederController::class, 'getKontrak']);
    Route::post('/seller/getSuratPesanan', [OrederController::class, 'getSuratPesanan']);
    Route::get('/seller/getOrder/{id}', [OrederController::class, 'getorder']);
    Route::get('/seller/getSP/{id}', [OrederController::class, 'getSP']);
    Route::get('/seller/detail/{id}', [OrederController::class, 'detailOrder']);
    Route::post('/seller/order/upload-faktur', [OrederController::class, 'uploadFaktur']);

    // Finance
    Route::post('/seller/finance/updatePin', [FinanceController::class, 'savePin']);
    Route::get('/seller/finance/getTraxPending', [FinanceController::class, 'getTraxPending']);
    Route::post('/seller/finance/requestrevenue', [FinanceController::class, 'RequestRevenue']);
    Route::get('/seller/finance/penghasilan', [FinanceController::class, 'showPenghasilan']);
    Route::get('/seller/finance/sudah_dilepas', [FinanceController::class, 'Successsaldo']);
    Route::get('/seller/finance/saldo', [FinanceController::class, 'PenarikanDana']);
    Route::get('/seller/finance/rekening', [FinanceController::class, 'rekeningNotdefault']);
    Route::get('/seller/finance/getbank', [FinanceController::class, 'getbank']);
    Route::get('/seller/finance/detailPenarikan/{id}', [FinanceController::class, 'getDetailPenarikan']);

    // Nego
    Route::get('/seller/nego/{kondisi}', [NegoController::class, 'GetDataNego']);
    Route::get('/seller/nego/detail/{id}', [NegoController::class, 'getDetailNego']);
    Route::post('/seller/calcNego', [NegoController::class, 'calcNego']);
    Route::post('/seller/nego/add_respon', [NegoController::class, 'add_respon']);
    Route::post('/seller/nego/acc_nego', [NegoController::class, 'acc_nego']);
    Route::post('/seller/nego/tolak_nego', [NegoController::class, 'tolak_nego']);

    Route::get('/seller/nego/Test_nego/{id}', [NegoController::class, 'Test_nego']);

    // Promo
    Route::post('/seller/promo/product', [PromotionController::class, 'getProductPromo']);
    Route::get('/seller/kategoripromo/', [PromotionController::class, 'getKategoriPromo']);
    Route::post('/seller/promo/add-promo', [PromotionController::class, 'addPromotionProduct']);
    Route::post('/seller/promo/delete-promo', [PromotionController::class, 'deleteProductPromo']);

    // Setting
    Route::get('/seller/setting/address', [SettingController::class, 'getaddress']);
    Route::get('/seller/setting/toko', [SettingController::class, 'getOprasional']);
    Route::post('/seller/setting/update/oprasional', [SettingController::class, 'updateOprasional']);
    Route::post('/seller/setting/update/libur', [SettingController::class, 'updateConfig_cuti']);
    Route::post('/seller/setting/address/setdefault', [SettingController::class, 'setDefaultAddress']);
    Route::post('/seller/setting/address/delete', [SettingController::class, 'deleteAddress']);
    Route::post('/seller/setting/address/getAddress', [SettingController::class, 'getDetailAddress']);
    Route::post('/seller/setting/address/update', [SettingController::class, 'addAddress']);

    // Health
    Route::get('/seller/info/health', [ShophealthController::class, 'get_health']);
    Route::get('/seller/info/faq', [ShophealthController::class, 'faq']);
    Route::get('/seller/info/toko', [ShophealthController::class, 'get_informasi_toko']);
    Route::get('/seller/info/rank-produk/{status}', [ShophealthController::class, 'get_rank_produk']);
    Route::get('/seller/info/rank-kategori', [ShophealthController::class, 'get_rank_category']);

    // Toko
    Route::get('/seller/toko/rates_shop', [ShopSettingController::class, 'index']);
    Route::get('/seller/toko/getRate/{rating}', [ShopSettingController::class, 'getRate']);
    Route::get('/seller/toko/a_chat', [ShopSettingController::class, 'a_chat']);
    Route::post('/seller/toko/updateReplyStatus', [ShopSettingController::class, 'updateReplyChat']);
    Route::get('/seller/toko/profile', [ShopSettingController::class, 'get_profile']);
    Route::get('/seller/toko/etalase', [ShopSettingController::class, 'get_etalase']);
    Route::post('/seller/toko/UpdateEtalase', [ShopSettingController::class, 'UpdateEtalase']);
    Route::post('/seller/toko/updateProfile', [ShopSettingController::class, 'updateProfile']);
    Route::post('/seller/toko/updatePassword', [ShopSettingController::class, 'updatePassword']);
    Route::post('/seller/toko/UploadFile', [ShopSettingController::class, 'UploadFile']);
    Route::post('/seller/toko/UplaodBanner', [ShopSettingController::class, 'UplaodBanner']);
    Route::delete('/seller/toko/deleteBanner', [ShopSettingController::class, 'deleteBanner']);
    Route::post('/seller/toko/updateProfileSeller', [ShopSettingController::class, 'updateProfileSeller']);
    Route::delete('/seller/toko/delete-etalase', [ShopSettingController::class, 'deleteEtalase']);
    Route::post('/seller/toko/tambahEtalase', [ShopSettingController::class, 'addEtalase']);
});

// Document
// Route::get('/generate-pdf', [PDFController::class, 'index'])->name('generate.pdf.form');
Route::post('/generate-kontrak', [OrederController::class, 'generateKontrak'])->name('generate.kontrak');
Route::post('/download-kontrak', [OrederController::class, 'downloadKontrak'])->name('download.kontrak');
Route::post('/generate-sp', [OrederController::class, 'generateSp'])->name('generate.sp');
Route::post('/download-sp', [OrederController::class, 'downloadSp'])->name('download.sp');


// Member
Route::group(['middleware' => 'member'], function () {
    Route::post('/update-quantity', [CartController::class, 'updateQuantity']);
    Route::post('/cart/update-quantity', [CartController::class, 'updateProductCart']);
    Route::post('/add-cart', [CartController::class, 'addCart']);
    Route::get('/update-top/{top}', [CartController::class, 'updateTOP']);
    Route::post('/update-payment', [CartController::class, 'updatePayment']);
    Route::delete('/cart/{id_temporary}/{id_shop}', [CartController::class, 'deleteCart']);
    Route::get('/member/getaddress', [CartController::class, 'getaddress']);
    Route::get('/updateAddressCart/{member_address_id}', [CartController::class, 'updateAddressCart']);
    Route::get('/shipping/{id_shipping}/{id_cs}', [CartController::class, 'getOngkir']);
    Route::get('/insurance/{id_shop}/{id_courier}/{idcs}/{status}', [CartController::class, 'insurance']);
    Route::get('/getorder/{id_cart}', [CheckoutController::class, 'getOrder']);
    Route::post('/upload-payment', [CheckoutController::class, 'uploadPayment']);
    Route::post('/storeKontrak', [ProfilememberController::class, 'storeKontrak']);
    Route::post('/storeSuratPesanan', [ProfilememberController::class, 'storeSuratPesanan']);
    Route::post('/finishCheckout', [CartController::class, 'finish_checkout']);
    Route::post('/sumbitBast', [CheckoutController::class, 'sumbit_bast']);
    Route::get('/lacak_pengiriman/{id_seller}/{id_cart_shop}', [CheckoutController::class, 'lacak_pengiriman']);
    Route::get('/get_detail_transaksi/{id_shop}/{id_cart_shop}', [CheckoutController::class, 'get_detail_product']);
    Route::post('/updateIsSelectProduct', [CartController::class, 'updateIsSelectProduct']);
    Route::post('/update-product-selection/shop', [CartController::class, 'updateIsSelectShop']);
    Route::post('/updateqtyCart', [CartController::class, 'updateqtyCart']);
    Route::post('/member/storeAddress', [ProfilememberController::class, 'storeAddress']);
    Route::post('/member/update-Address', [ProfilememberController::class, 'UpdateAddress']);
    Route::post('/member/nego/accNego', [ProfilememberController::class, 'accNego']);
    Route::post('/member/nego/tolak_nego', [ProfilememberController::class, 'tolak_nego']);
    Route::post('/member/nego/reqNego', [ProfilememberController::class, 'addRequestNego']);
    // Route::get('/bni/create-billing', [BniController::class, 'createBilling']);

});

// kurir
Route::post('/kurir/anter', [KurirController::class, 'anter']);
Route::post('/kurir/pickup', [KurirController::class, 'pickup']);
Route::post('/kurir/tracking', [KurirController::class, 'Tracking']);
Route::post('/kurir/view-tracking', [KurirController::class, 'trackingReturnView']);

Route::get('/config/getProvince', [LoginSellerController::class, 'getProvince']);
Route::get('/config/getCity/{id}', [LoginSellerController::class, 'getCity']);
Route::get('/config/getdistrict/{id}', [LoginSellerController::class, 'getdistrict']);

Route::get('/keranjang/{id_member}', [HomememberController::class, 'keranjang']);


// calc
Route::post('/calc_nego', [HomememberController::class, 'calc_nego']);