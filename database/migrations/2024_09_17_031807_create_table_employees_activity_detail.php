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
        Schema::create('employees_activity_detail', function (Blueprint $table) {
            $table->string('emp_act_id', 36);
            $table->integer('empd_seq');
            $table->string('empd_descripttion', 255)->nullable();
            $table->date('empd_date');
            $table->dateTime('empd_start_time');
            $table->dateTime('empd_end_time');
            $table->char('empd_status', 1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees_activity_detail');
    }
};
