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
        Schema::create('employee_applies', function (Blueprint $table) {
            $table->id();
            //Data
            $table->string('first_name');
            $table->string('last_name');
            $table->text('address');
            $table->string('phone')->unique();
            $table->string('email')->unique();

            $table->date('birthday')->nullable();
            $table->string('id_type')->default('national');
            $table->string('id_number');

            //Education
            $table->string('education_type')->nullable();
            $table->string('university')->nullable();
            $table->string('college')->nullable();
            $table->string('department')->nullable();

            //HR
            $table->string('position');
            $table->text('hr_cover_letter')->nullable();
            $table->boolean('has_insurance')->default(0);
            $table->string('insurance_number')->nullable();
            $table->double('explicated_salary')->default(0);
            $table->date('start_at')->nullable();

            //Status
            $table->string('status')->default('pending');

            //HR Notes
            $table->text('hr_notes')->nullable();
            $table->text('tech_notes')->nullable();

            //Options
            $table->boolean('is_activated')->default(0)->nullable();
            $table->boolean('ready_for_interview')->default(0)->nullable();
            $table->boolean('hr_approved')->default(0)->nullable();
            $table->foreignId('hr_approved_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->boolean('tech_approved')->default(0)->nullable();
            $table->foreignId('tech_approved_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->boolean('is_approved')->default(0)->nullable();
            $table->foreignId('is_approved_by')->nullable()->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applies');
    }
};
