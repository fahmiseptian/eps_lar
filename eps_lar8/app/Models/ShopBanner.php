<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ShopBanner extends Model implements HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia;

    protected $table = 'shop_banner';
    public $timestamps = false;

    function getBannerbyTipe($tipe, $limit = null)
    {
        $query = DB::table('banner')
            ->where('category_banner_id', $tipe)
            ->where('active', 'Y');
        if ($limit) {
            $query->limit($limit);
        }
        return $query->get();
    }
}
