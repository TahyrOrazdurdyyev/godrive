<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('intercity_services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image')->nullable();
            $table->boolean('enable')->default(true);
            $table->decimal('km_charge', 8, 2)->default(0);
            $table->json('admin_commission_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('enable');
        });
    }

    public function down()
    {
        Schema::dropIfExists('intercity_services');
    }
};

