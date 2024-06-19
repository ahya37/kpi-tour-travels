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
        Schema::create('detail_work_plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('idx')->unique();
            $table->unsignedBigInteger('work_plan_id');
            $table->string('description')->default(null)->comment('uraian tugas');
            $table->string('type_of_work')->default(null)->comment('jenis pekerjaan');
            $table->string('target')->default(null)->comment('target atau sasaran');
            $table->string('result')->default(null)->comment('hasil');
            $table->longText('evaluation')->default(null);
            $table->string('notes')->default(null)->comment('keterangan');
            $table->string('created_by',30);
            $table->string('updated_by',30);
            $table->foreign('work_plan_id')->references('id')->on('work_plans')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_work_plans');
    }
};
