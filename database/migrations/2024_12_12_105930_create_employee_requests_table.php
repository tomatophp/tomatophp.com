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
        Schema::create('employee_requests', function (Blueprint $table) {
            $table->id();

            //Refs
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

            $table->string('type')->default('holiday')->nullable();

            //Dates
            $table->dateTime('from')->nullable();
            $table->dateTime('to')->nullable();

            //Count
            $table->double('amount')->default(0)->nullable();
            $table->double('total')->default(0)->nullable();

            //Reason
            $table->text('request_message')->nullable();
            $table->text('request_response')->nullable();
            $table->foreignId('request_by')->nullable()->constrained('users')->onDelete('cascade');

            //Status
            $table->string('status')->default('pending')->nullable();

            //Options
            $table->boolean('is_activated')->default(0)->nullable();
            $table->boolean('is_approved')->default(0)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_requests');
    }
};
