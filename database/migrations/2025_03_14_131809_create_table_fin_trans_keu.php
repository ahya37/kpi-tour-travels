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
        Schema::create('trans_fin_keu', function (Blueprint $table) {
            $table->integer('trans_id', true)->primary(true);
            $table->string('trans_reference', length:30);
            $table->date('trans_date');
            $table->decimal('trans_total_amount', total:17, places:2);
            $table->string('trans_coa_id', length:30);
            $table->enum('trans_type', ['debit', 'kredit']);
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
        Schema::dropIfExists('trans_fin_keu');
    }
};
