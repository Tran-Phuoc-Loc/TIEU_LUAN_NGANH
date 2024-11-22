<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Cho phép tất cả người dùng
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required',
            'group_id' => 'nullable|exists:groups,id', // Kiểm tra nhóm hợp lệ
            'category_id' => 'required|exists:categories,id',
            // Tệp đơn: Ảnh tối đa 5MB, Video tối đa 60MB
            'media_single' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:61200', // 60MB
            // Nhiều ảnh: Tối đa 5MB mỗi ảnh
            'media_multiple.*' => 'nullable|array|max:10', // Số ượng tối đa có thể đăng lên
            'media_multiple.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB mỗi ảnh
            'status' => 'required|in:draft,published',
        ];

        // Kiểm tra nếu `media_single` là video thì cấm `media_multiple`
        if (request()->hasFile('media_single') && str_contains(request()->file('media_single')->getMimeType(), 'video')) {
            $rules['media_multiple'] = 'prohibited';
        }

        return $rules;
    }
}
