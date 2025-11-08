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
        Schema::table('discounts', function (Blueprint $table) {
            // Thêm cột type với enum: percentage, amount, shipping
            $table->enum('type', ['percentage', 'amount', 'shipping'])
                  ->default('percentage')
                  ->after('code')
                  ->comment('percentage = giảm %, amount = giảm tiền, shipping = miễn phí ship');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
