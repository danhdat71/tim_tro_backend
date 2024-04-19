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
        Schema::create('users_report_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->unsignedBigInteger('product_id')->index();
            $table->string('full_name', 100);
            $table->string('email', 100);
            $table->string('tel', 50);
            $table->tinyInteger('is_read')
                ->index()
                ->comment('1: is read, 0: unread');
            $table->tinyInteger('report_type')
                ->index()
                ->comment('1: info invalid, 2: address not found, 3: scam, 4: images not allow, 5: copy from other, 6: other');
            $table->text('description')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_report_products');
    }
};
