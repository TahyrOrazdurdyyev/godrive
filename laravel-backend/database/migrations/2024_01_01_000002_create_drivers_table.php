<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('firebase_uid')->unique()->nullable();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('country_code')->nullable();
            $table->enum('login_type', ['email', 'phone', 'google', 'apple'])->default('email');
            $table->string('profile_pic')->nullable();
            $table->string('fcm_token')->nullable();
            $table->boolean('is_online')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('document_verification')->default(false);
            $table->unsignedBigInteger('service_id')->nullable();
            $table->integer('reviews_count')->default(0);
            $table->decimal('reviews_sum', 8, 2)->default(0);
            $table->decimal('wallet_amount', 10, 2)->default(0);
            $table->decimal('location_lat', 10, 8)->nullable();
            $table->decimal('location_lng', 11, 8)->nullable();
            $table->decimal('rotation', 8, 2)->nullable();
            $table->json('vehicle_information')->nullable();
            $table->json('zone_ids')->nullable();
            $table->unsignedBigInteger('subscription_plan_id')->nullable();
            $table->integer('subscription_total_orders')->default(0);
            $table->timestamp('subscription_expiry_date')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['firebase_uid', 'email', 'phone_number', 'is_online', 'is_active']);
            $table->index(['location_lat', 'location_lng']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('drivers');
    }
};
