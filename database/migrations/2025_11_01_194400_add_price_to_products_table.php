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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->nullable()->after('description');
        });

        // Cập nhật giá cho các sản phẩm đã tồn tại bằng giá thấp nhất của variants
        DB::statement('
            UPDATE products p
            SET p.price = (
                SELECT MIN(pv.price)
                FROM product_variants pv
                WHERE pv.product_id = p.id
            )
            WHERE EXISTS (
                SELECT 1
                FROM product_variants pv2
                WHERE pv2.product_id = p.id
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
