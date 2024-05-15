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
        Schema::create('alumni_prospect_material', function (Blueprint $table) {
            $table->string('id', 30)->unique();
            $table->string('customer_service_id', 30);
            $table->integer('members');
            $table->string('label',100);
            $table->integer('periode');
            $table->string('notes');
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
        Schema::dropIfExists('alumni_prospect_material');
    }
};
