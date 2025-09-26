<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('intercity_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('intercity_service_id');
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->unsignedBigInteger('freight_vehicle_id')->nullable();
            
            $table->string('source_city');
            $table->string('source_location_name');
            $table->string('destination_city');
            $table->string('destination_location_name');
            $table->decimal('source_lat', 10, 8);
            $table->decimal('source_lng', 11, 8);
            $table->decimal('destination_lat', 10, 8);
            $table->decimal('destination_lng', 11, 8);
            
            $table->decimal('distance', 8, 2);
            $table->string('distance_type')->default('km');
            $table->decimal('offer_rate', 8, 2)->default(0);
            $table->decimal('final_rate', 8, 2)->default(0);
            
            $table->string('payment_type')->default('cash');
            $table->boolean('payment_status')->default(false);
            $table->string('status')->default('placed');
            $table->string('otp')->nullable();
            
            $table->string('parcel_dimension')->nullable();
            $table->string('parcel_weight')->nullable();
            $table->json('parcel_images')->nullable();
            $table->date('when_date')->nullable();
            $table->time('when_time')->nullable();
            $table->integer('number_of_passenger')->nullable();
            $table->text('comments')->nullable();
            
            $table->json('accepted_driver_ids')->nullable();
            $table->json('rejected_driver_ids')->nullable();
            $table->json('position_data')->nullable();
            
            $table->json('tax_data')->nullable();
            $table->json('coupon_data')->nullable();
            $table->json('someone_else_data')->nullable();
            $table->json('admin_commission_data')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'driver_id', 'status', 'created_at']);
            $table->index(['source_lat', 'source_lng']);
            $table->index(['destination_lat', 'destination_lng']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('intercity_orders');
    }
};
