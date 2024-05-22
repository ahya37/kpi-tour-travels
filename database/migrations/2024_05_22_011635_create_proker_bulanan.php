<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->uuid('uid')->unique()->default(DB::raw('(UUID())'));
            $table->string('description')->nullable();
            $table->string('type_of_work')->nullable();
            $table->string('target')->nullable();
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
