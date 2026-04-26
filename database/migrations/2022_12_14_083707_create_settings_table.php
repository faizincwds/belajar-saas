<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('settings.repositories.database.table') ?? 'settings', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id'); // Wajib buat multi tenant

            $table->string('group');
            $table->string('name');
            $table->boolean('locked')->default(false);
            $table->json('payload');

            $table->timestamps();

            $table->unique(['tenant_id', 'group', 'name']);
        });
    }
};
