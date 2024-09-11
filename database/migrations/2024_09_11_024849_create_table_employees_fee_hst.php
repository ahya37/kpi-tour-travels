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
        Schema::create('employees_fee_history', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id', 30);
            $table->decimal('employee_fee', 17, 2);
            $table->string('created_by', 3);
            $table->date('created_at');
            $table->date('expired_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees_fee_history');
    }
};
