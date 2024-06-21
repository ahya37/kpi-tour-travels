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
        Schema::create('pic_detailed_marketing_target', function (Blueprint $table) {
            $table->id();
            $table->string('detailed_marketing_target_id', 35);
            $table->integer('realization');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pic_detailed_marketing_target');
    }
};
