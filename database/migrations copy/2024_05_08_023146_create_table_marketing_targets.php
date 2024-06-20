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
        Schema::create('marketing_targets', function (Blueprint $table) {
            $table->string('id', 30)->unique();
            $table->year('year');
            $table->integer('total_target')->comment('total target dari detail target marketing');
            $table->integer('total_realization')->comment('total realisasi dari detail target marketing');
            $table->integer('total_difference')->comment('total selisih dari detail target marketing');
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
        Schema::dropIfExists('marketing_targets');
    }
};
