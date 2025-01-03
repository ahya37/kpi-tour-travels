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
        Schema::create('tm_member', function (Blueprint $table) {
            $table->integer('mb_id');
            $table->string('mb_full_name', 50);
            $table->string('mb_nik', 16);
            $table->string('mb_first_name', 40);
            $table->string('mb_middle_name', 40)->nullable();
            $table->string('mb_last_name', 40)->nullable();
            $table->string('mb_father_name', 50)->nullable();
            $table->enum('mb_gender', ['m', 'f']);
            $table->string('mb_birth_place', 50);
            $table->date('mb_birth_date');
            $table->string('mb_marital_status', 13)->nullable();
            $table->string('mb_mahram', 100)->default('0');
            $table->string('mb_mahram_status', 15)->nullable();
            $table->string('mb_ktp_num', 30)->default('000000000000000000000000000000');
            $table->date('mb_ktp_exp_date')->nullable();
            $table->string('mb_passport_num', 30)->nullable();
            $table->string('mb_passport_place')->nullable();
            $table->date('mb_passport_start_date')->nullable();
            $table->date('mb_passport_end_date')->nullable();
            $table->string('mb_npwp_num', 20)->nullable();
            $table->string('mb_npwp_name', 50)->nullable();
            $table->string('mb_npwp_relation', 50)->nullable();
            $table->string('mb_contact_1', 20)->nullable();
            $table->string('mb_contact_2', 20)->nullable();
            $table->string('mb_contact_email', 100)->nullable();
            $table->string('mb_job', 50)->nullable();
            $table->string('mb_company_name', 255)->nullable();
            $table->string('mb_last_education', 20)->nullable();
            $table->longText('mb_address');
            $table->longText('mb_address_letter');
            $table->string('mb_rt', 3);
            $table->string('mb_rw', 3);
            $table->string('mb_province_id', 2);
            $table->string('mb_city_id', 4)->nullable();
            $table->string('mb_district_id', 7)->nullable();
            $table->string('mb_village_id', 10)->nullable();
            $table->string('mb_postal_code', 10)->nullable();
            $table->text('mb_picture_dir')->nullable();
            $table->string('mb_det_mata', 30)->nullable();
            $table->string('mb_det_alis', 30)->nullable();
            $table->string('mb_det_berat', 4)->default('0');
            $table->string('mb_det_rambut', 30);
            $table->string('mb_det_uk_baju', 7)->nullable();
            $table->string('mb_source_information', 30);
            $table->string('mb_agent_id', 10)->nullable();
            $table->string('mb_cs_id', 3);
            $table->longText('mb_notes')->nullable();
            $table->string('created_by', 3);
            $table->string('updpated_by', 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tm_member');
    }
};
