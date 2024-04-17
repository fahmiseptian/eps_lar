<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Saldo;
use App\Models\Product;
use App\Models\Products;
use App\Models\Lpse_config;
use App\Models\Brand;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;

class ProductController extends Controller
{
    protected $seller;
    protected $getOrder;
    protected $data;

    public function __construct(Request $request)
    {
        $this->seller 	= $request->session()->get('seller_id');

        $sellerType =Shop::where('id', $this->seller)
                    ->pluck('type')
                    ->first();

        $saldo =Saldo::where('id_shop', $this->seller)
                ->where('status', 'pending')
                ->sum('total_diterima_seller');

        $lpse = Lpse_config::orderBy('id', 'desc')->first();  
                $ppn = $lpse->ppn;
                $pph = $lpse->pph;
                $mp_percent = $lpse->fee_mp_percent;

        $this->data['title'] = 'Product';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldo;
        $this->data['ppn'] = $ppn;
        $this->data['pph'] = $pph;
        $this->data['mp_percent'] = $mp_percent;
    }

    public function index()
    {
        $this->data['tipe'] = null;
        $products =DB::table('product as p')
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
        $products = DB::table('product as p')
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
            'name' => 'required|string|max:255', // Validate the provided name for the image
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Make the image field nullable and accept multiple images
        ]);

        // Ambil semua file gambar dari request
        $files = $request->file('images');

        // Periksa apakah minimal satu gambar diunggah
        if (!$files || count($files) < 1) {
            return response()->json(['message' => 'At least one image is required'], 400);
        }

        // Buat instance Produk baru
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

    public function test($id)
    {
        $products =Products::where('id',$id)
                    ->get();
        return response()->json([ 'product' => $products], 200);
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
}

