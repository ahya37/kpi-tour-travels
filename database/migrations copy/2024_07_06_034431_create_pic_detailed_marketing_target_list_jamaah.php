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
        Schema::create('pic_detailed_marketing_target_list_jamaah', function (Blueprint $table) {
            $table->id();
            $table->integer('pic_detailed_marketing_target_id');
            $table->integer('id_member');
            $table->string('name');
            $table->string('is_alumni',10);
            $table->string('sumber');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pic_detailed_marketing_target_list_jamaah');
    }
};
