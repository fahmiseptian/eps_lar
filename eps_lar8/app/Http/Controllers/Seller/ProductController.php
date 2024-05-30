<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Saldo;
use App\Models\Products;
use App\Models\Lpse_config;
use App\Models\Brand;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    protected $seller;
    protected $getOrder;
    protected $data;

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
        $this->data['ppn'] = $lpse->ppn;
        $this->data['pph'] = $lpse->pph;
        $this->data['mp_percent'] = $lpse->fee_mp_percent;
    }

    public function index()
    {
        $this->data['tipe'] = null;
        $products =DB::table('products as p')
                ->select(
                    'p.*',
                    'lp.price_lpse as price_tayang'
                    )
                ->join('lpse_price as lp','lp.id_product','=','p.id')
                ->where('p.id_shop',$this->seller)
                ->where('p.status_delete','N')
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
        $request->validate([
            'name' => 'required|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $files = $request->file('images');

        if (!$files || count($files) < 1) {
            return response()->json(['message' => 'At least one image is required'], 400);
        }

        $product = Products::create([
            'sku' => $request->sku,
            'id_shop' => $this->seller,
            'id_brand' => $request->merek,
            'id_category' => $request->kategorilevel2,
            'name' => $request->name,
            'price_exclude' => $request->hargaBelumPPn,
            'price' => $request->hargaSudahPPn,
            'weight' => $request->berat,
            'status_preorder' => $request->preorder,
            'status_new_product' => $request->kondisi,
            'stock' => $request->stok,
            'status_display' => 'Y', // Mengisi kolom yang harus memiliki nilai default
            'status_delete' => 'N', // Mengisi kolom yang harus memiliki nilai default
            'status_lpse' => '0', // Mengisi kolom yang harus memiliki nilai default
            'is_pdn' => $request->produk_dalam_negeri,
        ]);

        // Mengisi kolom spesifikasi, dimensi, dan lain-lain
        $product->spesifikasi = $request->spesifikasi;
        $product->dimension_length = $request->demensipanjang;
        $product->dimension_width = $request->demensilebar;
        $product->dimension_high = $request->demensitinggi;

        // Menyimpan perubahan
        $product->save();


        // Iterasi semua file yang diunggah
        foreach ($files as $file) {
            // Jika file diunggah dan valid
            if ($file && $file->isValid()) {
                $product->addMedia($file)
                    ->usingFileName(time() . '.jpg') // Beri nama unik untuk setiap file
                    ->toMediaCollection('artwork', 'products');
            }
        }

        return response()->json(['message' => 'Media uploaded successfully', 'product' => $product], 200);
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
        $idAkhir=$id + 1999;

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
            ->select('p.id', DB::raw("CONCAT('https://eliteproxy.co.id/',pi.image800) as image800"))
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
    public function addOldProduct($id)
    {
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


}

