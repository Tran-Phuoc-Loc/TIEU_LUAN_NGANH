<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'file_path'];

    // Liên kết với model Post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
