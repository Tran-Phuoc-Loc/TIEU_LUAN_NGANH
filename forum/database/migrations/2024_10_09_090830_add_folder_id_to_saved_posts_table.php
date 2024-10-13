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
        Schema::table('saved_posts', function (Blueprint $table) {
            $table->foreignId('folder_id')->nullable()->constrained()->after('post_id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saved_posts', function (Blueprint $table) {
            $table->dropForeign(['folder_id']); // Xóa khóa ngoại
            $table->dropColumn('folder_id'); // Xóa cột folder_id
        });
    }
};