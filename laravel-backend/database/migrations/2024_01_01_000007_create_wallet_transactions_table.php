<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 10, 2);
            $table->string('payment_type');
            $table->string('transaction_id')->nullable();
            $table->string('order_type')->nullable(); // city, intercity
            $table->enum('user_type', ['customer', 'driver']);
            $table->enum('type', ['credit', 'debit']);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'user_type', 'type', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
