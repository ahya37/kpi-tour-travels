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
        Schema::table('marketing_targets', function (Blueprint $table) {
            $table->integer('total_target')->default(0)->change();
            $table->integer('total_realization')->default(0)->change();
            $table->integer('total_difference')->default(0)->change();
            $table->string('updated_by',30)->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketing_targets', function (Blueprint $table) {
            $table->integer('total_target')->change();
            $table->integer('total_realization')->change();
            $table->integer('total_difference')->change();
            $table->integer('updated_by')->change();
        });
    }
};
