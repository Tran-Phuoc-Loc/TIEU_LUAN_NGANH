<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'creator_id'];

    // Quan hệ với User
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user');
    }

    // Người tạo nhóm
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}

