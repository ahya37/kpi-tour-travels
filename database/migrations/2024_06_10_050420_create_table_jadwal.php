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
        Schema::create('programs_jadwal', function (Blueprint $table) {
            $table->id();
            $table->uuid('jdw_uuid')->default(DB::raw('(UUID())'));
            $table->string('jdw_programs_id', 30);
            $table->date('jdw_depature_date');
            $table->date('jdw_arrival_date');
            $table->string('jdw_mentor_name', 100);
            $table->char('is_generated', 1)->default('f');
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
        Schema::dropIfExists('programs_jadwal');
    }
};
