<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can edit the model.
     *
     * @param  \App\Models\User  $authUser
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function edit(User $authUser, User $user)
    {
        // Chỉ cho phép người dùng chỉnh sửa thông tin cá nhân của họ
        return $authUser->id === $user->id;
    }
}

