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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('full_name', 100);
            $table->string('avatar')
                ->nullable()
                ->default(null);
            $table->string('app_id', 100)->unique();
            $table->string('email', 100)->unique();
            $table->string('tel', 50)->unique();
            $table->tinyInteger('gender')
                ->nullable()
                ->default(null)
                ->comment('0: male, 1: female, 2: other');
            $table->tinyInteger('user_type')
                ->comment('0: provider, 1: finder, 10: admin');
            $table->dateTime('birthday')
                ->nullable()
                ->default(null);
            $table->text('description')
                ->nullable()
                ->default(null);
            $table->string('verify_otp')
                ->nullable()
                ->default(null);
            $table->timestamp('otp_expired_at')->nullable()->default(null);
            $table->tinyInteger('status')
                ->nullable()
                ->default(0)
                ->comment('0:inactive, 1:active, 2:leave');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
