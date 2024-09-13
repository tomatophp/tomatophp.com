<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance_shifts', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique()->index();

            $table->string('department')->nullable();

            $table->string('type')->default('master')->nullable();

            $table->time('start_at');
            $table->time('end_at');

            $table->json('offs')->nullable();

            $table->boolean('is_activated')->default(true)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_shifts');
    }
};
