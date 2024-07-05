<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Foreach_;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Products extends Model implements HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia;
    protected $appends = ['artwork_url_lg','artwork_url_md','artwork_url_sm'];

    protected $table = 'products';
    protected $hidden = ['media'];
    protected $fillable = [
        'name',
        'sku',
        'id_shop',
        'id_brand',
        'id_category',
        'price_exclude',
        'price',
        'weight',
        'status_preorder',
        'status_new_product',
        'stock',
        'status_display',
        'status_delete',
        'status_lpse',
        'is_pdn',
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('artwork')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/svg']);

        $this->addMediaConversion('sm')
            ->width(100)
            ->height(100)
            ->performOnCollections('artwork')->nonOptimized()->nonQueued();

        $this->addMediaConversion('md')
            ->width(200)
            ->height(200)
            ->performOnCollections('artwork')->nonOptimized()->nonQueued();

        $this->addMediaConversion('lg')
            ->width(300)
            ->height(300)
            ->performOnCollections('artwork')->nonOptimized()->nonQueued();
    }

    public function getArtworkUrlLgAttribute($value)
    {
        $media = $this->getMedia('artwork'); // Mengambil semua media dengan nama koleksi 'artwork'
        $artworkUrls = [];

        foreach ($media as $item) {
            if ($item->disk != 'products') {
                $artworkUrls[] = $item->getTemporaryUrl(Carbon::now()->addMinutes(intval(1140)), 'lg');
            } else {
                $artworkUrls[] = $item->getFullUrl('lg');
            }
        }

        // Jika tidak ada media, kembalikan URL default
        if (empty($artworkUrls)) {
            if (isset($this->log) && isset($this->log->artwork_url)) {
                return $this->log->artwork_url;
            } else {
                return asset('common/default/profile.png');
            }
        }

        return $artworkUrls;
    }
    public function getArtworkUrlMdAttribute($value)
    {
        $media = $this->getMedia('artwork'); // Mengambil semua media dengan nama koleksi 'artwork'
        $artworkUrls = [];

        foreach ($media as $item) {
            if ($item->disk != 'products') {
                $artworkUrls[] = $item->getTemporaryUrl(Carbon::now()->addMinutes(intval(1140)), 'md');
            } else {
                $artworkUrls[] = $item->getFullUrl('md');
            }
        }

        // Jika tidak ada media, kembalikan URL default
        if (empty($artworkUrls)) {
            if (isset($this->log) && isset($this->log->artwork_url)) {
                return $this->log->artwork_url;
            } else {
                return asset('common/default/profile.png');
            }
        }

        return $artworkUrls;
    }
    public function getArtworkUrlsmAttribute($value)
    {
        $media = $this->getMedia('artwork'); // Mengambil semua media dengan nama koleksi 'artwork'
        $artworkUrls = [];

        foreach ($media as $item) {
            if ($item->disk != 'products') {
                $artworkUrls[] = $item->getTemporaryUrl(Carbon::now()->addMinutes(intval(1140)), 'sm');
            } else {
                $artworkUrls[] = $item->getFullUrl('sm');
            }
        }

        // Jika tidak ada media, kembalikan URL default
        if (empty($artworkUrls)) {
            if (isset($this->log) && isset($this->log->artwork_url)) {
                return $this->log->artwork_url;
            } else {
                return asset('common/default/profile.png');
            }
        }

        return $artworkUrls;
    }

    public function countProductByIdShop($id_shop, $where) {

		return self::where('id_shop', $id_shop)
                   ->where('status_delete', 'N')
                   ->where($where)
                   ->select('id')
                   ->count();
	}

    public function getproduct($perPage = 10) {
        return self::select(
            'products.*',
            'lp.price_lpse as hargaTayang',
            's.name as namaToko',
            's.id as idToko',
            'p.province_name'
        )
        ->leftJoin('lpse_price as lp', 'products.id', '=', 'lp.id_product')
        ->leftJoin('shop as s', 'products.id_shop', '=', 's.id')
        ->leftJoin('member_address as ma', 's.id_address', '=', 'ma.member_address_id')
        ->leftJoin('province as p', 'ma.province_id', '=', 'p.province_id')
        ->where('products.status_display', 'Y')
        ->where('products.status_delete', 'N')
        ->where('s.status', 'active')
        ->orderBy('products.id', 'DESC')
        ->distinct()
        ->paginate($perPage);
    }

    function getDataProduct($id_product){
        return self::select(
            'products.*',
            'lp.price_lpse as hargaTayang',
        )
        ->join('lpse_price as lp', 'products.id', '=', 'lp.id_product')
        ->where('products.id',$id_product)
        ->first();
    }

    public function getProductByIdShop($id_shop) {
        return self::select(
            'products.*',
            'lp.price_lpse as hargaTayang',
            's.name as namaToko',
            's.id as idToko',
            'p.province_name'
        )
        ->join('lpse_price as lp', 'products.id', '=', 'lp.id_product')
        ->join('shop as s', 'products.id_shop', '=', 's.id')
        ->join('member_address as ma', 's.id_address', '=', 'ma.member_address_id')
        ->join('province as p', 'ma.province_id', '=', 'p.province_id')
        ->where('products.status_display','Y')
        ->where('products.status_delete','N')
        ->where('s.status','active')
        ->where('s.id', $id_shop)
        ->get();
    }

    public function getproductById($id){
        return self::select(
            'products.*',
            'lp.price_lpse as hargaTayang',
            's.name as namaToko',
            's.id as idToko',
            's.avatar',
            'p.province_name',
            'b.name as merek'
        )
        ->join('lpse_price as lp', 'products.id', '=', 'lp.id_product')
        ->join('shop as s', 'products.id_shop', '=', 's.id')
        ->join('member_address as ma', 's.id_address', '=', 'ma.member_address_id')
        ->join('province as p', 'ma.province_id', '=', 'p.province_id')
        ->join('brand as b', 'products.id_brand', '=', 'b.id')
        ->where('products.id',$id)
        ->first();
    }

    public function get5ProductByIdShop($id_shop) {
        return self::select(
            'products.*',
        )
        ->join('shop as s', 'products.id_shop', '=', 's.id')
        ->where('s.id', $id_shop)
        ->inRandomOrder() // Urutkan hasil secara acak
        ->take(5) // Ambil 5 data
        ->get();
    }

    public function countproductTerjualbyId($id_shop)
    {
        return self::where('id_shop', $id_shop)
                ->sum('count_sold');
    }

    public function getProductbyEtalase($id_etalase)
    {
        $productIds = Etalase::getProductetase($id_etalase);
        $productIds = $productIds->pluck('id_product')->toArray();
        $products = self::select(
            'products.*',
            'lp.price_lpse as hargaTayang',
            's.name as namaToko',
            's.id as idToko',
            'p.province_name'
        )
        ->join('lpse_price as lp', 'products.id', '=', 'lp.id_product')
        ->join('shop as s', 'products.id_shop', '=', 's.id')
        ->join('member_address as ma', 's.id_address', '=', 'ma.member_address_id')
        ->join('province as p', 'ma.province_id', '=', 'p.province_id')
        ->where('products.status_display','Y')
        ->where('products.status_delete','N')
        ->where('s.status','active')
        ->whereIn('products.id', $productIds)
        ->get();

        return $products;
    }

    public function getProductTerbaruByIdshop($id_shop) {
        $products = self::select(
            'products.*',
            'lp.price_lpse as hargaTayang',
            's.name as namaToko',
            's.id as idToko',
            'p.province_name'
        )
        ->join('lpse_price as lp', 'products.id', '=', 'lp.id_product')
        ->join('shop as s', 'products.id_shop', '=', 's.id')
        ->join('member_address as ma', 's.id_address', '=', 'ma.member_address_id')
        ->join('province as p', 'ma.province_id', '=', 'p.province_id')
        ->where('products.status_display','Y')
        ->where('products.status_delete','N')
        ->where('s.status','active')
        ->where('products.id_shop',$id_shop)
        ->orderBy('products.created_at', 'desc')
        ->get();

        return $products;
    }

    public function GetKategoryProductByIdshoplavel1($id_shop) {
        $categories = DB::table('products')
            ->select('pc.id', 'pc.name')
            ->join('product_category as pc', 'products.id_category', '=', 'pc.id')
            ->where('products.id_shop', $id_shop)
            ->where('pc.level', 1)
            ->distinct()
            ->get(); // Retrieve the result set

        return $categories;
    }

    public function GetKategoryProductByIdshoplavel2($id_shop) {
        $categories = DB::table('products')
            ->select('pc.id', 'pc.name')
            ->join('product_category as pc', 'products.id_category', '=', 'pc.id')
            ->where('products.id_shop', $id_shop)
            ->where('pc.level', 2)
            ->distinct()
            ->get(); // Retrieve the result set

        return $categories;
    }

    public function GetKategoryProductByIdshoplavel3($id_shop) {
        $categories = DB::table('products')
            ->select('pc.id', 'pc.name')
            ->join('product_category as pc', 'products.id_category', '=', 'pc.id')
            ->where('products.id_shop', $id_shop)
            ->where('pc.level', 3)
            ->distinct()
            ->get(); // Retrieve the result set

        return $categories;
    }

    public function GetProductByKategoriandIdShop($id_kategori,$id_shop){
        $products = self::select(
            'products.*',
            'lp.price_lpse as hargaTayang',
            's.name as namaToko',
            's.id as idToko',
            'p.province_name'
        )
        ->join('lpse_price as lp', 'products.id', '=', 'lp.id_product')
        ->join('shop as s', 'products.id_shop', '=', 's.id')
        ->join('member_address as ma', 's.id_address', '=', 'ma.member_address_id')
        ->join('province as p', 'ma.province_id', '=', 'p.province_id')
        ->where('products.status_display','Y')
        ->where('products.status_delete','N')
        ->where('s.status','active')
        ->where('products.id_shop',$id_shop)
        ->where('products.id_category',$id_kategori)
        ->get();

        return $products;
    }

    function getProductDetail($id_product){
        $query = self::select(
            'products.*',
            'pc.barang_kena_ppn'
        )
        ->leftJoin('product_category as pc', 'pc.id', '=', 'products.id_category')
        ->where('products.id', $id_product)
        ->first();

        $image = $query->artwork_url_sm['0'];
        $query->image =$image;

        return $query ;
    }

    function getproductactivebyId_shop($id_shop){
        return self::select(
            'products.name',
            'products.id'
        )
        ->where('products.status_display','Y')
        ->where('products.status_delete','N')
        ->where('products.id_shop', $id_shop)
        ->get();
    }

    function getPricewithProduct($id_product) {
        $query  = DB::table('products as p')
        ->select(
            'p.price',
            'lp.price_lpse as harga_tayang'
        )
        ->join('lpse_price as lp','lp.id_product','p.id')
        ->where('p.id',$id_product)
        ->first();

        return $query;
    }

}
