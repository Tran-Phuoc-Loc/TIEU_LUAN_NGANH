<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedPost extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'post_id', 'folder_id',];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}
