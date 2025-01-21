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
        Schema::create('hr_master_jam', function (Blueprint $table) {
            $table->integer('id_master');
            $table->date('d_start');
            $table->date('d_end')->nullable();
            $table->string('dy_name', length:10);
            $table->time('cl_in');
            $table->time('cl_out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_master_jam');
    }
};
