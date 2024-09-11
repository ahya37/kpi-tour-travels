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
        Schema::create('branchs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->integer('company_id');
            $table->integer('city_id');
            $table->string('created_bv', 30);
            $table->string('updated_by', 30);
            $table->timestamps();
            $table->string('job_employee_id', 30);
            $table->enum('status', ['PUSAT', 'CABANG']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branchs');
    }
};
