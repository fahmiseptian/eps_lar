<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
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

    public function getProductByIdShop($id_shop, $where) {

		return self::where('id_shop', $id_shop)
                   ->where('status_delete', 'N')
                   ->where($where)
                   ->select('id')
                   ->count();
	}

    public function getproduct() {
        return self::select(
            'products.*',
            'lp.price_lpse as hargaTayang',
            's.name as namaToko',
            'p.province_name'
        )
        ->join('lpse_price as lp', 'products.id', '=', 'lp.id_product')
        ->join('shop as s', 'products.id_shop', '=', 's.id')
        ->join('member_address as ma', 's.id_address', '=', 'ma.member_address_id')
        ->join('province as p', 'ma.province_id', '=', 'p.province_id')
        ->where('products.status_display','Y')
        ->where('products.status_delete','N')
        ->where('s.status','active')
        ->get();
    }

}
