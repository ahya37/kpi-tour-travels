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
            $table->string('hj_trans_member_id', length:100);
            $table->string('hj_tour_code');
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
