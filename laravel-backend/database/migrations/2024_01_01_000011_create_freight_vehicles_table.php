<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('freight_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image')->nullable();
            $table->boolean('enable')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('enable');
        });
    }

    public function down()
    {
        Schema::dropIfExists('freight_vehicles');
    }
};

