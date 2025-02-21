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
        Schema::create('fin_mas_bank_account', function (Blueprint $table) {
            $table->id('bank_account_id');
            $table->integer('bank_id');
            $table->string('bank_account_number', length:100);
            $table->string('bank_account_currency', length:3);
            $table->enum('is_active', ['t', 'f'])->default('t');
            $table->string('coa_id', length:100);
            $table->string('created_by', length:3);
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('updated_by', length:3);
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_mas_bank_account');
    }
};
