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
        Schema::table('tenants', function(Blueprint $table){
            $table->foreignId('account_id')->nullable()->constrained('accounts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function(Blueprint $table){
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });
    }
};
