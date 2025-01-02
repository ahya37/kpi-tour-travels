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
        Schema::create('tm_member', function (Blueprint $table) {
            $table->integer('mb_id');
            $table->string('mb_full_name', 50);
            $table->string('mb_nik', 16);
            $table->string('mb_first_name', 40);
            $table->string('mb_middle_name', 40)->nullable();
            $table->string('mb_last_name', 40)->nullable();
            $table->string('mb_father_name', 50)->nullable();
            $table->enum('mb_gender', ['m', 'f']);
            $table->string('mb_birth_place', 50);
            $table->date('mb_birth_date');
            $table->string('mb_marital_status', 13);
            $table->string('mb_mahram', 100);
            $table->string('mb_mahram_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tm_member');
    }
};
