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
        Schema::create('fin_mas_payment_umrah', function (Blueprint $table) {
            $table->string('tour_code');
            $table->enum('category', ['tiket', 'fee_pembimbing', 'manasik', 'la', 'tiket_museum', 'perlengkapan_jemaah', 'handling', 'akomodasi', 'kereta_cepat', 'lain_lain','asuransi']);
            $table->string('category_description', length:100);
            $table->string('doc_reff', length:25)->nullable(true);
            $table->string('created_by', length:3);
            $table->dateTime('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('updated_by', length:3)->nullable(true);
            $table->dateTime('updated_date')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_mas_payment_umrah');
    }
};
