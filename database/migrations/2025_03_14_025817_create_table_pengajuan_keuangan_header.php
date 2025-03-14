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
        Schema::create('fin_trans_pengajuan_keuangan', function (Blueprint $table) {
            $table->string('pgj_trans_id', length:15)->primary(true);
            $table->integer('pgj_id_umhaj')->nullable(true);
            $table->date('pgj_date');
            $table->string('pgj_description', length:255)->nullable(true);
            $table->string('pgj_payment_methode', length:20)->nullable(true);
            $table->string('pgj_payment_account', length:255)->nullable(true);
            $table->decimal('pgj_payment_amount', total:17, places:2)->default(0.00);
            $table->string('pgj_currency', length: 25)->nullable(true);
            $table->text('pgj_file_url')->nullable(true);
            $table->string('created_by', length:3);
            $table->dateTime('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('updated_by', length:3)->nullable(true);
            $table->dateTime('updated_date')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_trans_pengajuan_keuangan');
    }
};
