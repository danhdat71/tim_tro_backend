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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('img_url');
            $table->string('organization')->nullable()->default(null);
            $table->tinyInteger('type')->comment('1: TOP_HEAD, 2: SIDE_LEFT, 3: SIDE_RIGHT, 4: OTHER')->index();
            $table->tinyInteger('status')->comment('1: Showing, 0: Hidden')->index();
            $table->dateTime('expired_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
