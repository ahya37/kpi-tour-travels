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
        Schema::create('proker_bulanan_detail', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pkb_id');
            $table->string('pkbd_type', 250)->nullable();
            $table->string('pkbd_target', 250)->nullable();
            $table->string('pkbd_result', 250)->nullable();
            $table->longText('pkbd_evaluation')->nullable();
            $table->longText('pkbd_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proker_bulanan_detail');
    }
};
