<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'product_category';

    public function getCategoryBydata($data)
    {
        return $this->where($data)
                    ->whereNull('deleted_by')
                    ->orderBy('name', 'asc')
                    ->get();
    }
}
