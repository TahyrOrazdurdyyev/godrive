<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('order_id');
            $table->text('message');
            $table->enum('sender_type', ['customer', 'driver']);
            $table->enum('message_type', ['text', 'image', 'video'])->default('text');
            $table->string('file_url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
            $table->index(['customer_id', 'driver_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversations');
    }
};
