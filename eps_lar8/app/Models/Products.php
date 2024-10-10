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

use function PHPUnit\Framework\returnSelf;

class Products extends Model implements HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia;
    protected $appends = ['artwork_url_lg', 'artwork_url_md', 'artwork_url_sm'];

    protected $table = 'products';
    protected $hidden = ['media'];
    public $timestamps = false;
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
        'barang_kena_ppn',
        'status_lpse',
        'is_pdn',
        'id_satuan',
        'id_tipe',
        'dimension_length',
        'dimension_width',
        'dimension_high	',
        'spesifikasi',
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('artwork')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/svg', 'image/jpg', 'application/octet-stream']);

        $this->addMediaConversion('sm')
            ->width(50)
            ->height(50)
            ->performOnCollections('artwork')->nonOptimized()->nonQueued();

        $this->addMediaConversion('md')
            ->width(100)
            ->height(100)
            ->performOnCollections('artwork')->nonOptimized()->nonQueued();

        $this->addMediaConversion('lg')
            ->width(300)
            ->height(300)
            ->performOnCollections('artwork')->nonOptimized()->nonQueued();

        $this->addMediaConversion('bg')
            ->width(800)
            ->height(800)
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

    public function countProductByIdShop($id_shop, $where = null)
    {
        $query = self::where('id_shop', $id_shop)
            ->where('status_delete', 'N');

        if ($where !== null) {
            // Tambahkan kondisi tambahan jika $where tidak null
            $query->where($where);
        }

        return $query->select('id')->count();
    }


    public function getShowproduct($perPage = 10, $is_lpse,  $category = null)
    {
        $query = DB::table('products') // Ganti DB::select dengan DB::table
            ->select(
                'products.*',
                'lp.price_lpse as hargaTayang',
                's.name as namaToko',
                's.id as idToko',
                'p.province_name',
                DB::raw('(SELECT image300 FROM product_image WHERE id_product = products.id AND is_default = "yes" LIMIT 1) AS image')
            )
            ->leftJoin('lpse_price as lp', 'products.id', '=', 'lp.id_product')
            ->leftJoin('shop as s', 'products.id_shop', '=', 's.id')
            ->leftJoin('member_address as ma', 's.id_address', '=', 'ma.member_address_id')
            ->leftJoin('province as p', 'ma.province_id', '=', 'p.province_id')
            ->where('products.status_display', 'Y')
            ->where('products.status_delete', 'N')
            ->where('s.status', 'active');

        if ($is_lpse == 1) {
            $query->where('s.is_lpse_verified', 1)
            ->where('products.status_lpse', 1);
        }

        if ($category != null) {
            $id_category = DB::table('product_category')->where('lpse_code', $category)->value('id');

            $query->leftJoin('product_category as pc', 'products.id_category', '=', 'pc.id')
                ->where(function ($q) use ($id_category) {
                    $q->where('products.id_category', $id_category)
                        ->orWhere('pc.parent_id', $id_category);
                });
        }

        $query->orderBy('products.id', 'DESC')
            ->distinct();

        // Eksekusi query dan kembalikan hasilnya
        return $query->paginate($perPage);
    }

    function getDataProduct($id_product)
    {
        return self::select(
            'products.*',
            'lp.price_lpse as hargaTayang',
            'pc.name'
        )
            ->join('lpse_price as lp', 'products.id', '=', 'lp.id_product')
            ->join('product_category as pc', 'products.id_category', '=', 'pc.id')
            ->where('products.id', $id_product)
            ->first();
    }

    function getproduct($id_product)
    {
        // Query utama
        $query = DB::table('products as p')
            ->select(
                'p.*',
                'pc.barang_kena_ppn',
                'lp.price_lpse',
                'pv.id as id_video',
                'pv.link',
                'b.name as brand_name',
                DB::raw('(select GROUP_CONCAT(image300) from product_image where id_product = p.id) as images'),
                DB::raw('(select name from product_category where id = p.id_category) as name_lvl3'),
                DB::raw('(select parent_id from product_category where id = p.id_category) as id_lvl2'),
                DB::raw('(select code from product_category where id = id_lvl2) as code_lvl2'),
                DB::raw('(select name from product_category where id = id_lvl2) as name_lvl2'),
                DB::raw('(select parent_id from product_category where id = id_lvl2) as id_lvl1'),
                DB::raw('(select code from product_category where id = id_lvl1) as code_lvl1'),
                DB::raw('(select name from product_category where id = id_lvl1) as name_lvl1')
            )
            ->leftJoin('brand as b', 'b.id', 'p.id_brand')
            ->leftJoin('product_video as pv', 'pv.id_product', '=', 'p.id')
            ->leftJoin('product_category as pc', 'pc.id', '=', 'p.id_category')
            ->leftJoin('lpse_price as lp', 'lp.id_product', '=', 'p.id')
            ->where('p.status_delete', 'N')
            ->where('p.id', $id_product);

        $results = $query->first();
        return $results;
    }

    public function getProductByIdShop($id_shop)
    {
        return self::select(
            'products.*',
            'lp.price_lpse as hargaTayang',
            's.name as namaToko',
            's.id as idToko',
            'p.province_name',
            DB::raw('(SELECT image300 FROM product_image WHERE id_product = products.id AND is_default = "yes" LIMIT 1) AS image')
        )
            ->join('lpse_price as lp', 'products.id', '=', 'lp.id_product')
            ->join('shop as s', 'products.id_shop', '=', 's.id')
            ->join('member_address as ma', 's.id_address', '=', 'ma.member_address_id')
            ->join('province as p', 'ma.province_id', '=', 'p.province_id')
            ->where('products.status_display', 'Y')
            ->where('products.status_delete', 'N')
            ->where('s.status', 'active')
            ->where('s.id', $id_shop)
            ->get();
    }

    public function getproductById($id)
    {
        return self::select(
            'products.*',
            'lp.price_lpse as hargaTayang',
            's.name as namaToko',
            's.id as idToko',
            's.avatar',
            'p.province_name',
            'b.name as merek',
            DB::raw('(select image from shop_banner where id_shop = s.id order by urutan desc limit 1) as image_banner')
        )
            ->join('lpse_price as lp', 'products.id', '=', 'lp.id_product')
            ->join('shop as s', 'products.id_shop', '=', 's.id')
            ->join('member_address as ma', 's.id_address', '=', 'ma.member_address_id')
            ->join('province as p', 'ma.province_id', '=', 'p.province_id')
            ->join('brand as b', 'products.id_brand', '=', 'b.id')
            ->where('products.id', $id)
            ->first();
    }

    function getGambarProduct($id)
    {
        $query = DB::table('product_image')
            ->where('id_product', $id)
            ->get();

        return $query;
    }

    public function get5ProductByIdShop($id_shop)
    {
        return self::select(
            'products.*',
            'pi.image300'
        )
            ->join('shop as s', 'products.id_shop', '=', 's.id')
            ->leftjoin('product_image as pi', 'products.id', '=', 'pi.id_product')
            ->where('s.id', $id_shop)
            ->where('pi.is_default', 'yes')
            ->where('products.status_display', 'Y')
            ->where('products.status_delete', 'N')
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
            ->where('products.status_display', 'Y')
            ->where('products.status_delete', 'N')
            ->where('s.status', 'active')
            ->whereIn('products.id', $productIds)
            ->get();

        return $products;
    }

    public function getProductTerbaruByIdshop($id_shop)
    {
        $products = self::select(
            'products.*',
            'lp.price_lpse as hargaTayang',
            's.name as namaToko',
            's.id as idToko',
            'p.province_name',
            DB::raw('(SELECT image300 FROM product_image WHERE id_product = products.id AND is_default = "yes" LIMIT 1) AS image')
        )
            ->join('lpse_price as lp', 'products.id', '=', 'lp.id_product')
            ->join('shop as s', 'products.id_shop', '=', 's.id')
            ->join('member_address as ma', 's.id_address', '=', 'ma.member_address_id')
            ->join('province as p', 'ma.province_id', '=', 'p.province_id')
            ->where('products.status_display', 'Y')
            ->where('products.status_delete', 'N')
            ->where('s.status', 'active')
            ->where('products.id_shop', $id_shop)
            ->orderBy('products.created_at', 'desc')
            ->get();

        return $products;
    }

    public function GetKategoryProductByIdshoplavel1($id_shop)
    {
        $categories = DB::table('products')
            ->select('pc.id', 'pc.name')
            ->join('product_category as pc', 'products.id_category', '=', 'pc.id')
            ->where('products.id_shop', $id_shop)
            ->where('pc.level', 1)
            ->distinct()
            ->get(); // Retrieve the result set

        return $categories;
    }

    public function GetKategoryProductByIdshoplavel2($id_shop)
    {
        $categories = DB::table('products')
            ->select('pc.id', 'pc.name')
            ->join('product_category as pc', 'products.id_category', '=', 'pc.id')
            ->where('products.id_shop', $id_shop)
            ->where('pc.level', 2)
            ->distinct()
            ->get(); // Retrieve the result set

        return $categories;
    }

    public function GetKategoryProductByIdshoplavel3($id_shop)
    {
        $categories = DB::table('products')
            ->select('pc.id', 'pc.name')
            ->join('product_category as pc', 'products.id_category', '=', 'pc.id')
            ->where('products.id_shop', $id_shop)
            ->where('pc.level', 3)
            ->distinct()
            ->get(); // Retrieve the result set

        return $categories;
    }

    public function GetProductByKategoriandIdShop($id_kategori, $id_shop)
    {
        $products = self::select(
            'products.*',
            'lp.price_lpse as hargaTayang',
            's.name as namaToko',
            's.id as idToko',
            'p.province_name',
            DB::raw('(SELECT image300 FROM product_image WHERE id_product = products.id AND is_default = "yes" LIMIT 1) AS image')
        )
            ->join('lpse_price as lp', 'products.id', '=', 'lp.id_product')
            ->join('shop as s', 'products.id_shop', '=', 's.id')
            ->join('member_address as ma', 's.id_address', '=', 'ma.member_address_id')
            ->join('province as p', 'ma.province_id', '=', 'p.province_id')
            ->where('products.status_display', 'Y')
            ->where('products.status_delete', 'N')
            ->where('s.status', 'active')
            ->where('products.id_shop', $id_shop)
            ->where('products.id_category', $id_kategori)
            ->get();

        return $products;
    }

    function getProductDetail($id_product)
    {
        $query = self::select(
            'products.*',
            'pc.barang_kena_ppn'
        )
            ->leftJoin('product_category as pc', 'pc.id', '=', 'products.id_category')
            ->where('products.id', $id_product)
            ->first();

        $image = $query->artwork_url_sm['0'];
        $query->image = $image;

        return $query;
    }

    function getproductactivebyId_shop($id_shop)
    {
        return self::select(
            'products.name',
            'products.id'
        )
            ->where('products.status_display', 'Y')
            ->where('products.status_delete', 'N')
            ->where('products.id_shop', $id_shop)
            ->get();
    }

    function getPricewithProduct($id_product)
    {
        $query  = DB::table('products as p')
            ->select(
                'p.price',
                'lp.price_lpse as harga_tayang'
            )
            ->join('lpse_price as lp', 'lp.id_product', 'p.id')
            ->where('p.id', $id_product)
            ->first();

        return $query;
    }

    function get_rank_produk_bySelling($id_shop)
    {
        $query = DB::table('products')
            ->select([
                'id',
                'name as info',
                'id_shop',
                // 'seoname',
                DB::raw('(SELECT SUM(qty) FROM complete_cart_shop_detail WHERE id_product = products.id) as count'),
                'price',
                'last_update'
            ])
            ->where('status_delete', 'N')
            ->where('id_shop', $id_shop)
            ->orderBy('count', 'desc')
            ->get();

        return $query;
    }

    function get_rank_produk_bySeen($id_shop)
    {
        $query = DB::table('products as p')
            ->select([
                'p.id',
                'p.name as info',
                'p.id_shop',
                // 'p.seoname',
                'p.price',
                'p.last_update',
                DB::raw('(SELECT SUM(count_view) FROM log_last_view WHERE id_product = p.id) as count')
            ])
            ->where('status_delete', 'N')
            ->where('p.id_shop', $id_shop)
            ->orderBy('count', 'desc')
            ->get();

        return $query;
    }

    public function getReviewsByIdshop($id_shop, $rating = null)
    {
        // Mulai query builder
        $query = DB::table('product_review_detail as prd')
            ->select(
                'pr.id as id_review',
                'prd.message',
                'prd.created',
                'cc.invoice',
                'ccs.id as id_ccs',
                'p.name',
                'prd.send_by',
                'pr.rating',
                'pr.id_product'
            )
            ->leftJoin('product_review as pr', 'pr.id', '=', 'prd.id_product_review')
            ->leftJoin('product as p', 'p.id', '=', 'pr.id_product')
            ->leftJoin('complete_cart as cc', 'cc.id', '=', 'pr.id_complete_cart')
            ->leftJoin('complete_cart_shop as ccs', 'ccs.id_cart', '=', 'cc.id')
            ->where('p.id_shop', $id_shop);

        // Tambahkan kondisi untuk rating jika ada
        if ($rating !== null) {
            $query->where('pr.rating', $rating);
        }

        // Ambil hasil dari query
        $results = $query->get();

        // Grupkan pesan berdasarkan id_review
        $groupedReviews = [];

        foreach ($results as $review) {
            $id_review = $review->id_review;

            // Jika id_review belum ada, buat array baru
            if (!isset($groupedReviews[$id_review])) {
                $groupedReviews[$id_review] = [
                    'id_review' => $review->id_review,
                    'name' => $review->name,
                    'rating' => $review->rating,
                    'invoice' => $review->invoice . '-' . $review->id_ccs,
                    'id_product' => $review->id_product,
                    'user_message' => null,  // Placeholder untuk user message
                    'seller_message' => null, // Placeholder untuk seller message
                ];
            }

            // Tentukan apakah pesan dari user atau seller
            if ($review->send_by == 'user') {
                $groupedReviews[$id_review]['user_message'] = [
                    'message' => $review->message,
                    'created' => $review->created,
                ];
            } elseif ($review->send_by == 'seller') {
                $groupedReviews[$id_review]['seller_message'] = [
                    'message' => $review->message,
                    'created' => $review->created,
                ];
            }
        }

        // Ubah hasil menjadi array
        $finalResult = array_values($groupedReviews);

        // Return data sebagai JSON
        return response()->json($finalResult);
    }

    function getCountRatingShop($id_shop)
    {
        $averageRatings = DB::table('product_review as pr')
            ->leftJoin('products as p', 'pr.id_product', '=', 'p.id')
            ->select(
                DB::raw('ROUND(SUM(pr.rating) / NULLIF(COUNT(pr.rating), 0), 1) as average_rating')
            )
            ->where('p.id_shop', $id_shop)
            ->value('average_rating');


        return $averageRatings;
    }

    function getPencarianProdukwithlimit($limit)
    {
        return DB::table('log_search')
            ->select([
                'log_search.*',
                'product_image.image50 as image'
            ])
            ->join('product_image', 'product_image.id_product', '=', 'log_search.first_product')
            ->orderBy('search_count', 'desc')
            ->limit($limit)
            ->get();
    }

    function getRandomSerach()
    {
        return DB::table('log_search')
            ->select([
                'log_search.*',
                'product_image.image50 as image'
            ])
            ->join('product_image', 'product_image.id_product', '=', 'log_search.first_product')
            ->inRandomOrder()
            ->limit(8)
            ->get();
    }

    function GetDetialProduct($id_product)
    {
        $query = DB::table('products as p')
            ->select(
                'p.*',
                'pc.barang_kena_ppn',
                'lp.price_lpse',
                'pv.id as id_video',
                'pv.link',
                'b.name as brand_name',
                DB::raw('(SELECT image300 FROM product_image WHERE id_product = p.id AND is_default = "yes" LIMIT 1) AS image'),
                DB::raw('(select name from product_category where id = p.id_category) as name_lvl3'),
                DB::raw('(select parent_id from product_category where id = p.id_category) as id_lvl2'),
                DB::raw('(select code from product_category where id = id_lvl2) as code_lvl2'),
                DB::raw('(select name from product_category where id = id_lvl2) as name_lvl2'),
                DB::raw('(select parent_id from product_category where id = id_lvl2) as id_lvl1'),
                DB::raw('(select code from product_category where id = id_lvl1) as code_lvl1'),
                DB::raw('(select name from product_category where id = id_lvl1) as name_lvl1')
            )
            ->leftJoin('brand as b', 'b.id', 'p.id_brand')
            ->leftJoin('product_video as pv', 'pv.id_product', '=', 'p.id')
            ->leftJoin('product_category as pc', 'pc.id', '=', 'p.id_category')
            ->leftJoin('lpse_price as lp', 'lp.id_product', '=', 'p.id')
            ->where('p.status_delete', 'N')
            ->where('p.id', $id_product);

        $results = $query->first();
        return $results;
    }

    function getwishmember($id_member)
    {
        return DB::table('member_wishlist as mw')
            ->select(
                'p.*',
                's.nama_pt',
                'l.price_lpse as harga_tayang',
                DB::raw('(SELECT image300 FROM product_image WHERE id_product = p.id AND is_default = "yes" LIMIT 1) AS image')
            )
            ->join('products as p', 'p.id', '=', 'mw.id_product')
            ->join('shop as s', 's.id', '=', 'p.id_shop')
            ->join('lpse_price as l', 'l.id_product', '=', 'p.id')
            ->where('mw.id_member', $id_member)
            ->get();
    }
}
