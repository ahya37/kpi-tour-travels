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
        Schema::create('programs_article', function (Blueprint $table) {
            $table->id();
            $table->string('jdw_uuid', length:36);
            $table->string('jdw_tour_code', length:30);
            $table->string('jdw_title_name', length:50);
            $table->string('jdw_title_slug', length:100);
            $table->enum('jdw_status_upload', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('created_by', length:3);
            $table->string('updated_by', length:3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs_article');
    }
};
