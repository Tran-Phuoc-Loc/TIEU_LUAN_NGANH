<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    // Các thuộc tính có thể được gán hàng loạt
    // Các cột này sẽ được sử dụng khi tạo hoặc cập nhật nhóm trong cơ sở dữ liệu
    protected $fillable = ['name', 'description', 'creator_id', 'requires_approval', 'groups'];

    /**
     * Định nghĩa mối quan hệ nhiều-nhiều với model `User`.
     * Một nhóm có thể có nhiều người dùng và một người dùng có thể tham gia nhiều nhóm.
     * Mối quan hệ này sử dụng bảng trung gian `group_user`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id');
    }

    /**
     * Định nghĩa mối quan hệ một-nhiều với model `User` cho người tạo nhóm.
     * Mỗi nhóm có một người tạo, liên kết qua cột `creator_id`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Kiểm tra xem người dùng hiện tại có phải là người tạo nhóm hay không.
     *
     * @param int $userId ID của người dùng cần kiểm tra
     * @return bool True nếu người dùng là chủ nhóm, False nếu không
     */
    public function isOwner($userId)
    {
        return $this->creator_id === $userId;
    }

    /**
     * Định nghĩa mối quan hệ một-nhiều với model `GroupJoinRequest`.
     * Một nhóm có thể có nhiều yêu cầu tham gia từ các người dùng khác.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function memberRequests()
    {
        return $this->hasMany(GroupJoinRequest::class);
    }

    /**
     * Định nghĩa mối quan hệ nhiều-nhiều với model `User` cho các thành viên của nhóm.
     * Quan hệ này sử dụng bảng trung gian `group_user` và lưu trữ thời gian tham gia qua `created_at`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id')
            ->withPivot('created_at');
    }

    /**
     * Định nghĩa mối quan hệ một-nhiều với model `Chat`.
     * Một nhóm có thể có nhiều tin nhắn chat.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    /**
     * Định nghĩa mối quan hệ một-nhiều với model `Post`.
     * Một nhóm có thể có nhiều bài viết.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Định nghĩa mối quan hệ một-nhiều với model `GroupJoinRequest`.
     * Một nhóm có thể có nhiều yêu cầu tham gia.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function joinRequests()
    {
        return $this->hasMany(GroupJoinRequest::class);
    }

    /**
     * Kiểm tra nếu người dùng đã gửi yêu cầu tham gia nhóm.
     * Chỉ kiểm tra các yêu cầu có trạng thái `pending`.
     *
     * @param int $userId ID của người dùng cần kiểm tra
     * @return bool True nếu đã có yêu cầu tham gia, False nếu không
     */
    public function hasJoinRequest($userId)
    {
        return $this->joinRequests()->where('user_id', $userId)->where('status', 'pending')->exists();
    }

    /**
     * Kiểm tra nếu người dùng hiện tại đã là thành viên của nhóm hay chưa.
     * Sử dụng mối quan hệ `users` đã được load trước đó để tránh truy vấn lặp lại.
     *
     * @param User $user Đối tượng người dùng cần kiểm tra
     * @return bool True nếu người dùng là thành viên, False nếu không
     */
    public function isMember(User $user)
    {
        // Kiểm tra nếu `users` đã được load trước đó
        if ($this->relationLoaded('users')) {
            return $this->users->contains($user);
        }

        // Truy vấn trực tiếp từ cơ sở dữ liệu nếu chưa load
        return $this->users()->where('user_id', $user->id)->exists();
    }
}
