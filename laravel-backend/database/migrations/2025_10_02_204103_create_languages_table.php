<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // en, ru, tm
            $table->string('name'); // English, Русский, Türkmençe
            $table->boolean('is_default')->default(false);
            $table->boolean('enable')->default(true);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('languages');
    }
};
