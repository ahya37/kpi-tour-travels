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
        Schema::create('proker_harian_file', function (Blueprint $table) {
            $table->string('pkh_id', 36);
            $table->string('pkhf_seq', 3);
            $table->string('pkhf_name', 255);
            $table->string('pkhf_path', 255);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proker_harian_file');
    }
};
