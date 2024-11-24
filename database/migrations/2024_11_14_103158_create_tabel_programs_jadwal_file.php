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
        Schema::create('programs_jadwal_file', function (Blueprint $table) {
            $table->string('jdw_det_tour_code', length:30);
            $table->integer('jdw_det_seq');
            $table->string('jdw_det_description', length:100)->nullable(true);
            $table->longText('jdw_det_link')->nullable(true);
            $table->string('created_by', length:3);
            $table->dateTime('created_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs_jadwal_file');
    }
};
