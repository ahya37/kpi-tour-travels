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
        Schema::create('proker_bulanan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->date('date');
            $table->string('created_by',30);
            $table->string('updated_by',30);
            $table->unsignedBigInteger('proker_tahunan_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proker_bulanan');
    }
};
