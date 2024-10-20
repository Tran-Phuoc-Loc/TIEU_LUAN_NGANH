<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupJoinRequest extends Model
{
    use HasFactory;
    protected $fillable = ['group_id', 'user_id', 'status'];

    // Liên kết với người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Liên kết với nhóm
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
