<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->string('redirect_url')->nullable();
            $table->boolean('enable')->default(true);
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['enable', 'position']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('banners');
    }
};

