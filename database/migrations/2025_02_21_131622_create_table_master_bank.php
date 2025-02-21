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
        Schema::create('fin_mas_bank', function (Blueprint $table) {
            $table->id('bank_id');
            $table->string('bank_name', length:100);
            $table->string('bank_code', length:3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_mas_bank');
    }
};
