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
        // Kiểm tra và drop existing foreign keys nếu tồn tại
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'discount_user' 
            AND TABLE_SCHEMA = DATABASE()
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        foreach ($foreignKeys as $fk) {
            DB::statement("ALTER TABLE discount_user DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        // Add foreign keys with ON UPDATE CASCADE
        Schema::table('discount_user', function (Blueprint $table) {
            $table->foreign('discount_id', 'fk_discount_user_discount')
                  ->references('id')->on('discounts')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            $table->foreign('user_id', 'fk_discount_user_user')
                  ->references('id')->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys with custom names
        Schema::table('discount_user', function (Blueprint $table) {
            $table->dropForeign('fk_discount_user_discount');
            $table->dropForeign('fk_discount_user_user');
        });

        // Add back original foreign keys
        Schema::table('discount_user', function (Blueprint $table) {
            $table->foreign('discount_id')
                  ->references('id')->on('discounts')
                  ->onDelete('cascade');
            
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }
};
