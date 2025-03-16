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
        Schema::create('fin_trans_journal', function (Blueprint $table) {
            $table->string('journal_id', length:13)->primary();
            $table->date('journal_date');
            $table->string('journal_description', length:255)->nullable(true);
            $table->string('journal_coa_id', length:15);
            $table->string('journal_currency', length:3);
            $table->decimal('journal_total_amount', total:17, places:2)->default(0);
            $table->enum('journal_type', ['debit', 'credit']);
            $table->string('journal_reff_code', length:30);
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
        Schema::dropIfExists('fin_trans_journal');
    }
};
