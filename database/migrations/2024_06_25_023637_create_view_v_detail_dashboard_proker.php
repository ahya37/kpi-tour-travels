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
        DB::select(
            "
            CREATE OR REPLACE VIEW v_detail_dashboard_proker AS
            SELECT 	b.proker_id,
                            b.proker_uuid,
                            b.proker_title,
                            b.proker_start_date,
                            b.proker_end_date,
                            b.proker_doc_reference,
                            b.role_id,
                            b.role_name,
                            SUBSTRING_INDEX(b.trans_type, '. ', -1) as trans_type,
                            b.proker_periode,
                            b.created_at
            FROM 		(
            SELECT 	a.id as proker_id,
                            a.uid as proker_uuid,
                            a.pkt_title as proker_title,
                            null as proker_start_date,
                            null as proker_end_date,
                            null as proker_doc_reference,
                            c.id as role_id,
                            c.name as role_name,
                            a.pkt_year as proker_periode,
                            '1. Tahunan' as trans_type,
                            a.created_at
            FROM 		proker_tahunan a
            JOIN 		model_has_roles b ON a.created_by = b.model_id
            JOIN 		roles c ON b.role_id = c.id

            UNION ALL

            SELECT 	a.id as proker_id,
                            a.uuid as proker_uuid,
                            a.pkb_title as proker_title,
                            a.pkb_start_date as proker_start_date,
                            a.pkb_end_date as proker_end_date,
                            a.pkb_pkt_id as proker_doc_reference,
                            c.id as role_id,
                            c.name as role_name,
                            EXTRACT(YEAR FROM a.pkb_start_date) as proker_periode,
                            '2. Bulanan' as trans_type,
                            a.created_at
            FROM 		proker_bulanan a
            JOIN 		model_has_roles b ON a.created_by = b.model_id
            JOIN 		roles c ON b.role_id = c.id

            UNION ALL

            SELECT 	a.id,
                            a.uuid,
                            a.pkh_title,
                            a.pkh_date as start_date,
                            null as end_date,
                            a.pkh_pkb_id as doc_reference,
                            c.id as role_id,
                            c.name as role_name,
                            EXTRACT(YEAR FROM a.pkh_date) as doc_periode,
                            '3. Harian' as trans_type,
                            a.created_at
            FROM 		proker_harian a
            JOIN 		model_has_roles b ON b.model_id = a.created_by
            JOIN 		roles c ON c.id = b.role_id
            ) AS B
            ORDER BY LEFT(b.trans_type, 1), b.created_at ASC
            "
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
