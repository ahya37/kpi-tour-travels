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
        Schema::create('detailed_marketing_targets', function (Blueprint $table) {
            $table->string('id', 30)->unique();
            $table->string('marketing_target_id', 30);
            $table->string('program_id', 30);
            $table->integer('month_number')->comment('angka urutan bulan');
            $table->string('month_name',30)->comment('nama bulan');
            $table->integer('target')->comment('total target dari detail target marketing');
            $table->integer('realization')->comment('total realisasi dari detail target marketing');
            $table->integer('difference')->comment('total selisih dari detail target marketing');
            $table->string('created_by',30);
            $table->string('updated_by',30);
            $table->foreign('program_id')->references('id')->on('programs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('marketing_target_id')->references('id')->on('marketing_targets')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_marketing_targets');
    }

};
