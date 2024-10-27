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
        Schema::create('log_cronjob', function (Blueprint $table) {
            $table->id();
            $table->string('log_type', length:5);
            $table->text('log_desc');
            $table->dateTime('log_date_time');
            $table->string('job_title', length:100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_cronjob');
    }
};
