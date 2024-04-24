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
    protected $appends = ['artwork_url_lg'];

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

}
