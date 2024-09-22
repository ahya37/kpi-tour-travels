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
        Schema::create('tm_presence', function (Blueprint $table) {
            $table->id();
            $table->date('prs_date');
            $table->string('prs_user_id', length:4);
            $table->dateTime('prs_in_time', precision:0)->nullable();
            $table->string('prs_in_file', length:100)->nullable();
            $table->dateTime('prs_out_time', precision:0)->nullable();
            $table->string('prs_out_file', length:100)->nullable();
            $table->string('created_by', length:30);
            $table->string('updated_by', length:30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tm_presence');
    }
};
