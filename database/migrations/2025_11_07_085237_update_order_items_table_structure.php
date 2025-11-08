<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Kiểm tra và thêm các cột mới nếu chưa tồn tại
            if (!Schema::hasColumn('order_items', 'variant_id')) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('order_items', 'sku')) {
                $table->string('sku', 50)->nullable()->after('variant_id');
            }
            if (!Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->nullable()->after('sku');
            }
            if (!Schema::hasColumn('order_items', 'attributes')) {
                $table->json('attributes')->nullable()->after('product_name');
            }
        });
        
        // Drop và recreate cột price
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'price')) {
                $table->dropColumn('price');
            }
        });
        
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('price')->after('attributes')->comment('Giá 1 sản phẩm (VND)');
        });
        
        // Thêm total_price nếu chưa có
        if (!Schema::hasColumn('order_items', 'total_price')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->unsignedBigInteger('total_price')->storedAs('price * quantity')->after('quantity')->comment('Tổng giá = price * quantity');
            });
        }
        
        // Thêm foreign key nếu chưa có
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'variant_id')) {
                return;
            }
            
            // Check nếu foreign key chưa tồn tại
            $foreignKeys = DB::select("SELECT CONSTRAINT_NAME 
                                      FROM information_schema.KEY_COLUMN_USAGE 
                                      WHERE TABLE_SCHEMA = DATABASE() 
                                      AND TABLE_NAME = 'order_items' 
                                      AND COLUMN_NAME = 'variant_id' 
                                      AND REFERENCED_TABLE_NAME IS NOT NULL");
            
            if (empty($foreignKeys)) {
                $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['variant_id']);
            
            // Drop các cột mới
            $table->dropColumn(['variant_id', 'sku', 'product_name', 'attributes', 'price', 'total_price']);
        });
        
        // Khôi phục lại cột price với kiểu DECIMAL
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->after('quantity');
        });
    }
};
