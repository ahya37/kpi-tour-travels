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
        Schema::create('ads_detail', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->integer('ads_province_id', 4);
            $table->integer('ads_reponse_total');
            $table->string('ads_response_from', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads_detail');
    }
};
