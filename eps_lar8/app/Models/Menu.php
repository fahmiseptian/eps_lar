<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'ad_menu';
    public $timestamps = false;
    protected $primaryKey = 'id'; 
    protected $fillable = ['nama', 'route', 'icon', 'urutan', 'status', 'parent_id'];

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'id');
    }
}
