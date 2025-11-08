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
            if (!Schema::hasColumn('discounts', 'type')) {
                $table->string('type')->default('percentage')->after('code');
            }
            if (!Schema::hasColumn('discounts', 'value')) {
                $table->decimal('value', 10, 2)->nullable()->after('type');
            }
            if (!Schema::hasColumn('discounts', 'usage_limit')) {
                $table->integer('usage_limit')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('discounts', 'used_count')) {
                $table->integer('used_count')->default(0)->after('usage_limit');
            }
            if (!Schema::hasColumn('discounts', 'min_purchase')) {
                $table->decimal('min_purchase', 10, 2)->nullable()->after('used_count');
            }
            if (!Schema::hasColumn('discounts', 'max_discount')) {
                $table->decimal('max_discount', 10, 2)->nullable()->after('min_purchase');
            }
            if (!Schema::hasColumn('discounts', 'description')) {
                $table->text('description')->nullable()->after('max_discount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'value',
                'usage_limit',
                'used_count',
                'min_purchase',
                'max_discount',
                'description'
            ]);
        });
    }
};
