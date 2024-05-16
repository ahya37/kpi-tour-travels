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
        Schema::create('job_employees', function (Blueprint $table) {
            $table->string('id', 30)->unique();
            $table->string('employee_id', 30);
            $table->string('sub_division_id', 30);
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
        Schema::dropIfExists('job_employees');
    }
};
