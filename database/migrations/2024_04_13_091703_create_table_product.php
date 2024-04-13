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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('ward_id')->index();
            $table->unsignedBigInteger('province_id')->index();
            $table->unsignedBigInteger('district_id')->index();


            $table->string('title', 100)->index();
            $table->string('slug', 255)->index();
            $table->integer('price')->index();
            $table->text('description');
            $table->string('tel', 20);
            $table->string('detail_address', 200);
            $table->double('lat');
            $table->double('long');
            $table->integer('acreage')->index();
            $table->tinyInteger('bed_rooms')->index()
                ->comment('số phòng ngủ');
            $table->tinyInteger('toilet_rooms')->index()
                ->comment('số phòng wc');
            $table->tinyInteger('used_type')->index()
                ->comment('1: trọ, 2: nhà nguyên căn, 3: sleepbox, 4: chung cư, 5: văn phòng, 6: khác');
            $table->tinyInteger('is_shared_house')->index()
                ->comment('0: không chung chủ, 1: chung chủ');
            $table->tinyInteger('time_rule')->index()
                ->comment('0: tự do, 1: quy định');
            $table->tinyInteger('is_allow_pet')->index()
                ->comment('1: không cho phép, 2: cho & cam kết, 3: tự do');
            $table->dateTime('posted_at')->nullable()->default(null);
            $table->tinyInteger('status')
                ->default(1)
                ->comment('0: draft, 1: reality, 3: hidden');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
