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
        Schema::create('bug_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('full_name', 100);
            $table->string('email', 100);
            $table->text('description');
            $table->string('ip_address', 50);
            $table->tinyInteger('status')
                ->default(1)
                ->index()
                ->comment('1: Waiting, 2: Viewed, 3: Not Fix, 4: Fixed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bug_reports');
    }
};
