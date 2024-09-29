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
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id')->nullable()->after('id'); // Thêm cột group_id
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade'); // Tạo khóa ngoại liên kết với bảng groups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['group_id']); // Xóa khóa ngoại
            $table->dropColumn('group_id'); // Xóa cột group_id
        });
    }
};
