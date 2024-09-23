<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
    
        // Xác thực thông tin người dùng
        $validatedData = $request->validated();
        
        // Kiểm tra xem có file ảnh không
        if ($request->hasFile('avatar')) {
            // Xác thực file ảnh
            $request->validate([
                'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Giới hạn kích thước và định dạng
            ]);
    
            // Lưu file ảnh
            $imagePath = $request->file('avatar')->store('avatar', 'public');
    
            // Cập nhật đường dẫn ảnh vào thông tin người dùng
            $validatedData['avatar'] = $imagePath;
        }
    
        // Cập nhật thông tin người dùng
        $user->fill($validatedData);
    
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
    
        $user->save();
    
        return Redirect::route('profile.edit')->with('status', 'Hồ sơ của bạn đã được cập nhật thành công!');
    }
    
    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
