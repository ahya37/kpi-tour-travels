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
        Schema::create('target_branch', function (Blueprint $table) {
            $table->id();
            $table->integer('target');
            $table->integer('branch_id');
            $table->enum('tipe', ['HAJI', 'UMRAH', 'TOUR MUSLIM']);
            $table->foreign('id')->references('id')->on('branchs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_branch');
    }
};
