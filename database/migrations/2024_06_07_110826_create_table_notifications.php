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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->index();
            $table->string('description', 255)->index()->nullable()->default(null);
            $table->tinyInteger('status')->default(0)->index()->comment('0: unread, 1: read');
            $table->string('link')->nullable()->default(null);
            $table->foreignId('user_id')->nullable()->default(null)->index();
            $table->dateTime('sent_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
