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
        // Thêm cột active vào bảng categories
        Schema::table('categories', function (Blueprint $table) {
            $table->tinyInteger('active')->default(1)->after('updated_at');
        });

        // Thêm cột active vào bảng brands
        Schema::table('brands', function (Blueprint $table) {
            $table->tinyInteger('active')->default(1)->after('updated_at');
        });

        // Thêm cột active vào bảng products
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('active')->default(1)->after('updated_at');
        });

        // Thêm cột active vào bảng product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->tinyInteger('active')->default(1)->after('updated_at');
        });

        // Thêm cột active vào bảng discounts (đã có cột active rồi, bỏ qua)
        // Schema::table('discounts', function (Blueprint $table) {
        //     $table->tinyInteger('active')->default(1)->after('updated_at');
        // });

        // Thêm cột active vào bảng users
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('active')->default(1)->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('active');
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('active');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('active');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('active');
        });

        // Schema::table('discounts', function (Blueprint $table) {
        //     $table->dropColumn('active');
        // });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
};
