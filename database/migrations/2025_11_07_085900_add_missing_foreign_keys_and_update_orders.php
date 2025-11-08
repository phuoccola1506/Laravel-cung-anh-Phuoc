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
        // 1. Thêm foreign key cho product_variants (product_id -> products)
        Schema::table('product_variants', function (Blueprint $table) {
            // Kiểm tra xem foreign key đã tồn tại chưa
            $foreignKeys = DB::select("SELECT CONSTRAINT_NAME 
                                      FROM information_schema.KEY_COLUMN_USAGE 
                                      WHERE TABLE_SCHEMA = DATABASE() 
                                      AND TABLE_NAME = 'product_variants' 
                                      AND COLUMN_NAME = 'product_id' 
                                      AND REFERENCED_TABLE_NAME = 'products'");
            
            if (empty($foreignKeys)) {
                $table->foreign('product_id', 'fk_variants_product')
                      ->references('id')->on('products')->onDelete('cascade');
            }
        });
        
        // 2. Thêm foreign keys cho order_items
        Schema::table('order_items', function (Blueprint $table) {
            // Check order_id foreign key
            $orderFK = DB::select("SELECT CONSTRAINT_NAME 
                                  FROM information_schema.KEY_COLUMN_USAGE 
                                  WHERE TABLE_SCHEMA = DATABASE() 
                                  AND TABLE_NAME = 'order_items' 
                                  AND COLUMN_NAME = 'order_id' 
                                  AND REFERENCED_TABLE_NAME = 'orders'");
            
            if (empty($orderFK)) {
                $table->foreign('order_id', 'fk_order_items_order')
                      ->references('id')->on('orders')->onDelete('cascade');
            }
            
            // Check product_id foreign key
            $productFK = DB::select("SELECT CONSTRAINT_NAME 
                                    FROM information_schema.KEY_COLUMN_USAGE 
                                    WHERE TABLE_SCHEMA = DATABASE() 
                                    AND TABLE_NAME = 'order_items' 
                                    AND COLUMN_NAME = 'product_id' 
                                    AND REFERENCED_TABLE_NAME = 'products'");
            
            if (empty($productFK)) {
                $table->foreign('product_id', 'fk_order_items_product')
                      ->references('id')->on('products')->onDelete('cascade');
            }
        });
        
        // 3. Update bảng orders - đổi total_amount sang các cột chi tiết
        if (Schema::hasColumn('orders', 'total_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('total_amount');
            });
        }
        
        Schema::table('orders', function (Blueprint $table) {
            // Thêm các cột mới nếu chưa có
            if (!Schema::hasColumn('orders', 'subtotal')) {
                $table->unsignedBigInteger('subtotal')->after('status')->comment('Tạm tính (VND)');
            }
            if (!Schema::hasColumn('orders', 'shipping_fee')) {
                $table->unsignedBigInteger('shipping_fee')->default(50000)->after('subtotal')->comment('Phí ship (VND)');
            }
            if (!Schema::hasColumn('orders', 'discount')) {
                $table->unsignedBigInteger('discount')->default(0)->after('shipping_fee')->comment('Giảm giá (VND)');
            }
            if (!Schema::hasColumn('orders', 'total')) {
                $table->unsignedBigInteger('total')->storedAs('subtotal + shipping_fee - discount')->after('discount')->comment('Tổng cộng (VND)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropForeign('fk_variants_product');
        });
        
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign('fk_order_items_order');
            $table->dropForeign('fk_order_items_product');
        });
        
        // Restore orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'shipping_fee', 'discount', 'total']);
        });
        
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->after('discount_id');
        });
    }
};
