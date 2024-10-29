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
        Schema::create('agent_activity', function (Blueprint $table) {
            $table->string('agt_id', length:8);
            $table->date('agt_act_date');
            $table->string('agt_act_tour_code', length:30);
            $table->integer('agt_act_qty')->default(0);
            $table->enum('agt_act_status', ['act', 'ref', 'psv'])->default('act');
            $table->char('agt_act_is_paid', length:1)->default(0);
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_activity');
    }
};
