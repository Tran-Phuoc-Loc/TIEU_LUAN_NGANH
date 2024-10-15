<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedPost extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'post_id', 'folder_id',];

    // Một bài viết đã lưu thuộc về một bài viết gốc
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Một bài viết đã lưu thuộc về một thư mục
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
