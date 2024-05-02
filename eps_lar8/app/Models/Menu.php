<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'ad_menu';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = ['nama', 'route', 'icon', 'urutan', 'status', 'parent_id', 'developer', 'superadmin', 'web_admin', 'finance', 'pajak', 'administration'];

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'id')->where('status', 2)->where(Session::get('access_code'), 1)->orderBy('urutan');
    }
}
