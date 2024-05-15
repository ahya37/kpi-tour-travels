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
        Schema::create('detail_alumni_prospect_material', function (Blueprint $table) {
            $table->string('id', 30)->unique();
            $table->string('alumni_prospect_material_id', 30);
            $table->integer('id_members');
            $table->string('name',100);
            $table->string('telp',100)->default(null);
            $table->string('address')->default(null);
            $table->enum('is_respone',['Y','N'])->default(null);
            $table->string('reason')->default(null);
            $table->string('notes')->default(null);
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
        Schema::dropIfExists('detail_alumni_prospect_material');
    }
};
