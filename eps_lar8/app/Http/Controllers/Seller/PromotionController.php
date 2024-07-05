<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Libraries\Calculation;
use App\Models\Cart;
use App\Models\Lpse_config;
use App\Models\Nego;
use App\Models\ProductCategory;
use App\Models\Promo_category;
use App\Models\Promoproduct;
use App\Models\Saldo;
use App\Models\Shop;
use App\Models\ShopCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{

    protected $user_id;
    protected $username;
    protected $seller;
    protected $Model;
    protected $Libraries;
    protected $data;
    protected $menu;

    public function __construct(Request $request)
    {
        $this->seller 	= $request->session()->get('seller_id');
        $sellerType     = Shop::getTypeById($this->seller);
        $saldoPending   = Saldo::calculatePendingSaldo($this->seller);

        // Membuat $this->data
        $this->data['title'] = 'Promosi Produk';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldoPending;

        // Model
        $this->Model['Shop'] = new Shop();
        $this->Model['Nego'] = new Nego();
        $this->Model['Cart'] = new Cart();
        $this->Model['PromoCategory'] = new Promo_category();
        $this->Model['Promoproduct'] = new Promoproduct();

        // Libraries
        $this->Libraries['Calculation'] = new Calculation();
    }

    public function index()
    {
        $promotions = $this->Model['PromoCategory']->getPromoCategory();
        $products   = $this->Model['Promoproduct']->getProductPromobyId_shop($this->seller,null);
        return view('seller.promosi.index',$this->data,['promotions' => $promotions, 'products' => $products]);
    }

    function getProductPromo(Request $request){
        $id_category_promo  = $request->id;

        if ($id_category_promo == 0) {
            $products   = $this->Model['Promoproduct']->getProductPromobyId_shop($this->seller,null);
        }else {
            $products   = $this->Model['Promoproduct']->getProductPromobyId_shop($this->seller,$id_category_promo);
        }

        return response()->json(['products' => $products]);
    }

    function getKategoriPromo(){
        $promotions = $this->Model['PromoCategory']->getPromoCategory();
        return response()->json(['promotions' => $promotions]);
    }

    public function addPromotionProduct(Request $request)
    {
        $id_category_promo = $request->id_category;
        $id_product = $request->id_product;
        $promo_origin = $request->promo_origin;
        $promo_price = $request->promo_price;

        // Check apakah promo untuk produk ini sudah ada
        $existingPromo = DB::table('promo_product')
            ->where('id_product', $id_product)
            ->where('id_shop', $this->seller)
            ->first();

        if ($existingPromo) {
            try {
                // Jika promo sudah ada, lakukan update
                DB::table('promo_product')
                    ->where('id', $existingPromo->id)
                    ->update([
                        'id_category_promo' => $id_category_promo,
                        'promo_origin' => $promo_origin,
                        'promo_price' => $promo_price,
                        'last_update' => Carbon::now(),
                    ]);

                // Update juga ke tabel promo_product_log
                DB::table('promo_product_log')->insert([
                    'id_shop' => $this->seller,
                    'id_category_promo' => $id_category_promo,
                    'id_product' => $id_product,
                    'promo_origin' => $promo_origin,
                    'promo_price' => $promo_price,
                    'is_active' => 'Y',
                    'created_dt' => Carbon::now(),
                    'last_update' => Carbon::now(),
                ]);

                // Berhasil update promo produk, kirim respons OK (200)
                return response()->json(['message' => 'Promo produk berhasil diupdate'], 200);
            } catch (\Exception $e) {
                // Gagal mengupdate, kirim respons error (500) dengan pesan error
                return response()->json(['message' => 'Gagal mengupdate promo produk: ' . $e->getMessage()], 500);
            }
        } else {
            // Jika promo belum ada, lakukan insert baru
            try {
                // Simpan ke tabel promo_product
                DB::table('promo_product')->insert([
                    'id_shop' => $this->seller,
                    'id_category_promo' => $id_category_promo,
                    'id_product' => $id_product,
                    'promo_origin' => $promo_origin,
                    'promo_price' => $promo_price,
                    'is_active' => 'Y',
                    'created_dt' => Carbon::now(),
                    'last_update' => Carbon::now(),
                ]);

                // Simpan ke tabel promo_product_log
                DB::table('promo_product_log')->insert([
                    'id_shop' => $this->seller,
                    'id_category_promo' => $id_category_promo,
                    'id_product' => $id_product,
                    'promo_origin' => $promo_origin,
                    'promo_price' => $promo_price,
                    'is_active' => 'Y',
                    'created_dt' => Carbon::now(),
                    'last_update' => Carbon::now(),
                ]);

                // Berhasil menambahkan promo produk baru, kirim respons OK (200)
                return response()->json(['message' => 'Promo produk berhasil ditambahkan'], 200);
            } catch (\Exception $e) {
                // Gagal menyimpan, kirim respons error (500) dengan pesan error
                return response()->json(['message' => 'Gagal menambahkan promo produk: ' . $e->getMessage()], 500);
            }
        }
    }


    function deleteProductPromo(Request $request) {
        $id = $request->id;

        // Menggunakan Eloquent untuk menghapus data
        try {
            $promoProduct = PromoProduct::where('id', $id)
                                        ->where('id_shop', $this->seller)
                                        ->delete();

            DB::table('promo_product_log')->where('id',$id)->update([
                'is_active' => 'N',
                'last_update' => Carbon::now(),
            ]);

            if ($promoProduct) {
                return response()->json(['message' => 'Promo produk berhasil dihapus'], 200);
            } else {
                return response()->json(['message' => 'Promo produk tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus promo produk: ' . $e->getMessage()], 500);
        }
    }
}
