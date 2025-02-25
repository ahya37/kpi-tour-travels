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
        Schema::create('fin_mas_currency', function (Blueprint $table) {
            $table->id();
            $table->string('curr_from', length:3);
            $table->string('curr_to', length:3);
            $table->decimal('curr_from_value', 17, 2);
            $table->decimal('curr_to_value_low', 17, 2);
            $table->decimal('curr_to_value_high', 17, 2);
            $table->date('curr_start_date');
            $table->date('curr_end_date');
            $table->text('curr_note');
            $table->string('created_by');
            $table->dateTime('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('updated_by');
            $table->dateTime('updated_date')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_mas_currency');
    }
};
