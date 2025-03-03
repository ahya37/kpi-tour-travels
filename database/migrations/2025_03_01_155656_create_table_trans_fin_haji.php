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
        Schema::create('fin_trans_haji', function (Blueprint $table) {
            $table->id('hj_trans_id');
            $table->date('hj_create_date');
            $table->smallInteger('hj_trans_member_id');
            $table->string('hj_trans_member_name', length:100);
            $table->string('hj_tour_code', length:100);
            $table->string('hj_no_daftar', length:50);
            $table->date('hj_tgl_daftar')->nullable();
            $table->string('hj_no_bpih', length:15);
            $table->string('hj_paket', length:10);
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
        Schema::dropIfExists('fin_trans_haji');
    }
};
