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
        Schema::create('system_counters', function (Blueprint $table) {
            $table->id();
            $table->integer('hostel_count');
            $table->integer('full_house_count');
            $table->integer('apartment_count');
            $table->integer('together_count');
            $table->dateTime('created_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_counters');
    }
};
