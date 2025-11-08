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
        // Chuyển cột amount từ DECIMAL sang BIGINT UNSIGNED
        DB::statement('ALTER TABLE discounts MODIFY COLUMN amount BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback về DECIMAL(10,2)
        DB::statement('ALTER TABLE discounts MODIFY COLUMN amount DECIMAL(10,2) NULL');
    }
};
