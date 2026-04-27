<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kampus5s', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id')->nullable();
            $table->string('name')->nullable();
            $table->string('domain')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kampus5s');
    }
};