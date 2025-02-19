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
        Schema::create('fin_mas_coa', function (Blueprint $table) {
            $table->string('coa_id')->primary(true);
            $table->string('coa_desc', length: 100);
            $table->integer('coa_level');
            $table->string('coa_parent')->nullable(true);
            $table->string('created_by');
            $table->string('updated_by');
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_mas_coa');
    }
};
