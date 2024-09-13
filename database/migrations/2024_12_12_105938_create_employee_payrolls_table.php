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
        Schema::create('employee_payrolls', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');

            //Date
            $table->string('year');
            $table->string('month');
            $table->dateTime('date');

            //Collect Time
            $table->double('total_time')->default(0)->nullable();
            $table->double('offs_time')->default(0)->nullable();
            $table->double('overtime_time')->default(0)->nullable();
            $table->double('delay_time')->default(0)->nullable();

            //Collect Money
            $table->double('out_date_payments')->default(0)->nullable();
            $table->double('subscription')->default(0)->nullable();
            $table->double('tax')->default(0)->nullable();

            //Collect Total
            $table->double('total')->default(0)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_payrolls');
    }
};
