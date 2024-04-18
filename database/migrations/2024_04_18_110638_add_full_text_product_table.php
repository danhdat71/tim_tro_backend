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
        Schema::table('products', function (Blueprint $table) {
            DB::statement('ALTER TABLE products ADD FULLTEXT search_index (title, description, detail_address)');
            DB::statement('ALTER TABLE products ENGINE = MyISAM');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop full-text index
            DB::statement('ALTER TABLE products DROP INDEX search_index');
            DB::statement('ALTER TABLE products ENGINE = InnoDB');
        });
    }
};
