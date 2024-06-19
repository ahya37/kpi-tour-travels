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
        Schema::create('programs_jadwal_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rul_title', 255)->nullable();
            $table->string('rul_pkt_id', 100);
            $table->string('rul_pic_sdid', 30);
            $table->integer('rul_duration_day');
            $table->string('rul_sla', 5);
            $table->string('created_by', 30);
            $table->string('updated_by', 30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs_jadwal_rules');
    }
};
