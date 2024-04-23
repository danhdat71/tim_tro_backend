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
        Schema::create('users_viewed_products', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')
                ->index();
            $table->unsignedBigInteger('user_id')
                ->index()
                ->nullable()
                ->default(null);
            $table->string('guest_ip')
                ->index()
                ->nullable()
                ->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_viewed_products');
    }
};
