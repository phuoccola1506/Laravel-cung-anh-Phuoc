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
        Schema::create('discount_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discount_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('used')->default(0)->comment('0 = chưa dùng, 1 = đã dùng');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('used_at')->nullable();
            
            // Foreign keys
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Index để tìm kiếm nhanh
            $table->index(['discount_id', 'user_id']);
            $table->index('used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_user');
    }
};
