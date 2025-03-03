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
        Schema::create('fin_trans_haji_detail', function (Blueprint $table) {
            $table->string('hj_trans_id', length:10);
            $table->integer('hj_seq');
            $table->date('hj_payment_date');
            $table->enum('hj_payment_method', ['tf', 'cash']);
            $table->integer('hj_bank_account_id')->nullable();
            $table->decimal('hj_payment_amount', 17, 2);
            $table->string('hj_payment_currency', length:3);
            $table->text('hj_payment_note');
            $table->string('created_by', length:10);
            $table->dateTime('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('updated_by', length:10);
            $table->dateTime('updated_date')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_trans_haji_detail');
    }
};
