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
        Schema::create('tr_prog_jdw', function (Blueprint $table) {
            $table->string('prog_jdw_id', 36);
            $table->string('prog_rul_id', 36);
            $table->string('prog_pkb_id', 36);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_prog_jdw');
    }
};
