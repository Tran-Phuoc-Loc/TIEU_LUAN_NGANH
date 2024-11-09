<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMessage extends Model
{
    use HasFactory;
    protected $fillable = ['sender_id', 'receiver_id', 'product_id', 'content'];

     // Quan hệ với người gửi
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Quan hệ với người nhận
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    // Quan hệ với sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
