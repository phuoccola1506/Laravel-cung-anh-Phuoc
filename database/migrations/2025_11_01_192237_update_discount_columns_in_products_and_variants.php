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
        // Xóa cột discount ở bảng products
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('discount');
        });

        // Đổi tên cột discount_price thành discount ở bảng product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->renameColumn('discount_price', 'discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục lại cột discount ở bảng products
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('discount', 5, 2)->default(0)->after('price');
        });

        // Đổi tên cột discount về discount_price ở bảng product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->renameColumn('discount', 'discount_price');
        });
    }
};
