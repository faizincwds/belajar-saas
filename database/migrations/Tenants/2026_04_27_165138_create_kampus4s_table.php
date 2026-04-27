<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kampus4s', function (Blueprint $table) {
            $table->uuid('id')->primary();
// Default only ID & Timestamps
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kampus4s');
    }
};