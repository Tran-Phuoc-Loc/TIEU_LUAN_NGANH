<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Mews\Purifier\Facades\Purifier;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'content', 'likes_count', 'image_url', 'is_featured', 'status', 'slug', 'category_id',];

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = Purifier::clean($value); // Gọi Purifier trực tiếp
    }

    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // Bài viết chỉ thuộc 1 danh mục
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Kiểm tra xem ảnh/video chính là ảnh
    public function isImage()
    {
        return in_array(pathinfo($this->image_url, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']);
    }

    // Kiểm tra xem ảnh/video chính là video
    public function isVideo()
    {
        return in_array(pathinfo($this->image_url, PATHINFO_EXTENSION), ['mp4', 'avi', 'mov']);
    }

    public function postImages()
    {
        return $this->hasMany(PostImage::class); // Quan hệ 1-n với bảng post_images
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($post) {
            // Xóa tệp ảnh hoặc video của bài viết (nếu có)
            if ($post->image_url && file_exists(public_path('storage/' . $post->image_url))) {
                unlink(public_path('storage/' . $post->image_url));
            }

            // Xóa tất cả các ảnh liên quan từ bảng post_images và thư mục lưu trữ
            foreach ($post->postImages as $image) { //  $post->postImages
                if (file_exists(public_path('storage/' . $image->file_path))) {
                    unlink(public_path('storage/' . $image->file_path));
                }
                $image->delete(); // Xóa bản ghi trong bảng post_images
            }

            // Xóa tất cả bình luận liên quan nếu có mối quan hệ
            if (method_exists($post, 'comments')) {
                $post->comments()->delete();
            }
        });
    }
}
