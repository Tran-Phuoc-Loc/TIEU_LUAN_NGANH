<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';
    protected $fillable = ['post_id', 'user_id', 'content', 'image_url', 'likes_count'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Định nghĩa mối quan hệ với Like
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Quan hệ để lấy các bình luận con
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // Quan hệ để lấy bình luận cha (nếu có)
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
}
