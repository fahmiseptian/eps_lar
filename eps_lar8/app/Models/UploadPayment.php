<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class UploadPayment extends Model implements HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia;

    protected $table = 'upload_payment';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['invoice', 'nominal', 'created_dt', 'file_upload'];
    protected $appends = ['url_img_payment'];

    public function registerMediaCollections(): void {
        $this
            ->addMediaCollection('upload_payment')
            ->useDisk('upload_payment')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif']);
    }


    public function getUrlImgPaymentAttribute() {
        $media = $this->getFirstMedia('upload_payment');
        if (!$media) {
            return null;
        } else {
            return $media->getFullUrl();
        }
    }
}
