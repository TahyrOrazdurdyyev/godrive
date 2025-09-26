<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firebase_uid')->unique()->nullable();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('country_code')->nullable();
            $table->enum('login_type', ['email', 'phone', 'google', 'apple'])->default('email');
            $table->string('profile_pic')->nullable();
            $table->string('fcm_token')->nullable();
            $table->integer('reviews_count')->default(0);
            $table->decimal('reviews_sum', 8, 2)->default(0);
            $table->decimal('wallet_amount', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['firebase_uid', 'email', 'phone_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};

