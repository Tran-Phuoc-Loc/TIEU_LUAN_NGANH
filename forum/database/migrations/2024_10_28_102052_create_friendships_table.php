<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');  // ID người gửi yêu cầu
            $table->unsignedBigInteger('receiver_id'); // ID người nhận yêu cầu
            $table->enum('status', ['pending', 'accepted', 'declined', 'blocked'])->default('pending'); // Trạng thái
            $table->timestamps();

            // Tạo khóa ngoại để đảm bảo tính toàn vẹn dữ liệu
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');

            // Đảm bảo mỗi cặp người dùng chỉ có một yêu cầu
            $table->unique(['sender_id', 'receiver_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friendships');
    }
};
