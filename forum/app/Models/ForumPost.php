<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ForumPost extends Model
{
    use HasFactory;

    protected $table = 'forum_posts';

    protected $fillable = ['user_id', 'forum_category_id', 'title', 'content'];

    // Khai báo mối quan hệ với ForumCategory
    public function category()
    {
        return $this->belongsTo(ForumCategory::class, 'forum_category_id');
    }

    // Khai báo mối quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class);
    }
}
