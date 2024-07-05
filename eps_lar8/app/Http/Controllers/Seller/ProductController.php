<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Libraries\Calculation;
use App\Models\Shop;
use App\Models\Saldo;
use App\Models\Products;
use App\Models\Lpse_config;
use App\Models\Brand;
use App\Models\Lpseprice;
use App\Models\ProductCategory;
use App\Models\ShopCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    protected $seller;
    protected $getOrder;
    protected $data;
    protected $Model;

    public function __construct(Request $request)
    {
        $this->seller 	= $request->session()->get('seller_id');

        $sellerType     = Shop::getTypeById($this->seller);
        $saldoPending   = Saldo::calculatePendingSaldo($this->seller);
        $lpse           = Lpse_config::orderBy('id', 'desc')->first();

        // Membuat $this->data
        $this->data['title'] = 'Product';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldoPending;

        $this->Model['ProductCategory'] = new ProductCategory();
        $this->Model['ShopCategory'] = new ShopCategory();
        $this->Model['Shop'] = new Shop();

    }

    public function index()
    {
        $this->data['tipe'] = null;
        $products =DB::table('products as p')
                ->select(
                    'p.*',
                    'lp.price_lpse as price_tayang'
                    )
                ->leftJoin('lpse_price as lp', 'p.id', '=', 'lp.id_product')
                ->where('p.id_shop',$this->seller)
                ->where('p.status_delete','N')
                ->distinct()
                ->orderBy('id', 'desc')
                ->get();
        return view('seller.product.index',$this->data,['products'=>$products]);
    }

    public function filterProduct($status) {
        $this->data['tipe'] = $status;
        $products = DB::table('products as p')
                    ->select(
                        'p.*',
                        'lp.price_lpse as price_tayang'
                    )
                    ->join('lpse_price as lp', 'lp.id_product', '=', 'p.id')
                    ->where('p.id_shop', $this->seller)
                    ->where('p.status_delete', 'N');

        if ($status == 'live') {
            $products->where('p.status_display', 'Y');
        } elseif ($status == 'habis') {
            $products->where('p.stock', 0);
        } elseif ($status == 'arsip') {
            $products->where('p.status_display', 'N');
        }

        $products = $products->orderBy('id', 'desc')->get();

        return view('seller.product.index', $this->data, ['products' => $products]);
    }

    public function addProduct(Request $request)
    {
        // Validasi input yang diperlukan
        $request->validate([
            'name' => 'required|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Pastikan minimal satu gambar diunggah
        if (!$request->hasFile('images')) {
            return redirect()->back()->with('error', 'At least one image is required');
        }

        // Tentukan ID kategori yang akan disimpan
        $id_category = $request->kategorilevel2 ? $request->kategorilevel2 : $request->kategorilevel1;

        // Buat objek produk baru
        $product = Products::create([
            'sku' => $request->sku,
            'id_shop' => $this->seller,
            'id_brand' => $request->merek,
            'id_category' => $id_category,
            'name' => $request->name,
            'price_exclude' => $request->harga,
            'price' => $request->harga,
            'weight' => $request->berat,
            'status_preorder' => $request->preorder,
            'status_new_product' => $request->kondisi,
            'stock' => $request->stok,
            'status_display' => 'Y', // Nilai default
            'status_delete' => 'N', // Nilai default
            'status_lpse' => '0', // Nilai default
            'is_pdn' => $request->produk_dalam_negeri,
        ]);

        // Simpan informasi tambahan produk
        $product->spesifikasi = $request->spesifikasi;
        $product->dimension_length = $request->demensipanjang;
        $product->dimension_width = $request->demensilebar;
        $product->dimension_high = $request->demensitinggi;
        $product->save();

        // Lakukan perhitungan harga tayang dan simpan ke dalam LPSE
        $data_kategori = ProductCategory::getCategoryById($id_category);
        $config = Lpse_config::first();

        $ppn = ($data_kategori->barang_kena_ppn == 0) ? 0 : $config->ppn;

        $dataArr = [
            'harga' => $request->harga,
            'fee_pg_percent' => $config->fee_pg_percent,
            'fee_mp_percent' => $config->fee_mp_percent,
            'fee_mp_nominal' => $config->fee_mp_nominal,
            'fee_pg_nominal' => $config->fee_pg_nominal,
            'ppn' => $ppn,
            'pph' => $config->pph,
        ];

        $result = Calculation::calc_harga_tayang($dataArr);
        $final_price = $result['price_final'];

        // Simpan informasi harga di LPSEprice
        $Lpse = Lpseprice::create([
            'id_product' => $product->id,
            'price_lpse' => $final_price,
            'price_before_rounded' => $final_price,
            'last_update' => now()->format('Y-m-d H:i:s')
        ]);

        // Unggah semua file media yang diunggah
        foreach ($request->file('images') as $file) {
            if ($file->isValid()) {
                $product->addMedia($file)
                    ->usingFileName(time() . '.jpg') // Nama file unik
                    ->toMediaCollection('artwork', 'products');
            }
        }

        // Redirect dengan pesan sukses jika berhasil
        return redirect()->route('seller.product')->with('success', 'Product added successfully');
    }


    public function EditProduct($id_product){
        $data = Products::getDataProduct($id_product);
        // return response()->json($data);
        return view('seller.product.editProduct',$this->data,['product'=>$data]);
    }

    public function showaddProduct()
    {
        $this->data['tipe'] = null;

        $categoryModel = new ProductCategory();
        $data = [
            'level' => 1,
        ];
        $categorylevel1 = $categoryModel->getCategoryByData($data);

        $brands = Brand::all();

        return view('seller.product.addProduct',['categorylevel1' => $categorylevel1, 'brands' => $brands],$this->data);
    }

    public function getCategoryLevel2($id_level1)
    {
        $categoryModel = new ProductCategory();
        $data = [
            'level' => 2,
            'parent_id' => $id_level1,
        ];
        $categoryLevel2 = $categoryModel->getCategoryByData($data);

        return response()->json($categoryLevel2);
    }

    public function showViolation()
    {
        $this->data['tipe'] = null;
        $violation =DB::table('product_violation_report as pvr')
                ->select(
                    'pvr.detail',
                    'pvr.suggest_admin as saran',
                    'pvr.status',
                    'p.name as nameProduct',
                    'pv.title as category',
                    'pv.type',
                    'pv.description',
                    )
                ->join('product_violation as pv','pv.id','=','pvr.id_violation')
                ->join('product as p','p.id','=','pvr.id_product')

                // Kalo menggunkan table product yang baru
                // ->join('products as p','p.id','=','pvr.id_product')

                ->where('pvr.id_shop',$this->seller)
                ->orderBy('pvr.id', 'desc')
                ->get();

        return view('seller.product.violation',$this->data,["violations"=> $violation]);
    }


    public function test($id)
    {
        $products =Products::where('id',$id)
                    ->get();
        return response()->json([ 'product' => $products], 200);
    }

    // Ambil Data Product lama
    public function productAll($id)
    {
        $idAkhir=$id + 199;

        // Gambar rusak
        // id= 630,631,632,634,



        $products = DB::table('product as p')
            ->select(
                'p.*'
            )
            ->where('p.status_delete','N')
            ->whereBetween('p.id', [$id, $idAkhir])
            // ->where('p.id', 634)
            ->get();

        // Mengambil URL gambar untuk setiap produk
        $images = DB::table('product as p')
            ->select('p.id', DB::raw("CONCAT('http://127.0.0.1:8001/',pi.image800) as image800"))
            ->join('product_image as pi', 'pi.id_product', '=', 'p.id')
            ->where('p.status_delete','N')
            ->whereIn('p.id', range($id, $idAkhir))
            // ->where('p.id', 634)
            ->get();

        // Mengelompokkan URL gambar berdasarkan ID produk
        $imageMap = [];
        foreach ($images as $image) {
            if (!isset($imageMap[$image->id])) {
                $imageMap[$image->id] = [];
            }
            $imageMap[$image->id][] = $image->image800;
        }

        // Menggabungkan data produk dengan URL gambar yang sesuai
        foreach ($products as &$product) {
            $product->images = $imageMap[$product->id] ?? [];
        }

        return response()->json(['products' => $products]);
    }

    // memsukan data dari API product lama ke product baru
    public function addOldProduct($id) {
        $idAkhir=$id + 1999;
        // Panggil metode productAll() untuk mendapatkan data produk dalam format JSON
        $jsonResponse = $this->productAll($id);
        // Ubah format JSON menjadi array PHP
        $data = json_decode($jsonResponse->getContent(), true);
        // Iterasi setiap produk dalam array
        foreach ($data['products'] as $item) {
            // Buat objek Products baru
            $product = new Products();
            // Isi data dari JSON
            $product->id = $item['id'];
            $product->sku = $item['sku'];
            $product->id_shop = $item['id_shop'];
            $product->id_brand = $item['id_brand'];
            $product->id_category = $item['id_category'];
            $product->name = $item['name'];
            $product->price_exclude = $item['price_exclude'];
            $product->price = $item['price'];
            $product->weight = $item['weight'];
            $product->status_preorder = $item['status_preorder'];
            $product->status_new_product = $item['status_new_product'];
            $product->stock = $item['stock'];
            $product->status_display = $item['status_display'];
            $product->status_delete = $item['status_delete'];
            $product->status_lpse = $item['status_lpse'];
            $product->is_pdn = $item['is_pdn'];
            $product->spesifikasi = $item['spesifikasi'];
            $product->dimension_length = $item['dimension_length'];
            $product->dimension_width = $item['dimension_width'];
            $product->dimension_high = $item['dimension_high'];
            $product->special_price = $item['special_price'];
            $product->special_start = $item['special_start'];
            $product->special_end = $item['special_end'];
            $product->discount_value = $item['discount_value'];
            $product->discount_type = $item['discount_type'];
            $product->price_after_discount = $item['price_after_discount'];
            $product->discount_start = $item['discount_start'];
            $product->discount_end = $item['discount_end'];
            $product->description = $item['description'];
            $product->averange_rating = $item['averange_rating'];
            $product->count_rating = $item['count_rating'];
            $product->count_sold = $item['count_sold'];
            $product->status = $item['status'];
            $product->status_display = $item['status_display'];
            $product->status_delete = $item['status_delete'];
            // Simpan
            $product->save();
            // Proses gambar-gambar
            foreach ($item['images'] as $imageUrl) {
                    $product->addMediaFromUrl($imageUrl)
                        ->usingFileName(time() . '.jpg')
                        ->toMediaCollection('artwork', 'products');
            }
        }
        return response()->json(['message' => "Berhasil Menambahkan data dari = ".$id ."-".$idAkhir , "Link " => "https/127.0.0.1:8000/seller/product/get/product/". ($idAkhir+1) ], 200);
    }

    function deleteProduct(Request $request){
        $id_product = $request->id_product;
        $product = Products::find($id_product);
        if (!$product) {
            return response()->json(['message' => 'Product tidak ditemukan'], 404);
        }
        $product->status_delete = 'Y';
        $product->save();
        return response()->json(['message' => 'Product berhasil di Hapus'], 200);
    }

    function editStatusProduct(Request $request){
        $id_product = $request->id_product;
        $product = Products::find($id_product);
        if (!$product) {
            return response()->json(['message' => 'Product tidak ditemukan'], 404);
        }

        if ($product->status_display == 'Y') {
            $product->status_display = 'N';
            $product->save();
        }else {
            $product->status_display = 'Y';
            $product->save();
        }

        return response()->json(['message' => $product], 200);
    }

    function CheckDetailCategory($id_category) {
        $data_kategori = ProductCategory::getCategoryById($id_category);
        $config = Lpse_config::first();

        $ppn = ($data_kategori->barang_kena_ppn == 0) ? 0 : $config->ppn;
        $data= [
            'ppn'=>$ppn,
            'pph'=>$config->pph,
            'mp-percent' => $config->fee_mp_percent
        ];

        return response()->json($data);
    }

    function getProductSeller(){
        $id_shop = $this->seller;
        $products   = Products::getproductactivebyId_shop($id_shop);
        return response()->json(['products'=>$products]);
    }

    function getPrice($id_product){
        $price  =   Products::getPricewithProduct($id_product);
        return response()->json($price);
    }

    function calcHargaTayang(Request $request) {
        // Deklarasi data
        $harga_input    = $request->price;
        $id_product     = $request->id_product;

        // Config
        $lc 			= Lpse_config::first();
		$ppn			= $lc->ppn;
		$fee_mp_nominal	= $lc->fee_mp_nominal;
		$fee_mp_percent	= $lc->fee_mp_percent;
		$fee_pg_nominal	= $lc->fee_pg_nominal;
		$fee_pg_percent	= $lc->fee_pg_percent;

        // check PPN
        $CheckppnProduct   = $this->Model['ProductCategory']->check_ppn($id_product);
        $checkShop         = $this->Model['Shop']->getShopCategory($this->seller);

        if ($checkShop) {
            $checkKategori     = $this->Model['ShopCategory']->getSpesialKategori($checkShop->shop_category);
            $spesial_cat_product = in_array($CheckppnProduct->id_category, [1949, 1947, 1952, 1948]) ? 1 : 0;

            if (isset($checkKategori->spesial_kategori) && $checkKategori->spesial_kategori == 1 && $spesial_cat_product == 1) {
                $ppn = 0;
            } else {
                $cek_ppn = $CheckppnProduct->barang_kena_ppn;
                if ($cek_ppn == '0') {
                    $ppn = 0;
                }
            }
        } else {
            $cek_ppn = $CheckppnProduct->barang_kena_ppn;
            if ($cek_ppn == '0') {
                $ppn = 0;
            }
        }

		// RUMUS HARGA INPUT TO HARGA TAYANG //
		$harga 						= $harga_input;
		$tot_fee_perc       		= $fee_pg_percent + $fee_mp_percent;
		$tot_fee_nom        		= $fee_mp_nominal + $fee_pg_nominal;
		$fee_mp             		= round($harga * ($tot_fee_perc / 100)) + ($tot_fee_nom + $tot_fee_nom * ($ppn / 100));
		$harga_dasar        		= $this->ceil_price(($harga + $fee_mp), 100);
		$nominal_ppn        		= $harga_dasar * ($ppn / 100);
        $harga_tayang               = $harga_dasar + $nominal_ppn;
		// END RUMUS HARGA INPUT TO HARGA TAYANG //

        $data = [
            'harga_input' => $harga_input,
            'fee_mp_percent' => $fee_mp_percent,
            'ppn' =>$ppn,
            'harga_dasar' => $harga_dasar,
            'nominal_ppn' => $nominal_ppn,
            'harga_tayang' => $harga_tayang,
        ];

        return response()->json($data);
    }

    private function ceil_price($number, $significance = 1) {

		return (is_numeric($number) && is_numeric($significance)) ? (ceil($number / $significance) * $significance) : false;
	}

}

