<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Libraries\Calculation;
use App\Libraries\Fungsi;
use App\Models\Shop;
use App\Models\Saldo;
use App\Models\Products;
use App\Models\Lpse_config;
use App\Models\Brand;
use App\Models\Lpseprice;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ShopCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $seller;
    protected $getOrder;
    protected $data;
    protected $Model;
    protected $Libraries;

    public function __construct(Request $request)
    {
        $this->seller     = $request->session()->get('seller_id');

        $sellerType     = Shop::getTypeById($this->seller);
        $saldoPending   = Saldo::calculatePendingSaldo($this->seller);
        $lpse           = Lpse_config::orderBy('id', 'desc')->first();

        // Membuat $this->data
        $this->data['title'] = 'Product';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldoPending;
        $this->data['ppn'] = $lpse->ppn;
        $this->data['pph'] = $lpse->pph;
        $this->data['mp_percent'] = $lpse->mp_percent;

        $this->Model['ProductCategory'] = new ProductCategory();
        $this->Model['ShopCategory'] = new ShopCategory();
        $this->Model['Shop'] = new Shop();
        $this->Model['Brands'] = new Brand();
        $this->Model['Products'] = new Products();

        // Libraries
        $this->Libraries['Fungsi'] = new Fungsi();
        $this->Libraries['Calculation'] = new Calculation();
    }

    public function index()
    {
        $this->data['tipe'] = null;
        $products = DB::table('products as p')
            ->select(
                'p.*',
                'lp.price_lpse as price_tayang'
            )
            ->leftJoin('lpse_price as lp', 'p.id', '=', 'lp.id_product')
            ->where('p.id_shop', $this->seller)
            ->where('p.status_delete', 'N')
            ->distinct()
            ->orderBy('id', 'desc')
            ->get();
        return view('seller.product.index', $this->data, ['products' => $products]);
    }

    public function filterProduct($status)
    {
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

        return response()->json($products);

        // return view('seller.product.index', $this->data, ['products' => $products]);
    }

    public function addProduct(Request $request)
    {
        // Validasi input yang diperlukan
        $request->validate([
            'name' => 'required|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kategorilevel1' => 'required|integer',
            'sku' => 'nullable|string|max:100',
            'price' => 'required|numeric',
            'price_exclude' => 'required|numeric',
            'PPn' => 'required|numeric',
            'price_lpse' => 'required|numeric',
            'stock' => 'required|integer',
            'id_satuan' => 'required|string|max:50',
            'weight' => 'required|numeric',
            // 'dimension_length' => 'nullable|numeric',
            // 'dimension_width' => 'nullable|numeric',
            // 'dimension_high' => 'nullable|numeric',
            'status_preorder' => 'required|string|max:255',
            'is_pdn' => 'required|string|max:255',
            'status_new_product' => 'required|string|max:50',
        ]);

        // Pastikan minimal satu gambar diunggah
        if (!$request->hasFile('images')) {
            return response()->json(['error' => 'At least one image is required'], 400);
        }

        // Tentukan ID kategori yang akan disimpan
        $id_category = $request->kategorilevel2 ? $request->kategorilevel2 : $request->kategorilevel1;

        // Data produk yang akan disimpan
        $data = [
            'sku' => $request->sku,
            'id_shop' => $this->seller,
            'id_brand' => $request->id_brand,
            'id_category' => $id_category,
            'name' => $request->name,
            'price_exclude' => $request->price_exclude,
            'price' => $request->price,
            'weight' => $request->weight,
            'status_preorder' => $request->status_preorder,
            'status_new_product' => $request->status_new_product,
            'id_satuan' => $request->id_satuan,
            'id_tipe' => $request->id_jenis_produk,
            'stock' => $request->stock,
            'status_display' => $request->status_display, // Nilai default
            'status_delete' => 'N', // Nilai default
            'status_lpse' => '0', // Nilai default
            'is_pdn' => $request->is_pdn,
            'spesifikasi' => $request->spesifikasi,
            'dimension_length' => $request->dimension_length,
            'dimension_width' => $request->dimension_width,
        ];

        // Buat record baru menggunakan array data
        $product = Products::create($data);

        $updateS = DB::table('products')->where('id', $product->id)->update(['dimension_high' => $request->dimension_high]);


        $fulldata = array_merge([
            'seoname' => $this->Libraries['Fungsi']->getSeoName($request->name),
            'dimension_high' => $request->dimension_high,
            'id' => $product->id,
        ], $data);

        // Insert into product table
        DB::table('product')->insert($fulldata);

        // Simpan informasi harga di LPSEprice
        $Lpse = Lpseprice::create([
            'id_product' => $product->id,
            'price_lpse' => $request->price_lpse,
            'price_before_rounded' => $request->price_lpse,
            'last_update' => now()->format('Y-m-d H:i:s')
        ]);

        // Unggah semua file media yang diunggah
        foreach ($request->file('images') as $index => $file) {
            if ($file->isValid()) {
                // Simpan media
                $media = $product->addMedia($file)
                    ->usingFileName(time() . '_' . $index . '.jpg') // Nama file unik
                    ->toMediaCollection('artwork', 'products');

                // Ambil URL dari berbagai konversi
                $image50 = $media->getUrl('sm');
                $image100 = $media->getUrl('md');
                $image300 = $media->getUrl('lg');
                $image800 = $media->getUrl('bg');

                // Simpan informasi gambar di tabel product_image
                $data_img = [
                    'id_product' => $product->id,
                    'image50' => $image50,
                    'image100' => $image100,
                    'image300' => $image300,
                    'image800' => $image800,
                    'is_default' => $index == 0 ? 'yes' : 'no', // Gambar pertama sebagai default
                    'description' => 'Product',
                    'created_dt' => Carbon::now(),
                    'last_updated_dt' => Carbon::now(),
                ];

                // Debugging: Cek data sebelum disimpan
                Log::info('Inserting image data: ', $data_img);

                DB::table('product_image')->insert($data_img);
            }
        }

        // Simpan file video
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $index => $video) {
                if ($video->isValid()) {
                    $media = $product->addMedia($video)
                        ->usingFileName(time() . '_' . $index . '.' . $video->getClientOriginalExtension())
                        ->toMediaCollection('vidio_product', 'vidio_product');

                    $videoUrl = $media->getUrl();

                    $data_video = [
                        'id_product' => $product->id,
                        'link' => $videoUrl,
                        'created_dt' => Carbon::now(),
                    ];
                    // Log::info('Video saved : ' . $data_video);

                    DB::table('product_video')->insert($data_video);
                }
            }
        }

        // Redirect dengan pesan sukses jika berhasil
        return response()->json(['success' => 'product berhasil di simpan']);
    }

    public function updateProduct(Request $request)
    {
        $id_product = $request->id;

        $product = Products::find($id_product);
        $id_category = $request->kategorilevel2 ? $request->kategorilevel2 : $request->kategorilevel1;
        $data = [
            'sku' => $request->sku,
            'id_shop' => $this->seller,
            'id_brand' => $request->id_brand,
            'id_category' => $id_category,
            'name' => $request->name,
            'price_exclude' => $request->price_exclude,
            'price' => $request->price,
            'weight' => $request->weight,
            'status_preorder' => $request->status_preorder,
            'status_new_product' => $request->status_new_product,
            'id_satuan' => $request->id_satuan,
            'id_tipe' => $request->id_jenis_produk,
            'stock' => $request->stock,
            'status_display' => $request->status_display,
            'status_delete' => 'N',
            'status_lpse' => '0',
            'is_pdn' => $request->is_pdn,
            'spesifikasi' => $request->spesifikasi,
            'dimension_length' => $request->dimension_length,
            'dimension_width' => $request->dimension_width,
            // 'dimension_high' => $request->dimension_high,
        ];
        // Update produk dengan data baru
        $product->update($data);

        $fulldata = array_merge([
            'seoname' => $this->Libraries['Fungsi']->getSeoName($request->name),
            'dimension_high' => $request->dimension_high,
        ], $data);

        // Insert into product table
        DB::table('product')->where('id', $id_product)->update($fulldata);

        $updateS = DB::table('products')->where('id', $id_product)->update(['dimension_high' => $request->dimension_high]);

        if ($request->has('old_images')) {
            $oldImages = $request->input('old_images');
            $jmlh_old_images = count($oldImages);
            $old_data = DB::table('product_image')->where('id_product', $id_product)->count();

            if ($jmlh_old_images != $old_data) {
                $delete = DB::table('product_image')->where('id_product', $id_product)->delete();
                foreach ($oldImages as $index => $imageUrl) {
                    // $product->clearMediaCollection('artwork');

                    // Ubah URL menjadi path relatif dari 'storage/app/public/'
                    $relativePath = str_replace(url('/storage'), 'public', $imageUrl);
                    $filePath = storage_path('app/' . $relativePath);

                    try {
                        if (file_exists($filePath)) {
                            $media = $product->addMedia($filePath)
                                ->usingFileName(time() . '.jpg')
                                ->toMediaCollection('artwork', 'products');
                        } else {
                            Log::error('File not found', ['path' => $filePath]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Gagal menambahkan media dari disk', [
                            'path' => $filePath,
                            'exception' => $e->getMessage()
                        ]);
                    }

                    if (isset($media)) {
                        $image50 = $media->getUrl('sm');
                        $image100 = $media->getUrl('md');
                        $image300 = $media->getUrl('lg');
                        $image800 = $media->getUrl('bg');

                        $data_img = [
                            'id_product' => $product->id,
                            'image50' => $image50,
                            'image100' => $image100,
                            'image300' => $image300,
                            'image800' => $image800,
                            'is_default' => $index == 0 ? 'yes' : 'no',
                            'description' => 'Product',
                            'created_dt' => Carbon::now(),
                            'last_updated_dt' => Carbon::now(),
                        ];
                        Log::info('Inserting image old: ', $data_img);

                        DB::table('product_image')->insert($data_img);
                    } else {
                        Log::warning('Media not defined for path: ', ['path' => $filePath]);
                    }
                }
            }
        } else {
            if (empty($request->hasFile('images'))) {
                return response()->json(['Eror' => 'Mohon Uploaded image'], 500);
            } else {
                $delete = DB::table('product_image')->where('id_product', $id_product)->delete();
            }
        }

        // Hapus gambar lama jika ada gambar baru yang diunggah
        if ($request->hasFile('images')) {
            // $delete = DB::table('product_image')->where('id_product', $id_product)->delete();
            // $product->clearMediaCollection('artwork');

            foreach ($request->file('images') as $index => $file) {
                if ($file->isValid()) {
                    // Simpan media baru
                    $media = $product->addMedia($file)
                        ->usingFileName(time() . '_' . $index . '.jpg')
                        ->toMediaCollection('artwork', 'products');

                    // Ambil URL dari berbagai konversi
                    $image50 = $media->getUrl('sm');
                    $image100 = $media->getUrl('md');
                    $image300 = $media->getUrl('lg');
                    $image800 = $media->getUrl('bg');

                    // Simpan informasi gambar di tabel product_image
                    $data_img = [
                        'id_product' => $product->id,
                        'image50' => $image50,
                        'image100' => $image100,
                        'image300' => $image300,
                        'image800' => $image800,
                        'is_default' => 'no',
                        'description' => 'Product',
                        'created_dt' => Carbon::now(),
                        'last_updated_dt' => Carbon::now(),
                    ];

                    // Debugging: Cek data sebelum disimpan
                    Log::info('Inserting image new: ', $data_img);

                    DB::table('product_image')->insert($data_img);
                } else {
                    // Debugging: Cek apakah file gambar valid
                    Log::warning('Invalid file: ', ['file' => $file]);
                }
            }
        } else {
            if (empty($request->has('old_images'))) {
                return response()->json(['Eror' => 'Mohon Uploaded image'], 500);
            }
        }

        // VIdio
        if (empty($request->has('old_vidio'))) {
            $product->clearMediaCollection('vidio_product');
            $delete = DB::table('product_video')->where('id_product', $id_product)->delete();

            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $index => $video) {
                    if ($video->isValid()) {
                        $media = $product->addMedia($video)
                            ->usingFileName(time() . '_' . $index . '.' . $video->getClientOriginalExtension())
                            ->toMediaCollection('vidio_product', 'vidio_product');

                        $videoUrl = $media->getUrl();

                        $data_video = [
                            'id_product' => $product->id,
                            'link' => $videoUrl,
                            'created_dt' => Carbon::now(),
                        ];
                        DB::table('product_video')->insert($data_video);
                    }
                }
            }
        }

        // Update harga di LPSEprice
        $lpse = Lpseprice::where('id_product', $id_product)->update([
            'price_lpse' => $request->price_lpse,
            'price_before_rounded' => $request->price_lpse,
            'last_update' => now()->format('Y-m-d H:i:s')
        ]);

        // Redirect dengan pesan sukses jika berhasil
        return response()->json(['success' => 'Product updated successfully']);
    }


    public function EditProduct($id_product)
    {
        $produk = $this->Model['Products']->getproduct($id_product);
        $satuanProduk = DB::table('jenis_satuan')->select('*')->get();
        $jenisProduk = DB::table('tipe_produk')->select('*')->where('status_delete', 0)->get();

        $data = [
            'produk' => $produk,
            'satuanProduk' => $satuanProduk,
            'jenisProduk' => $jenisProduk,
        ];
        return response()->json($data);
        // return view('seller.product.editProduct',$this->data,['product'=>$data]);
    }

    function getCategorylv1()
    {
        $data = [
            'level' => 1,
        ];
        $categorylevel1 = $this->Model['ProductCategory']->getCategoryByData($data);
        return response()->json($categorylevel1);
    }

    function getsatuanProduk()
    {
        $satuanProduk = DB::table('jenis_satuan')->select('*')->get();
        $jenisProduk = DB::table('tipe_produk')->select('*')->where('status_delete', 0)->get();

        $data = [
            'satuanProduk' => $satuanProduk,
            'jenisProduk' => $jenisProduk,
        ];

        return response()->json($data);
    }

    public function showaddProduct()
    {

        return view('seller.product.addProduct');
    }

    function getBrands()
    {
        $brands = $this->Model['Brands']::all();
        return response()->json($brands);
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
        $violation = DB::table('product_violation_report as pvr')
            ->select(
                'pvr.detail',
                'pvr.suggest_admin as saran',
                'pvr.status',
                'p.name as nameProduct',
                'pv.title as category',
                'pv.type',
                'pv.description',
            )
            ->join('product_violation as pv', 'pv.id', '=', 'pvr.id_violation')
            ->join('product as p', 'p.id', '=', 'pvr.id_product')

            // Kalo menggunkan table product yang baru
            // ->join('products as p','p.id','=','pvr.id_product')

            ->where('pvr.id_shop', $this->seller)
            ->orderBy('pvr.id', 'desc')
            ->get();

        return view('seller.product.violation', $this->data, ["violations" => $violation]);
    }


    public function test($id)
    {
        $products = Shop::getAddressByIdshop($id);
        return response()->json(['product' => $products], 200);
    }

    // Ambil Data Product lama
    public function productAll($id)
    {
        $idAkhir = $id + 1999;

        // Gambar rusak
        // id= 630,631,632,634,



        $products = DB::table('product as p')
            ->select(
                'p.*'
            )
            ->where('p.status_delete', 'N')
            ->whereBetween('p.id', [$id, $idAkhir])
            // ->where('p.id', 634)
            ->get();

        // Mengambil URL gambar untuk setiap produk
        $images = DB::table('product as p')
            ->select('p.id', DB::raw("CONCAT('http://127.0.0.1:8001/',pi.image800) as image800"))
            ->join('product_image as pi', 'pi.id_product', '=', 'p.id')
            ->where('p.status_delete', 'N')
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
        $idAkhir = $id + 1999;
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
        return response()->json(['message' => "Berhasil Menambahkan data dari = " . $id . "-" . $idAkhir, "Link " => "https/127.0.0.1:8000/seller/product/get/product/" . ($idAkhir + 1)], 200);
    }

    function deleteProduct(Request $request)
    {
        $id_product = $request->id_product;
        $product = Products::find($id_product);
        if (!$product) {
            return response()->json(['message' => 'Product tidak ditemukan'], 404);
        }
        $product->status_delete = 'Y';
        $product->save();

        $old_product = Product::find($id_product);
        $old_product->status_delete = 'Y';
        $old_product->save();
        return response()->json(['message' => 'Product berhasil di Hapus'], 200);
    }

    function editStatusProduct(Request $request)
    {
        $id_product = $request->id_product;
        $product = Products::find($id_product);
        if (!$product) {
            return response()->json(['message' => 'Product tidak ditemukan'], 404);
        }

        if ($product->status_display == 'Y') {
            $product->status_display = 'N';
            $product->save();

            $old_product = Product::find($id_product);
            $old_product->status_display = 'N';
            $old_product->save();
        } else {
            $product->status_display = 'Y';
            $product->save();

            $old_product = Product::find($id_product);
            $old_product->status_display = 'Y';
            $old_product->save();
        }

        return response()->json(['message' => $product], 200);
    }

    function CheckDetailCategory($id_category)
    {
        $data_kategori = ProductCategory::getCategoryById($id_category);
        $config = Lpse_config::first();

        $ppn = ($data_kategori->barang_kena_ppn == 0) ? 0 : $config->ppn;
        $data = [
            'ppn' => $ppn,
            'pph' => $config->pph,
            'mp-percent' => $config->fee_mp_percent
        ];

        return response()->json($data);
    }

    function getProductSeller()
    {
        $id_shop = $this->seller;
        $products   = Products::getproductactivebyId_shop($id_shop);
        return response()->json(['products' => $products]);
    }

    function getPrice($id_product)
    {
        $price  =   Products::getPricewithProduct($id_product);
        return response()->json($price);
    }

    function calcHargaTayang(Request $request)
    {
        // Deklarasi data
        $harga_input    = $request->price;
        $id_product     = $request->id_product;

        // Config
        $lc             = Lpse_config::first();
        $ppn            = $lc->ppn;
        $fee_mp_nominal    = $lc->fee_mp_nominal;
        $fee_mp_percent    = $lc->fee_mp_percent;
        $fee_pg_nominal    = $lc->fee_pg_nominal;
        $fee_pg_percent    = $lc->fee_pg_percent;

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
        $harga                         = $harga_input;
        $tot_fee_perc               = $fee_pg_percent + $fee_mp_percent;
        $tot_fee_nom                = $fee_mp_nominal + $fee_pg_nominal;
        $fee_mp                     = round($harga * ($tot_fee_perc / 100)) + ($tot_fee_nom + $tot_fee_nom * ($ppn / 100));
        $harga_dasar                = $this->ceil_price(($harga + $fee_mp), 100);
        $nominal_ppn                = $harga_dasar * ($ppn / 100);
        $harga_tayang               = $harga_dasar + $nominal_ppn;
        // END RUMUS HARGA INPUT TO HARGA TAYANG //

        $data = [
            'harga_input' => $harga_input,
            'fee_mp_percent' => $fee_mp_percent,
            'ppn' => $ppn,
            'harga_dasar' => $harga_dasar,
            'nominal_ppn' => $nominal_ppn,
            'harga_tayang' => $harga_tayang,
        ];

        return response()->json($data);
    }

    private function ceil_price($number, $significance = 1)
    {

        return (is_numeric($number) && is_numeric($significance)) ? (ceil($number / $significance) * $significance) : false;
    }

    function calcHarga(Request $request)
    {
        // Config
        $config         = Lpse_config::first();
        $ppn            = $config->ppn;
        $fee_mp_nominal = $config->fee_mp_nominal;
        $fee_mp_percent = $config->fee_mp_percent;
        $fee_pg_nominal = $config->fee_pg_nominal;
        $fee_pg_percent = $config->fee_pg_percent;
        $int_rounded    = 1000;

        // PPh menggunkan default barang
        $pph            = 1.5;

        // Deklarasi data
        $harga          = $request->harga;
        $id_kategori    = $request->id_kategori;
        $idtipeProduk   = $request->idtipeProduk;
        $id_shop        = $this->seller;

        // Deklarasi Model
        $kategori         = $this->Model['ProductCategory']->find($id_kategori);
        $shop             = $this->Model['Shop']->getShopCategory($id_shop);
        $id_category_shop = $shop->shop_category;

        // cek Jenis PPh
        $checkPPh         = $this->Model['ProductCategory']->jenisProduct($id_kategori);

        if ($shop) {
            $ShopKategori     = $this->Model['ShopCategory']->getSpesialKategori($id_category_shop);
            $spesial_cat_product = in_array($id_kategori, [1949, 1947, 1952, 1948]) ? 1 : 0;
            if (isset($ShopKategori->spesial_kategori) && $ShopKategori->spesial_kategori == 1 && $spesial_cat_product == 1) {
                $ppn = 0;
                $int_rounded = 1;
            } else {
                $cek_ppn = $kategori->barang_kena_ppn;
                if ($cek_ppn == '0') {
                    $ppn = 0;
                    $int_rounded = 1;
                }
            }
        } else {
            $cek_ppn = $kategori->barang_kena_ppn;
            if ($cek_ppn == '0') {
                $ppn = 0;
                $int_rounded = 1;
            }
        }

        // Set PPh
        if ($checkPPh == 1) {
            // Jasa biasa
            $pph = 2;
        }

        if ($idtipeProduk == 3 && $checkPPh == 1) {
            // untuk jasa sewa ruangan
            $pph = 10;
        }

        // RUMUS HARGA INPUT TO HARGA TAYANG //
        $dataArr = [
            'harga'          => $harga,
            'fee_pg_percent' => $fee_pg_percent,
            'fee_mp_percent' => $fee_mp_percent,
            'fee_mp_nominal' => $fee_mp_nominal,
            'fee_pg_nominal' => $fee_pg_nominal,
            'ppn'            => $ppn,
            'pph'            => $pph,
            'int_rounded'    => $int_rounded,
        ];

        $result = $this->Libraries['Calculation']->calc_harga_tayang($dataArr);
        // END RUMUS HARGA INPUT TO HARGA TAYANG //

        $final_data = [
            'harga_tayang_belum_ppn'    => $result['price_exlude_with_ppn'],
            'ppn_price'     => $result['price_ppn'],
            'pph_price'     => $result['price_pph'],
            'harga_tayang'  => $result['price_final'],
            'ppn'           => $ppn,
            'pph'           => $pph,
            'idtipeProduk'  => $idtipeProduk,
        ];

        return response()->json($final_data);
    }
}
