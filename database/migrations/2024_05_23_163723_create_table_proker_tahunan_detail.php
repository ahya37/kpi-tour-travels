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
        Schema::create('proker_tahunan_detail', function (Blueprint $table) {
            $table->bigInteger('pkt_id');
            $table->integer('pktd_seq', 3);
            $table->longText('pktd_title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proker_tahunan_detail');
    }
};
