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
        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->id();

            //Refs
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->string('department');

            $table->date('date');

            //Source
            $table->string('source')->default('fingerprint')->nullable();

            //Data
            $table->time('in_at');
            $table->time('out_at')->nullable();

            //Counters
            $table->double('delay')->default(0)->nullable();
            $table->double('overtime')->default(0)->nullable();
            $table->double('total')->default(0)->nullable();

            //Notes
            $table->text('notes')->nullable();
            $table->foreignId('note_by')->nullable()->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_attends');
    }
};
