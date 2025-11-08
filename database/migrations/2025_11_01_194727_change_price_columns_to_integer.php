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
        // Chuyển đổi cột price trong bảng products từ decimal sang bigint
        DB::statement('ALTER TABLE products MODIFY COLUMN price BIGINT UNSIGNED NULL');

        // Chuyển đổi cột price và discount trong bảng product_variants
        DB::statement('ALTER TABLE product_variants MODIFY COLUMN price BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE product_variants MODIFY COLUMN discount TINYINT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE product_variants MODIFY COLUMN stock INT UNSIGNED NOT NULL DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Chuyển lại về decimal
        DB::statement('ALTER TABLE products MODIFY COLUMN price DECIMAL(15,2) NULL');
        DB::statement('ALTER TABLE product_variants MODIFY COLUMN price DECIMAL(15,2) NOT NULL');
        DB::statement('ALTER TABLE product_variants MODIFY COLUMN discount DECIMAL(5,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE product_variants MODIFY COLUMN stock INT NOT NULL DEFAULT 0');
    }
};
