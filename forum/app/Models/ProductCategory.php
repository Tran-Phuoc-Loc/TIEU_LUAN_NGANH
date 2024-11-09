<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    // Danh sách các cột có thể được gán hàng loạt
    protected $fillable = [
        'name',
        'description'
    ];

    // Định nghĩa quan hệ với Product
    public function products()
    {
        return $this->hasMany(Product::class, 'product_category_id');
    }
}
