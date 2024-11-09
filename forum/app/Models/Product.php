<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class Product extends Model
{
    use HasFactory;

    // Danh sách các cột có thể được gán hàng loạt
    protected $fillable = [
        'user_id',
        'product_category_id',
        'name',
        'description',
        'price',
        'status',
        'image',
    ];

    // Định nghĩa quan hệ với ProductCategory
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    // Thiết lập quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Khi xóa bản ghi trong CSDL, xóa luôn tệp ảnh
    protected static function booted()
    {
        static::deleted(function ($product) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
        });
    }
}
