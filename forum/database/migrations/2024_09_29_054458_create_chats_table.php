<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id(); // Tạo cột id, kiểu bigint tự động tăng, làm khóa chính cho bảng
            $table->foreignId('group_id')->constrained()->onDelete('cascade'); // Cột group_id, liên kết với bảng groups, xóa liên kết khi nhóm bị xóa
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Cột user_id, liên kết với bảng users, xóa liên kết khi người dùng bị xóa
            $table->text('message'); // Cột message, lưu trữ nội dung tin nhắn
            $table->timestamps(); // Tạo hai cột created_at và updated_at để lưu thời gian tạo và cập nhật
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
