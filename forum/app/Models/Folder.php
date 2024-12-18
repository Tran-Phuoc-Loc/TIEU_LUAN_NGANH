<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
    ];

    // Định nghĩa mối quan hệ với model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Mối quan hệ với bảng saved_posts
    public function savedPosts()
    {
        return $this->hasMany(SavedPost::class);
    }

    // Thiết lập quan hệ nhiều-nhiều với Post thông qua bảng trung gian saved_posts
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'saved_posts', 'folder_id', 'post_id');
    }
}
