<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('discount', 8, 2);
            $table->enum('discount_type', ['percentage', 'fixed'])->default('fixed');
            $table->decimal('minimum_amount', 8, 2)->default(0);
            $table->decimal('maximum_discount', 8, 2)->nullable();
            $table->timestamp('expire_at');
            $table->boolean('enable')->default(true);
            $table->integer('user_limit')->default(1);
            $table->integer('used_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['code', 'enable', 'expire_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('coupons');
    }
};

