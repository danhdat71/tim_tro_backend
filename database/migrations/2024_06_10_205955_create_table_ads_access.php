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
        Schema::create('ads_access', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ads_id')->index();
            $table->unsignedBigInteger('user_id')
                ->index()
                ->nullable()
                ->default(null);
            $table->string('guest_ip', 20)
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
        Schema::dropIfExists('ads_access');
    }
};
