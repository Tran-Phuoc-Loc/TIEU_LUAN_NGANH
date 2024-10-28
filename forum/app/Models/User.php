<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Group[] $groups
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany groups()
 * @mixin \Illuminate\Database\Eloquent\Model
 * @property \Illuminate\Database\Eloquent\Collection $groups
 */

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'profile_picture',
        'last_activity_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function getPostCountAttribute()
    {
        return $this->posts()->count();
    }

    // Mối quan hệ với bảng role
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hasRole($role)
    {
        // Vai trò người dùng trong cột 'role' trong bảng 'users'
        return $this->role === $role;
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Các nhóm mà người dùng tham gia
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user', 'user_id', 'group_id');
    }

    public function primaryGroup()
    {
        return $this->groups()->first(); // Lấy nhóm đầu tiên 
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    // Quan hệ với SavedPost
    public function savedPosts()
    {
        return $this->hasMany(SavedPost::class);
    }

    // Lấy tất cả yêu cầu kết bạn đã gửi
    public function sentFriendRequests() {
        return $this->hasMany(Friendship::class, 'sender_id');
    }

    // Lấy tất cả yêu cầu kết bạn đã nhận
    public function receivedFriendRequests() {
        return $this->hasMany(Friendship::class, 'receiver_id');
    }

    // Lấy danh sách bạn bè (chỉ yêu cầu đã được chấp nhận)
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friendships', 'sender_id', 'receiver_id')
                    ->wherePivot('status', 'accepted')
                    ->withPivot('status')
                    ->withTimestamps()
                    ->union(
                        $this->belongsToMany(User::class, 'friendships', 'receiver_id', 'sender_id')
                             ->wherePivot('status', 'accepted')
                             ->withPivot('status')
                             ->withTimestamps()
                    );
    }

    // Quan hệ khi người dùng là người gửi yêu cầu
    public function friendsOfMine() {
        return $this->belongsToMany(User::class, 'friendships', 'sender_id', 'receiver_id')
                    ->wherePivot('status', 'accepted')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    // Quan hệ khi người dùng là người nhận yêu cầu
    public function friendOf() {
        return $this->belongsToMany(User::class, 'friendships', 'receiver_id', 'sender_id')
                    ->wherePivot('status', 'accepted')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    // Accessor để kết hợp cả hai quan hệ
    public function getFriendsAttribute() {
        return $this->friendsOfMine->merge($this->friendOf);
    }

}
