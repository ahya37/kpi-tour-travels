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
        Schema::create('employees_fee', function (Blueprint $table) {
           $table->string('employee_id', 30);
           $table->string('employee_name', 100);
           $table->decimal('employee_fee', 17, 2);
           $table->string('created_by', 3);
           $table->string('updated_by', 3);
           $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees_fee');
    }
};
