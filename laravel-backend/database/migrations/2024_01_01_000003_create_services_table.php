<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image')->nullable();
            $table->boolean('enable')->default(true);
            $table->boolean('offer_rate')->default(false);
            $table->boolean('intercity_type')->default(false);
            $table->boolean('is_ac_non_ac')->default(false);
            $table->decimal('ac_charge', 8, 2)->default(0);
            $table->decimal('non_ac_charge', 8, 2)->default(0);
            $table->decimal('basic_fare', 8, 2)->default(0);
            $table->decimal('basic_fare_charge', 8, 2)->default(0);
            $table->integer('holding_minute')->default(0);
            $table->decimal('holding_minute_charge', 8, 2)->default(0);
            $table->time('start_night_time')->nullable();
            $table->time('end_night_time')->nullable();
            $table->decimal('night_charge', 8, 2)->default(0);
            $table->decimal('per_minute_charge', 8, 2)->default(0);
            $table->decimal('km_charge', 8, 2)->default(0);
            $table->json('admin_commission_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['enable', 'intercity_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
};

