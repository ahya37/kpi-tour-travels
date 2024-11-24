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
        Schema::create('agent_activity_jemaah', function (Blueprint $table) {
            $table->string('agt_act_tour_code', length:30);
            $table->date('agt_act_date');
            $table->integer('agt_act_prs_seq');
            $table->string('agt_act_prs_id', length:7);
            $table->string('agt_act_prs_name');
            $table->string('created_by', length:3);
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_activity_jemaah');
    }
};