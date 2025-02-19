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
        Schema::create('fin_mas_sa', function (Blueprint $table) {
            $table->string('sa_id', length:14)->primary(true);
            $table->integer('sa_periode_year');
            $table->integer('sa_periode_month');
            $table->string('sa_coa_code');
            $table->enum('sa_type', ['debit', 'kredit']);
            $table->decimal('sa_amount', total: 17, places:2);
            $table->integer('created_by');
            $table->integer('udpated_by');
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_mas_sa');
    }
};
