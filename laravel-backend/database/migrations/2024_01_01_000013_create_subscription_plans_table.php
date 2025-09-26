<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('amount', 8, 2);
            $table->integer('duration_days');
            $table->integer('total_orders');
            $table->boolean('enable')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('enable');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_plans');
    }
};

