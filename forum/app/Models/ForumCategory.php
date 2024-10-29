<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumCategory extends Model
{
    use HasFactory;
    
    protected $table = 'forum_categories';

    protected $fillable = ['name', 'description'];

    // Khai báo mối quan hệ với ForumPost
    public function posts()
    {
        return $this->hasMany(ForumPost::class, 'forum_category_id');
    }
}
