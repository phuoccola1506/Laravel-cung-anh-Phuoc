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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Khóa cấu hình');
            $table->text('value')->nullable()->comment('Giá trị cấu hình');
            $table->string('type')->default('text')->comment('Loại dữ liệu: text, textarea, image, number, boolean');
            $table->string('group')->default('general')->comment('Nhóm: general, contact, social, payment, shipping');
            $table->string('label')->nullable()->comment('Nhãn hiển thị');
            $table->text('description')->nullable()->comment('Mô tả');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
