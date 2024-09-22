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
        Schema::create('employees_activity', function (Blueprint $table) {
            $table->id();
            $table->uuid('emp_act_uuid');
            $table->string('emp_act_user_id', 30);
            $table->string('emp_act_title', 100);
            $table->date('emp_act_start_date');
            $table->date('emp_act_end_date');
            $table->string('emp_act_type', 10);
            $table->enum('emp_act_status', ['1', '2', '3']);
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
        Schema::dropIfExists('employees_activity');
    }
};
