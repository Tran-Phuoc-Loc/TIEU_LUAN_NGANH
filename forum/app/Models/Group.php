<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'creator_id', 'requires_approval', 'groups'];

    // Định nghĩa mối quan hệ nhiều-nhiều với model User
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id');
    }

    // Người tạo nhóm
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    // Quan hệ với yêu cầu tham gia
    public function memberRequests()
    {
        return $this->hasMany(GroupJoinRequest::class);
    }

    // Thành viên
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    // Định nghĩa quan hệ với bài viết
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function joinRequests()
    {
        return $this->hasMany(GroupJoinRequest::class);
    }
}
