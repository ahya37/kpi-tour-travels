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
        Schema::create('ads_header', function (Blueprint $table) {
            $table->id();
            $table->uuid('ads_idx', 30)->unique();
            $table->string('ads_name', 255);
            $table->date('ads_start_date');
            $table->integer('ads_duration');
            $table->date('ads_end_date');
            $table->decimal('ads_total_cost', 17, 2)->default(0);
            $table->char('ads_status', 1)->default(0);
            $table->longText('ads_description')->nullable();
            $table->integer('ads_gender_total_response')->default(0);
            $table->integer('ads_gender_respones_from_m')->default(0);
            $table->integer('ads_gender_response_from_f')->default(0);
            $table->integer('ads_gender_percentage_from_m')->default(0);
            $table->integer('ads_gender_percentage_from_f')->default(0);
            $table->integer('ads_grand_total_response')->d;
            $table->integer('ads_reach')->default(0);
            $table->integer('ads_show')->default(0);
            $table->string('created_by', 100);
            $table->string('updated_by', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads_header');
    }
};
