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
        Schema::create('proker_bulanan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('pkb_title', 100);
            $table->date('pkb_start_date');
            $table->date('pkb_end_date')->nullable();
            $table->longText('pkb_description')->nullable();
            $table->string('pkb_pkt_id', 36);
            $table->string('pkb_employee_id', 30);
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
        Schema::dropIfExists('proker_bulanan');
    }
};
