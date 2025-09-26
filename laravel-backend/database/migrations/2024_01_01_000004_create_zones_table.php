<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('coordinates');
            $table->boolean('enable')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('enable');
        });
    }

    public function down()
    {
        Schema::dropIfExists('zones');
    }
};

