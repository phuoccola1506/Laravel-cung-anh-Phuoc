<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Bỏ các cột liên quan đến variant
            if (Schema::hasColumn('products', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('products', 'discount_price')) {
                $table->dropColumn('discount_price');
            }
            if (Schema::hasColumn('products', 'stock')) {
                $table->dropColumn('stock');
            }
            if (Schema::hasColumn('products', 'color')) {
                $table->dropColumn('color');
            }
            if (Schema::hasColumn('products', 'storage')) {
                $table->dropColumn('storage');
            }
            if (Schema::hasColumn('products', 'ram')) {
                $table->dropColumn('ram');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Thêm lại các cột nếu rollback
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('color')->nullable();
            $table->string('storage')->nullable();
            $table->string('ram')->nullable();
        });
    }
};
