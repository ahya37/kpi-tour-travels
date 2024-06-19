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
        Schema::create('proker_tahunan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->unsignedBigInteger('parent_id');
            $table->string('pkt_title')->nullable();
            $table->string('pkt_description')->nullable();
            $table->date('pkt_year');
            $table->string('pkt_pic_job_employee_id', 30);
            $table->string('division_group_id', 30);
            $table->string('created_by',30);
            $table->string('updated_by',30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proker_tahunan');
    }
};
