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
        Schema::create('agent', function (Blueprint $table) {
            $table->string('agt_id', length: 7)->primary();
            $table->string('agt_name', length: 50);
            $table->string('agt_pic', length: 30)->nullable();
            $table->longText('agt_address');
            $table->string('agt_contact_1', length: 30)->nullable();
            $table->string('agt_contact_2', length: 25)->nullable();
            $table->string('agt_fax', length: 15)->nullable();
            $table->string('agt_email', length: 100)->nullable();
            $table->string('agt_note', length: 100)->nullable();
            $table->enum('agt_is_active', ['t', 'f'])->default('t');
            $table->enum('agt_type', ['act', 'ref', 'psv'])->default('ref');
            $table->date('agt_create_date');
            $table->string('agt_old_id', length: 4)->nullable();
            $table->string('created_by', length: 3);
            $table->string('updated_by', length: 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent');
    }
};
