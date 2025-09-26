<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add foreign keys after all tables are created
        Schema::table('drivers', function (Blueprint $table) {
            $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('set null');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('set null');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
        });

        Schema::table('intercity_orders', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
            $table->foreign('intercity_service_id')->references('id')->on('intercity_services')->onDelete('cascade');
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('set null');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
            $table->foreign('freight_vehicle_id')->references('id')->on('freight_vehicles')->onDelete('set null');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['subscription_plan_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['service_id']);
            $table->dropForeign(['zone_id']);
            $table->dropForeign(['coupon_id']);
        });

        Schema::table('intercity_orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['intercity_service_id']);
            $table->dropForeign(['zone_id']);
            $table->dropForeign(['coupon_id']);
            $table->dropForeign(['freight_vehicle_id']);
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['order_id']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['order_id']);
        });
    }
};

