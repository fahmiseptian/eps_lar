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
}
