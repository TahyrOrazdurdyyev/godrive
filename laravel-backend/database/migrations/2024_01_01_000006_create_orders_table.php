<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            
            $table->string('source_location_name');
            $table->string('destination_location_name');
            $table->decimal('source_lat', 10, 8);
            $table->decimal('source_lng', 11, 8);
            $table->decimal('destination_lat', 10, 8);
            $table->decimal('destination_lng', 11, 8);
            
            $table->decimal('distance', 8, 2);
            $table->string('distance_type')->default('km');
            $table->string('duration')->nullable();
            $table->decimal('offer_rate', 8, 2)->default(0);
            $table->decimal('final_rate', 8, 2)->default(0);
            
            $table->string('payment_type')->default('cash');
            $table->boolean('payment_status')->default(false);
            $table->string('status')->default('placed');
            $table->string('otp')->nullable();
            
            $table->boolean('is_ac_selected')->default(false);
            $table->decimal('ac_non_ac_charges', 8, 2)->default(0);
            $table->decimal('total_holding_charges', 8, 2)->default(0);
            $table->integer('ride_hold_time_minutes')->default(0);
            
            $table->json('accepted_driver_ids')->nullable();
            $table->json('rejected_driver_ids')->nullable();
            $table->json('position_data')->nullable();
            $table->timestamp('accept_hold_time')->nullable();
            
            $table->json('tax_data')->nullable();
            $table->json('coupon_data')->nullable();
            $table->json('someone_else_data')->nullable();
            $table->json('admin_commission_data')->nullable();
            $table->json('vehicle_information_data')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'driver_id', 'status', 'created_at']);
            $table->index(['source_lat', 'source_lng']);
            $table->index(['destination_lat', 'destination_lng']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
