<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use DateTime;
use Illuminate\Support\Facades\Log;
use Route;
use Str;

use function Psy\debug;

date_default_timezone_set('Asia/Jakarta');

class DivisiService 
{
    public static function getListJadwalUmrah($request) {
        // var_dump($request);die();
        $bulan_cari     = $request['cari'][0];
        $tahun_cari     = $request['cari'][1];
        $uuid           = $request['cari'][2];
        $programs_id    = $request['cari'][3];
        $query  = DB::select(
            "
            SELECT 	a.jdw_uuid as jdw_id,
                    a.jdw_depature_date,
                    a.jdw_arrival_date,
                    UPPER(a.jdw_mentor_name) AS jdw_mentor_name,
                    b.name as jdw_program_name,
                    a.is_generated as status_generated,
                    a.is_active as status_active,
                    a.jdw_tour_code
            FROM 	programs_jadwal a
            JOIN 	programs b on a.jdw_programs_id = b.id
            WHERE   a.jdw_uuid LIKE '$uuid'
            AND     EXTRACT(YEAR FROM a.jdw_depature_date) LIKE '$tahun_cari'
            AND     (EXTRACT(MONTH FROM a.jdw_depature_date) = '$bulan_cari' OR EXTRACT(MONTH FROM a.jdw_depature_date) LIKE '$bulan_cari') 
            AND     a.jdw_programs_id LIKE '$programs_id'
            AND     a.is_active = 't'
            ORDER BY a.jdw_depature_date DESC
            "
        );

        return $query;
    }
    public static function doSimpanJadwalUmrah($request)
    {
        DB::beginTransaction();
        $data   = $request->all()['sendData'];
        $ip     = $request->ip();

        // var_dump($data);die();

        if($data['transType'] == 'add') {
            $dataSimpan     = array(
                "jdw_programs_id"   => $data['programPaket'],
                "jdw_depature_date" => $data['depatureDate'],
                "jdw_arrival_date"  => $data['arrivalDate'],
                "jdw_mentor_name"   => $data['programPembimbing'],
                "created_by"        => Auth::user()->id,
                "updated_by"        => Auth::user()->id,
                "created_at"        => date('Y-m-d H:i:s'),
                "updated_at"        => date('Y-m-d H:i:s'),
            );

            DB::table('programs_jadwal')->insert($dataSimpan);
        } else if($data['transType'] == 'edit') {
            $data_cari  = array(
                "jdw_uuid"  => $data['programID'],
            );

            $data_update    = array(
                "jdw_programs_id"   => $data['programPaket'],
                "jdw_depature_date" => $data['depatureDate'],
                "jdw_arrival_date"  => $data['arrivalDate'],
                "jdw_mentor_name"   => $data['programPembimbing'],
                "updated_by"        => Auth::user()->id,
                "updated_at"        => date('Y-m-d H:i:s'),
            );

            DB::table('programs_jadwal')->where($data_cari)->update($data_update);
        }
        
        try {
            DB::commit();
            // RETURN VAL
            $output     = array(
                "status"    => "berhasil",
                "errMsg"    => "",
            );
            // LOG
            if($data['transType'] == 'add') {
                LogHelper::create("add", "Berhasil Menambahkan Jadwal Umrah Baru (" . $data['depatureDate'] . " s/d ". $data['arrivalDate'] .")", $ip);
            } else {
                LogHelper::create("edit", "Berhasil Mengubah Jadwal Umrah ID : ".$data['programID']."", $ip);
            }
        } catch(\Exception $e) {
            DB::rollBack();
            $output     = array(
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            );

            Log::channel('daily')->error($e->getMessage());
            LogHelper::create("error_system", $e->getMessage(), $ip);
        }

        return $output;
    }

    public static function doGetDataJadwalUmrah($data)
    {
        $uuid   = $data['programID'];
        $query  = DB::select(
            "
            SELECT 	a.jdw_uuid as jdw_id,
                    a.jdw_depature_date,
                    a.jdw_arrival_date,
                    UPPER(a.jdw_mentor_name) AS jdw_mentor_name,
                    a.jdw_programs_id,
                    b.name as jdw_program_name,
                    a.is_active as status_active,
                    a.is_generated as status_generated,
                    a.jdw_tour_code
            FROM 	programs_jadwal a
            JOIN 	programs b on a.jdw_programs_id = b.id
            WHERE   a.jdw_uuid LIKE '$uuid'
            ORDER BY a.jdw_depature_date DESC
            "
        );

        return $query;
    }

    public static function doSimpanDataRules($data, $jenis)
    {
        DB::beginTransaction();
        $getData    = $data->all()['sendData'];
        $ip         = $data->ip();

        if($jenis == 'add') {
            $data_simpan    = array(
                "rul_title"     => $getData['dataTitle'],
                "rul_pkt_id"    => $getData['dataPktID'],
                "rul_pic_sdid"  => $getData['dataPIC'],
                "rul_duration_day"  => $getData['dataDuration'],
                "rul_sla"       => $getData['dataSLA'],
                "rul_condition" => $getData['dataCondition'],
                "rul_value"     => $getData['dataBobot'],
                "created_by"    => Auth::user()->id,
                "updated_by"    => Auth::user()->id,
                "created_at"    => date('Y-m-d H:i:s'),
                "updated_at"    => date('Y-m-d H:i:s'),
            );
            DB::table('programs_jadwal_rules')->insert($data_simpan);
        } else if($jenis == 'edit') {
            $data_where     = array(
                "id"        => $getData['dataID'],
            );

            $data_update    = array(
                "rul_title"         => $getData['dataTitle'],
                "rul_pkt_id"        => $getData['dataPktID'],
                "rul_pic_sdid"      => $getData['dataPIC'],
                "rul_duration_day"  => $getData['dataDuration'],
                "rul_sla"           => $getData['dataSLA'],
                "rul_condition"     => $getData['dataCondition'],
                "rul_value"         => $getData['dataBobot'],
                "updated_by"        => Auth::user()->id,
                "updated_at"        => date('Y-m-d H:i:s'),
            );
            DB::table('programs_jadwal_rules')->where($data_where)->update($data_update);
        }

        try {
            DB::commit();
            // RETURN VAL
            $output     = array(
                "status"    => "berhasil",
                "errMsg"    => "",
            );
            // LOG
            if($jenis == 'add') {
                LogHelper::create("add", "Berhasil Membuat Rules Program Kerja Operasional", $ip);
            } else {
                LogHelper::create("edit", "Berhaisl Mengubah Rules Program Kerja Operasional", $ip);
            }
        } catch(\Exception $e) {
            DB::rollBack();
            $output     = array(
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            );

            Log::channel('daily')->error($e->getMessage());
            LogHelper::create("error_system", $e->getMessage(), $ip);
        }

        return $output;
    }

    public static function doGetListRules($data)
    {
        $query  = DB::select(
            "
            SELECT 	a.id,
                    a.rul_title,
                    a.rul_duration_day,
                    a.rul_sla,
                    b.name as rul_pic_name
            FROM 	programs_jadwal_rules a
            JOIN 	sub_divisions b ON a.rul_pic_sdid = b.id
            ORDER BY a.id ASC
            "
        );

        return $query;
    }

    public static function doGenerateRules($data)
    {
        $depature_date  = $data->all()['sendData']['depature_date'];
        $arrival_date   = $data->all()['sendData']['arrival_date'];
        $program_id     = $data->all()['sendData']['program_id'];
        $ip             = $data->ip();

        // DO TRANS
        DB::beginTransaction();
        $query_getDataProgram   = DB::select(
            "
            SELECT 	a.jdw_uuid as jdw_id,
                    CONCAT('Program ', c.name, ' (', b.name,') ', DATE_FORMAT(a.jdw_depature_date,'%d %M %Y'), ' s/d ', DATE_FORMAT(a.jdw_arrival_date,'%d %M %Y')) as jdw_description,
                    a.jdw_mentor_name,
                    UPPER(b.name) as program_name,
                    c.name as product_name
            FROM 	programs_jadwal a
            JOIN 	programs b on a.jdw_programs_id = b.id
            JOIN 	products c on b.product_id = c.id
            WHERE 	a.jdw_uuid = '$program_id'
            "
        );
        
        if(!empty($query_getDataProgram)) {
            $dataProgram    = [
                "jdw_id"            => $query_getDataProgram[0]->jdw_id,
                "jdw_description"   => $query_getDataProgram[0]->jdw_description,
                "jdw_depature_date" => $depature_date,
                "jdw_arrival_date"  => $arrival_date,
                "jdw_mentor_name"   => $query_getDataProgram[0]->jdw_mentor_name,
                "jdw_program_name"  => $query_getDataProgram[0]->program_name,
                "jdw_product_name"  => $query_getDataProgram[0]->product_name,
            ];
        } else {
            $dataProgram    = [];
        }

        if(!empty($dataProgram['jdw_product_name']))
        {
            if($dataProgram['jdw_product_name'] == 'Umrah') {
                $query_getDataRules     = DB::select(
                    "
                    SELECT 	a.id,
                            a.rul_title,
                            a.rul_duration_day,
                            a.rul_sla,
                            LEFT(a.rul_sla, 1) as custom_sla_condition,
                            SUBSTRING_INDEX(SUBSTRING_INDEX(a.rul_sla,'-',-1),'+',-1) AS custom_sla,
                            a.rul_pkt_id,
                            a.rul_condition
                    FROM 	programs_jadwal_rules a
                    WHERE 	a.rul_condition IS NOT NULL
                    ORDER BY a.id ASC
                    "
                );

                // print("<pre>".print_r($query_getDataRules, true)."</pre>");die();
            } else if($dataProgram['jdw_product_name'] == 'Haji') {
                $query_getDataRules     = DB::select(
                    "
                    SELECT 	a.id,
                            a.rul_title,
                            a.rul_duration_day,
                            a.rul_sla,
                            LEFT(a.rul_sla, 1) as custom_sla_condition,
                            SUBSTRING_INDEX(SUBSTRING_INDEX(a.rul_sla,'-',-1),'+',-1) AS custom_sla,
                            a.rul_pkt_id,
                            a.rul_condition
                    FROM 	programs_jadwal_rules a
                    WHERE 	a.rul_condition IS NULL
                    ORDER BY a.id ASC
                    "
                );
            }
        }

        if(!empty($query_getDataRules)) {
            for($i = 0; $i < count($query_getDataRules); $i++) {
                $dataRules  = $query_getDataRules[$i];
                if($dataProgram['jdw_product_name'] == 'Umrah') {
                    if($dataRules->custom_sla_condition == '-') {
                        $rulesCondition     = $dataRules->rul_condition == 'bf-dpt' ? $dataProgram['jdw_depature_date'] : $dataProgram['jdw_arrival_date'];
                        $dataSimpan         = [
                            "uuid"              => Str::random(30),
                            "pkb_title"         => "[".$dataProgram['jdw_program_name']."] (".date('d/M/Y', strtotime($dataProgram['jdw_depature_date']))." s/d ".date('d/M/Y', strtotime($dataProgram['jdw_arrival_date'])).") ".$dataRules->rul_title,
                            "pkb_start_date"    => count($dataProgram) > 0 ? date('Y-m-d', strtotime(' -'.$dataRules->custom_sla.' days', strtotime($rulesCondition))) : '1970-01-01',
                            "pkb_end_date"      => count($dataProgram) > 0 ? $rulesCondition : '1970-01-01',
                            "pkb_description"   => count($dataProgram) > 0 ? $dataRules->rul_title : '',
                            "pkb_pkt_id"        => $dataRules->rul_pkt_id,
                            "pkb_employee_id"   => '',
                            "created_by"        => Auth::user()->id,
                            "updated_by"        => Auth::user()->id,
                            "created_at"        => date('Y-m-d H:i:s'),
                            "updated_at"        => date('Y-m-d H:i:s'),
                        ];
                    } else {
                        $rulesCondition     = $dataRules->rul_condition == 'af-dpt' ? $dataProgram['jdw_depature_date'] : $dataProgram['jdw_arrival_date'];
                        $dataSimpan         = [
                            "uuid"              => Str::random(30),
                            "pkb_title"         => "[".$dataProgram['jdw_program_name']."] (".date('d/M/Y', strtotime($dataProgram['jdw_depature_date']))." s/d ".date('d/M/Y', strtotime($dataProgram['jdw_arrival_date'])).") ".$dataRules->rul_title,
                            "pkb_start_date"    => count($dataProgram) > 0 ? $rulesCondition : '1970-01-01',
                            "pkb_end_date"      => count($dataProgram) > 0 ? date('Y-m-d', strtotime(' +'.$dataRules->custom_sla.' days', strtotime($rulesCondition))) : '1970-01-01',
                            "pkb_description"   => count($dataProgram) > 0 ? $dataRules->rul_title : '',
                            "pkb_pkt_id"        => $dataRules->rul_pkt_id,
                            "pkb_employee_id"   => '',
                            "created_by"        => Auth::user()->id,
                            "updated_by"        => Auth::user()->id,
                            "created_at"        => date('Y-m-d H:i:s'),
                            "updated_at"        => date('Y-m-d H:i:s'),
                        ];
                    }
                    $idProkerBulanan   = DB::table('proker_bulanan')->insertGetId($dataSimpan);
                    $uuidProkerBulanan = DB::table('proker_bulanan')->where('id', $idProkerBulanan)->value('uuid');

                    // INSERT TO DETAIL
                    $simpan_detail  = array(
                        "pkb_id"        => $idProkerBulanan,
                        "pkbd_type"     => $dataRules->rul_title,
                        "pkbd_pic"      => "0",
                    );
                    DB::table('proker_bulanan_detail')->insert($simpan_detail);
                } else {
                    $uuidProkerBulanan  = null;
                }

                $insert_trans       = array(
                    "prog_jdw_id"   => $query_getDataProgram[0]->jdw_id,
                    "prog_rul_id"   => $dataRules->id,
                    "prog_pkb_id"   => $uuidProkerBulanan,
                    "prog_pkb_is_created"   => "f"
                );

                DB::table('tr_prog_jdw')->insert($insert_trans);
                
                try {

                } catch(\Exception $e) {
                    DB::rollback();
                    break;
                    $output     = array(
                        "status"    => "gagal",
                        "errMsg"    => $e->getMessage(),
                    );
                }
            }

            // UPDATE JADWAL PROGRAMS
            $data_where     = [
                "jdw_uuid"  => $dataProgram['jdw_id']
            ];
            $data_update    = [
                "is_generated"  => "t",
            ];
            DB::table('programs_jadwal')->where($data_where)->update($data_update);

            try {
                DB::commit();
                // RETURN VAL
                $output     = array(
                    "status"    => "berhasil",
                    "errMsg"    => "",
                );
                // LOG
                LogHelper::create("add", "Berhasil Generate Program Kerja Bulanan Berdasarkan Rules Divisi Operasional", $ip);
            } catch(\Exception $e) {
                DB::rollBack();
                $output     = array(
                    "status"    => "gagal",
                    "errMsg"    => $e->getMessage(),
                );
    
                Log::channel('daily')->error($e->getMessage());
                LogHelper::create("error_system", $e->getMessage(), $ip);
            }
        } else {
            DB::rollBack();
            $output     = array(
                "status"    => "gagal",
                "errMsg"    => "Tidak ada Rules untuk Program Kerja"
            );
        }

        return $output;
    }

    public static function doGetRulesDetail($rulesID)
    {
        $query  = DB::select(
            "
            SELECT 	a.id as rul_id,
                    a.rul_title,
                    a.rul_pkt_id as rul_pkt,
                    a.rul_pic_sdid as rul_pic,
                    a.rul_duration_day,
                    LEFT(a.rul_sla, 1) as rul_length_day_condition,
                    SUBSTRING_INDEX(SUBSTRING_INDEX(a.rul_sla,'+',-1), '-', -1) as rul_length_day,
                    a.rul_condition,
                    a.rul_value as rul_bobot
            FROM    programs_jadwal_rules a
            WHERE 	a.id = '$rulesID'
            "
        );

        return $query;
    }

    public static function doGetDataDashboard($year)
    {
        $query  = DB::select(
            "
            SELECT 	SUM(total_jadwal_umrah) as grand_total_jadwal_umrah,
                    SUM(total_rule) as grand_total_rule
            FROM    (
                    SELECT 	count(*) as total_jadwal_umrah,
                            0 as total_rule
                    FROM 	programs_jadwal
                    WHERE 	EXTRACT(YEAR FROM jdw_depature_date) = EXTRACT(YEAR FROM CURRENT_DATE)
                    AND     is_active = 't'

                    UNION

                    SELECT 	0 as total_jadwal_umrah,
                            count(*) as total_rul
                    FROM 	programs_jadwal_rules
            ) as x
            "
        );

        return $query;
    }

    public static function doGetDataRulesJadwal($id, $subDivision)
    {
        $subDivision == 'pic' ? $subDivision = '%' : $subDivision;

        $query_get_rule_all     = DB::select(
            "
            SELECT 	DISTINCT d.id as rul_id,
                    d.rul_title,
                    b.jdw_depature_date as depature_date,
                    b.jdw_arrival_date as arrival_date,
                    d.rul_duration_day as number_of_processing_day,
                    SUBSTRING_INDEX(SUBSTRING_INDEX(d.rul_sla, '-', -1), '+', -1) as duration_day,
                    LEFT(d.rul_sla, 1) as duration_cond,
                    d.rul_condition,
                    CASE
                        WHEN LEFT(d.rul_sla, 1) = '-' AND d.rul_condition = 'bf-dpt' THEN DATE_ADD(b.jdw_depature_date, INTERVAL d.rul_sla DAY)
                        WHEN LEFT(d.rul_sla, 1) = '-' AND d.rul_condition = 'bf-arv' THEN DATE_ADD(b.jdw_arrival_date, INTERVAL d.rul_sla DAY)
                        WHEN LEFT(d.rul_sla, 1) = '+' AND d.rul_condition = 'af-arv' THEN b.jdw_arrival_date
                        WHEN LEFT(d.rul_sla, 1) = '+' AND d.rul_condition = 'af-dpt' THEN b.jdw_depature_date
                        ELSE b.jdw_depature_date
                    END as start_date_job,
                    CASE
                        WHEN LEFT(d.rul_sla, 1) = '-' AND d.rul_condition = 'bf-dpt' THEN b.jdw_depature_date
                        WHEN LEFT(d.rul_sla, 1) = '-' AND d.rul_condition = 'bf-arv' THEN b.jdw_arrival_date
                        WHEN LEFT(d.rul_sla, 1) = '+' AND d.rul_condition = 'af-arv' THEN DATE_ADD(b.jdw_arrival_date, INTERVAL d.rul_sla DAY)
                        WHEN LEFT(d.rul_sla, 1) = '+' AND d.rul_condition = 'af-dpt' THEN DATE_ADD(b.jdw_depature_date, INTERVAL d.rul_sla DAY)
                        ELSE b.jdw_arrival_date
                    END as end_date_job,
				    e.name as pic_role_name, 
                    d.rul_value as rul_bobot
            FROM 	tr_prog_jdw a
            JOIN 	programs_jadwal b ON a.prog_jdw_id = b.jdw_uuid
            JOIN 	programs_jadwal_rules d ON a.prog_rul_id = d.id
            JOIN    sub_divisions e ON d.rul_pic_sdid = e.id
            WHERE 	a.prog_jdw_id = '$id'
            ORDER BY d.id ASC
            "
        );

        $query_get_jdw_in_pkb  = DB::select(
            "
            SELECT 	b.prog_rul_id,
                    a.pkb_start_date,
                    a.pkb_end_date,
                    b.prog_pkb_is_created as status_created
            FROM 	proker_bulanan a
            JOIN 	tr_prog_jdw b ON a.uuid = b.prog_pkb_id
            WHERE 	b.prog_jdw_id = '$id'
            "
        );

        $queryGetJadwal     = DB::select(
            "
            SELECT 	*,
                    CASE
                        WHEN rp.realization_start_date IS NOT NULL AND rp.realization_start_date <> rp.realization_end_date THEN DATEDIFF(rp.realization_end_date, rp.realization_start_date)
                        WHEN rp.realization_start_date IS NOT NULL AND rp.realization_start_date = rp.realization_end_date THEN 1
                        ELSE 0
                    END as realization_duration_day
            FROM 	(
                    SELECT 	a.prog_jdw_id as jdw_id,
                            f.name as sub_program_name,
                            c.jdw_mentor_name as mentor_name,
                            d.id as rules_id,
                            d.rul_title as rules,
                            CONCAT('H', d.rul_sla) as duration,
                            CONCAT(d.rul_duration_day,' Hari') as duration_day,
                            d.rul_duration_day as duration_day_num,
                            c.jdw_depature_date as depature_date,
                            c.jdw_arrival_date as arrival_date,
                            CASE
                                    WHEN LEFT(d.rul_sla, 1) = '-' THEN DATE_ADD(c.jdw_depature_date, INTERVAL d.rul_sla DAY)
                                    WHEN LEFT(d.rul_sla, 1) = '+' THEN c.jdw_arrival_date
                                    ELSE c.jdw_depature_date
                            END as start_date_job,
                            CASE
                                    WHEN LEFT(d.rul_sla, 1) = '-' THEN c.jdw_depature_date
                                    WHEN LEFT(d.rul_sla, 1) = '+' THEN DATE_ADD(c.jdw_arrival_date, INTERVAL d.rul_sla DAY)
                            END as end_date_job,
                            CASE
                                    WHEN (b.pkb_start_time IS NOT NULL OR b.pkb_end_time IS NOT NULL) AND LEFT(d.rul_sla, 1) = '-' THEN b.pkb_start_date
                                    WHEN (b.pkb_start_time IS NOT NULL OR b.pkb_end_time IS NOT NULL) AND LEFT(d.rul_sla, 1) = '+' THEN b.pkb_start_date
                                    ELSE null
                            END as realization_start_date,
                            CASE
                                    WHEN (b.pkb_start_time IS NOT NULL OR b.pkb_end_time IS NOT NULL) AND LEFT(d.rul_sla, 1) = '-' THEN b.pkb_end_date
                                    WHEN (b.pkb_start_time IS NOT NULL OR b.pkb_end_time IS NOT NULL) AND LEFT(d.rul_sla, 1) = '+' THEN b.pkb_end_date
                                    ELSE null
                            END as realization_end_date,
                            e.name as pic_role
                    FROM 	tr_prog_jdw a
                    JOIN 	proker_bulanan b ON a.prog_pkb_id = b.uuid
                    JOIN 	programs_jadwal c ON a.prog_jdw_id = c.jdw_uuid
                    JOIN 	programs_jadwal_rules d ON a.prog_rul_id = d.id
                    JOIN 	sub_divisions e ON d.rul_pic_sdid = e.id
                    JOIN 	programs f ON c.jdw_programs_id = f.id
                    ) AS rp 
            WHERE 	rp.jdw_id LIKE '$id'
            AND     lower(rp.pic_role) LIKE '$subDivision'
            ORDER BY rp.pic_role, rp.depature_date ASC
            "
        );
        
        $output     = array(
            "list_rules"    => $query_get_rule_all,
            "proker_bulanan"=> $query_get_jdw_in_pkb,
            "jadwal"        => $queryGetJadwal,
        );
       return $output;
    }

    // MASTER
    public static function doGetDataProkerTahunan($roleName, $data)
    {
        $valueCari  = $data->all()['sendData'];
        $getData    = DB::select(
            "
            SELECT 	CONCAT(a.uid,' | ', d.pktd_seq) as pkt_id,
                    a.pkt_year as pkt_periode, 
                    d.pktd_seq,
                    d.pktd_title
            FROM 	proker_tahunan a
            JOIN 	group_divisions b ON a.division_group_id = b.id
            JOIN 	roles c ON b.roles_id = c.id
            JOIN 	proker_tahunan_detail d ON a.id = d.pkt_id
            WHERE 	lower(b.name) LIKE '$roleName'
            AND     a.uid LIKE '$valueCari'
            AND 	a.pkt_year = EXTRACT(YEAR FROM CURRENT_DATE)
            ORDER BY CAST(d.pktd_seq AS SIGNED) ASC
            "
        );
        
        return $getData;
    }

    public static function doGetDataSubDivision($roleName, $data)
    {
        $valueCari  = $data->all()['sendData'];
        
        $getData    = DB::select(
            "
            SELECT 	b.id as sub_division_id,
                    b.name as sub_division_name
            FROM 	group_divisions a
            JOIN 	sub_divisions b ON a.id = b.division_group_id
            JOIN 	roles c ON a.roles_id = c.id
            WHERE 	c.name LIKE '$roleName'
            AND     b.id LIKE '$valueCari'
            ORDER BY b.name ASC
            "
        );

        return $getData;
    }

    // 26 JUNI 2024
    // NOTE : PENGAMBILAN DATA UNTUK CONTOLLER DIVISICONTROLER > getDataRulesJadwalDetail
    public static function doGetDataRulesJadwalDetail($id)
    {
        $query_get_jadwal   = DB::select(
            "
            SELECT 	a.jdw_uuid,
                    a.jdw_programs_id,
                    b.name as jdw_programs_name,
                    a.jdw_mentor_name,
                    a.jdw_depature_date,
                    a.jdw_arrival_date,
                    a.is_generated as jdw_status_generate_rules
            FROM 	programs_jadwal a
            JOIN 	programs b ON a.jdw_programs_id = b.id
            WHERE 	a.jdw_uuid = '$id'
            "
        );

        $query_get_rules    = DB::select(
            "
            SELECT 	a.id as jdw_rules_id,
                    a.rul_title as jdw_rules_title,
                    SUBSTRING_INDEX(a.rul_pkt_id, ' | ', 1) as jdw_rules_pkt_id,
                    SUBSTRING_INDEX(a.rul_pkt_id, ' | ', -1) as jdw_rules_pkt_seq,
                    a.rul_pic_sdid as jdw_rules_sub_division_id,
                    b.name as jdw_rules_sub_division_name,
                    a.rul_duration_day as jdw_rules_duration_day,
                    SUBSTRING_INDEX(SUBSTRING_INDEX(a.rul_sla,'-', -1), '+', -1) as jdw_rules_deadline_day,
                    LEFT(a.rul_sla, 1) as jdw_rules_deadline_cond_1,
                    a.rul_condition as jdw_rules_deadline_cond_2
            FROM 	programs_jadwal_rules a
            JOIN 	sub_divisions b ON a.rul_pic_sdid = b.id
            ORDER BY CAST(a.id as SIGNED) ASC
            "
        );

        $output     = array(
            "jadwal"        => !empty($query_get_jadwal) ? $query_get_jadwal : null,
            "jadwal_rules"  => !empty($query_get_rules) ? $query_get_rules : null
        );

        return $output;
    }

    public static function doGetDataJobUser()
    {
        $query_for_chart  = DB::select(
            "
            SELECT 	b.name as employee_name,
                    count(a.id) as total_job
            FROM 	proker_bulanan a
            JOIN 	employees b ON a.created_by = b.user_id
            JOIN 	job_employees c ON c.employee_id = b.id
            JOIN 	group_divisions d ON c.group_division_id = d.id
            WHERE 	d.name LIKE 'Operasional'
            AND 	EXTRACT(YEAR FROM a.pkb_start_date) = EXTRACT(YEAR FROM CURRENT_DATE)
            AND     a.pkb_is_active = 't'
            GROUP BY b.name
            ORDER BY count(a.id) DESC
            "
        );

        $query_for_table    = DB::select(
            "
            SELECT 	b.name as full_name,
                    c.name as group_division_name,
                    d.name as sub_division_name
            FROM 	job_employees a
            JOIN 	employees b ON a.employee_id = b.id
            JOIN 	group_divisions c ON a.group_division_id = c.id
            JOIN 	sub_divisions d ON a.sub_division_id = d.id
            WHERE 	c.name = 'Operasional'
            ORDER BY b.name ASC
            "
        );


        $output     = array(
            "chart" => !empty($query_for_chart) ? $query_for_chart : "",
            "table" => !empty($query_for_table) ? $query_for_table : "",
        );

        return $output;
    }

    // 05 JULI 2024
    // NOTE : PEMBUATAN MODUL HAPUS PROGRAMS
    public static function doHapusProgram($id, $ip)
    {
        DB::beginTransaction();

        // CHECK APAKAH ID INI SUDAH MASUK KE PROGRAM KERJA BULANAN
        $queryCheckProkerBulanan    = DB::select(
            "
            SELECT 	*
            FROM 	tr_prog_jdw a
            WHERE 	prog_jdw_id = '$id'
            AND 	REPLACE(a.prog_pkb_id, ' ', '') <> '' 
            ORDER BY CAST(a.prog_rul_id AS SIGNED) ASC
            "
        );
        
        if(!empty($queryCheckProkerBulanan)) {
            for($i = 0; $i < count($queryCheckProkerBulanan); $i++) {
                $tarik  = $queryCheckProkerBulanan[$i];

                $pkb_ID     = $tarik->prog_pkb_id;
                // UPDATE PROKER BULANAN
                $whereUpdateBulanan     = [
                    "uuid"  => $pkb_ID
                ];
                $dataUpdateBulanan      = [
                    "pkb_is_active"     => "f"
                ];

                DB::table('proker_bulanan')->where($whereUpdateBulanan)->update($dataUpdateBulanan);
            }
        } else {
            // DO NOTHING
        }
        
        // UPDATE JADWAL
        $query_where    = [
            "jdw_uuid"      => $id,
        ];


        $query_update   = [
            "is_active"     => "f",
        ];

        DB::table('programs_jadwal')->where($query_where)->update($query_update);

        try {
            DB::commit();
            $output     = array(
                "status"    => "berhasil",
                "errMsg"    => [],
            );
            LogHelper::create('delete', 'Berhasil Membatalkan Program Umrah : '.$id, $ip);
        } catch(\Exception $e) {
            DB::rollBack();
            $output     = array(
                "status"    => "error",
                "errMsg"    => $e->getMessage(),
            );
            LogHelper::create('error_system', 'Gagal Membatalkan Program Umrah', $ip);
        }

        return $output;
    }

    public static function doHapusProgramByTourcode($tour_code, $ip, $is_activce, $log_user_id)
    {
        $jadwal = DB::table('programs_jadwal')->select('jdw_uuid')->where('jdw_tour_code', $tour_code)->first();
        if (!$jadwal) {
            $output     = array(
                "status"    => "0",
            );

            return $output;
        }
        $output = self::doHapusProgramFromUmhaj($jadwal->jdw_uuid, $ip, $is_activce, $log_user_id);
        return $output;
    }

    public static function doHapusProgramFromUmhaj($id, $ip,$is_activce, $log_user_id)
    {
        DB::beginTransaction();

        // CHECK APAKAH ID INI SUDAH MASUK KE PROGRAM KERJA BULANAN
        $queryCheckProkerBulanan    = DB::select(
            "
            SELECT 	*
            FROM 	tr_prog_jdw a
            WHERE 	prog_jdw_id = '$id'
            AND 	REPLACE(a.prog_pkb_id, ' ', '') <> '' 
            ORDER BY CAST(a.prog_rul_id AS SIGNED) ASC
            "
        );
        
        if(!empty($queryCheckProkerBulanan)) {
            for($i = 0; $i < count($queryCheckProkerBulanan); $i++) {
                $tarik  = $queryCheckProkerBulanan[$i];

                $pkb_ID     = $tarik->prog_pkb_id;
                // UPDATE PROKER BULANAN
                $whereUpdateBulanan     = [
                    "uuid"  => $pkb_ID
                ];
                $dataUpdateBulanan      = [
                    "pkb_is_active"     => $is_activce
                ];

                DB::table('proker_bulanan')->where($whereUpdateBulanan)->update($dataUpdateBulanan);
            }
        } else {
            // DO NOTHING
        }
        
        // UPDATE JADWAL
        $query_where    = [
            "jdw_uuid"      => $id,
        ];


        $query_update   = [
            "is_active"     => $is_activce,
        ];

        DB::table('programs_jadwal')->where($query_where)->update($query_update);

        try {
            DB::commit();
            $output     = array(
                "status"    => "berhasil",
                "errMsg"    => [],
            );
            LogHelper::create('delete', 'Berhasil Membatalkan Program Umrah : '.$id, $ip, $log_user_id);
        } catch(\Exception $e) {
            DB::rollBack();
            $output     = array(
                "status"    => "error",
                "errMsg"    => $e->getMessage(),
            );
            LogHelper::create('error_system', 'Gagal Membatalkan Program Umrah', $ip, $log_user_id);
        }

        return $output;
    }

    public static function getListDailyOperasional($data)
    {
        $start_date     = $data['start_date'];
        $end_date       = $data['end_date'];
        $program        = $data['program'];
        $sub_division   = $data['sub_divisi'];
        $aktivitas      = $data['aktivitas'];

        $query  = DB::select(
            "
            SELECT 	DISTINCT pkb.*
            FROM 	(
                    SELECT 	a.uuid as pkb_id,
                            CONCAT('[', UPPER(g.name), '] ', UPPER(a.pkb_description)) as pkb_title,
                            a.pkb_start_date, 
                            a.pkb_end_date,
                            e.prog_pkb_is_created,
                            e.prog_jdw_id as programs_id
                    FROM 	proker_bulanan a
                    JOIN 	proker_tahunan b ON SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) = b.uid
                    JOIN 	proker_tahunan_detail c ON (SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', -1) = c.pktd_seq AND b.id = c.pkt_id)
                    JOIN 	group_divisions d ON b.division_group_id = d.id
                    JOIN    tr_prog_jdw e ON a.uuid = e.prog_pkb_id
                    JOIN 	programs_jadwal f ON e.prog_jdw_id = f.jdw_uuid
                    JOIN 	programs g ON f.jdw_programs_id = g.id
                    JOIN 	programs_jadwal_rules h ON e.prog_rul_id = h.id
                    WHERE   d.name LIKE '%operasional%'
                    AND 	a.pkb_is_active = 't'
                    AND 	h.rul_pic_sdid LIKE '$sub_division'
                    AND     e.prog_rul_id LIKE '$aktivitas'

                    UNION ALL

                    SELECT 	a.uuid as pkb_id,
                            UPPER(a.pkb_title) as pkb_title, 
                            a.pkb_start_date, 
                            a.pkb_end_date,
                            null as prog_pkb_is_created,
                            'programs_id' as programs_id
                    FROM 	proker_bulanan a
                    JOIN 	proker_tahunan b ON SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) = b.uid
                    JOIN 	proker_tahunan_detail c ON (SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', -1) = c.pktd_seq AND b.id = c.pkt_id)
                    JOIN 	group_divisions d ON b.division_group_id = d.id
                    WHERE	pkb_title NOT LIKE '%[%'
                    AND 	d.name LIKE '%operasional%'
                    AND 	a.pkb_is_active = 't'
                    ) pkb
            WHERE   (pkb.pkb_start_date >= '$start_date' OR pkb.pkb_start_date <= '$end_date' OR pkb.pkb_end_date >= '$start_date' OR pkb.pkb_end_date <= '$end_date')
            AND     pkb.programs_id LIKE '$program'
            ORDER BY pkb.pkb_start_date ASC
            "
        );

        return $query;
    }

    // 20 JULY 2024
    public static function getListProkerAllOperasional($data)
    {
        $role   = $data['current_role'];
        $userID = $data['current_id'];

        $tahunan = DB::select(
            "
            SELECT 	a.uid as pkt_id,
                    a.pkt_title,
                    a.pkt_year,
                    c.pktd_seq,
                    c.pktd_title,
                    b.name as group_divisi
            FROM 	proker_tahunan a
            JOIN 	group_divisions b ON a.division_group_id = b.id
            JOIN 	proker_tahunan_detail c ON c.pkt_id = a.id
            WHERE 	b.name LIKE '%$role%'
            AND 	a.pkt_year = EXTRACT(YEAR FROM CURRENT_DATE)
            "
        );

        $tahunan_header = [];

        if(!empty($tahunan)) {
            for($i = 0; $i < count($tahunan); $i++) {
                $temp_header[] = array(
                    "pkt_id"        => $tahunan[$i]->pkt_id,
                    "pkt_title"     => $tahunan[$i]->pkt_title,
                    "pkt_year"      => $tahunan[$i]->pkt_year,
                    "pkt_group_division_name"   => $tahunan[$i]->group_divisi,
                    "detail"        => [],
                );
            }

            // TAHUNAN HEADER
            $header_remove_duplicate    = array_reduce($temp_header, function($carry, $item){
                if(!isset($carry[$item['pkt_id']])) {
                    $carry[$item['pkt_id']]     = $item;
                }
                return $carry;
            }, []);
    
            $tahunan_header   = array_values($header_remove_duplicate);

            for($j = 0; $j < count($tahunan_header); $j++) {

                for($k = 0; $k < count($tahunan); $k++) {
                    if($tahunan[$k]->pkt_id == $tahunan_header[$j]['pkt_id']) {
                        $tahunan_detail     = [
                            "pktd_seq"      => $tahunan[$k]->pktd_seq,
                            "pktd_title"    => $tahunan[$k]->pktd_title,
                        ];
                        array_push($tahunan_header[$j]['detail'], $tahunan_detail);
                    }
                }
            }

            $output     = array(
                "tahunan"   => $tahunan_header,
            );
        } else {
            $output     = array(
                "tahunan"   => $tahunan_header
            );
        }

        return $output;
    }

    public static function getListPIC($data)
    {
        $role   = $data['current_role'];
        return DB::select(
            "
            SELECT 	a.user_id,
                    a.name as user_name
            FROM 	employees a
            JOIN 	job_employees b ON b.employee_id = a.id
            JOIN 	group_divisions c ON b.group_division_id = c.id
            JOIN 	roles d ON c.roles_id = d.id
            WHERE 	d.name LIKE '%$role%'
            ORDER BY user_id ASC
            "
        );
    }

    public static function getDetailCalendarOperasional($data)
    {
        $pkb_id     = $data['pkb_id'];
        $query      = DB::select(
            "
            SELECT 	a.uuid as pkb_id,
                    a.pkb_start_date,
                    a.pkb_end_date,
                    a.pkb_title,
                    a.pkb_description,
                    SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) as pkt_id,
                    SUBSTRING_INDEX(a.pkb_pkt_id,' | ',-1) as pktd_id,
				    a.created_by
            FROM 	proker_bulanan a
            WHERE 	uuid = '$pkb_id'
            "
        );

        return $query;
    }

    // 22 JULY 2024
    // NOTE : SIMPAN DATA CALENDAR
    public static function doSimpanOperasionalJenisPekerjaan($data)
    {
        DB::beginTransaction();
        $ip         = $data['ip'];
        $user_id    = $data['user_id'];
        $user_role  = $data['user_role'];

        $pkb_id         = $data['data']['pkb_id'];
        $pkb_pkt_id     = $data['data']['pkt_id']." | ".$data['data']['pktd_id'];
        $pkb_created_by = $data['data']['pkb_created_by'];
        $pkb_title      = $data['data']['pkb_title'];
        $pkb_description= $data['data']['pkb_description'];
        $pkb_start_date = $data['data']['pkb_start_date'];
        $pkb_end_date   = $data['data']['pkb_end_date'];

        $employee_id    = DB::table('employees')->select('id')->where(['user_id' => $pkb_created_by])->get()[0]->id;
        
        $jenis          = $data['data']['jenis'];
        
        if($jenis == 'add') {
            $data_simpan    = [
                "uuid"              => Str::random(30),
                "pkb_title"         => $pkb_title,
                "pkb_start_date"    => $pkb_start_date,
                "pkb_end_date"      => $pkb_end_date,
                "pkb_description"   => $pkb_description,
                "pkb_pkt_id"        => $pkb_pkt_id,
                "pkb_employee_id"   => $employee_id,
                "pkb_is_active"     => "t",
                "created_by"        => $pkb_created_by,
                "created_at"        => date('Y-m-d H:i:s'),
                "updated_by"        => $user_id,
                "updated_at"        => date('Y-m-d H:i:s'),
            ];

            DB::table('proker_bulanan')->insert($data_simpan);
            $id     = DB::getPdo()->lastInsertId();
            $pkb_id     = DB::table('proker_bulanan')->select('uuid')->where(['id' => $id])->get()[0]->uuid;
        } else if($jenis == 'edit') {
            // CHECK APAKAH DIA YANG PLANNING ATAU BUKAN
            $check      = DB::table('tr_prog_jdw')->where(['prog_pkb_id' => $pkb_id, 'prog_pkb_is_created' => 'f'])->get();
            if(count($check) > 0) {
                // UPDATE TR PROG JDW
                $where_prog_jdw     = [
                    "prog_pkb_id"           => $pkb_id,
                    "prog_pkb_is_created"   => "f",
                ];
                $update_data        = [
                    "prog_pkb_is_created"   => "t",
                ];
                DB::table('tr_prog_jdw')->where($where_prog_jdw)->update($update_data);
            } else {
                // DO NOTHING
            }
            // UPDATE PROKER BULANAN
            $where_proker_bulanan   = [
                "uuid"          => $pkb_id,
                "pkb_is_active" => "t",
            ];
            $data_proker_bulanan    = [
                "pkb_title"         => $pkb_title,
                "pkb_start_date"    => $pkb_start_date,
                "pkb_end_date"      => $pkb_end_date,
                "pkb_description"   => $pkb_description,
                "pkb_pkt_id"        => $pkb_pkt_id,
                "pkb_employee_id"   => $employee_id,
                "created_by"        => $user_id,
                "created_at"        => date('Y-m-d H:i:s'),
                "updated_by"        => $user_id,
                "updated_at"        => date('Y-m-d H:i:s'), 
            ];
            DB::table('proker_bulanan')->where($where_proker_bulanan)->update($data_proker_bulanan);
        }

        try {
            DB::commit();
            $jenis == 'add' ? LogHelper::create('add', 'Berhasil Menambahkan Program Kerja Operasional Baru : '.$pkb_id, $ip) : LogHelper::create('edit', 'Berhasil Mengubah Program Kerja Operasional : '.$pkb_id, $ip);

            $output     = array(
                "status"    => "berhasil",
                "errMsg"    => [],
            );
        } catch(\Exception $e) {
            DB::rollBack();
            $jenis == 'add' ? LogHelper::create('error_system', 'Gagal Menambahkan Program Kerja Bulanan Baru', $ip) : LogHelper::create('error_system', 'Gagal Merubah Program Kerja Bulanan Baru', $ip);
            
            $output     = array(
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            );
        }

        return $output;
    }

    public static function getDataListFilter()
    {
        // GET DATA PROGRAM
        $query_program  = DB::select(
            "
            SELECT 	UPPER(d.name) as program_name,
                    c.jdw_depature_date as program_start_date,
                    c.jdw_arrival_date as program_end_date,
                    c.jdw_uuid
            FROM 	proker_bulanan a
            JOIN 	tr_prog_jdw b ON a.uuid = b.prog_pkb_id
            JOIN 	programs_jadwal c ON b.prog_jdw_id = c.jdw_uuid
            JOIN 	programs d ON c.jdw_programs_id = d.id
            WHERE 	pkb_title LIKE '%[%'
            AND 	a.pkb_is_active = 't'
            GROUP BY d.name, c.jdw_depature_date, c.jdw_arrival_date, c.jdw_uuid
            "
        );

        $query_sub_division     = DB::select(
            "
            SELECT 	e.id as sub_division_id,
                    e.name as sub_division_name
            FROM 	employees a
            JOIN 	job_employees b ON a.id = b.employee_id
            JOIN 	group_divisions c ON b.group_division_id = c.id
            JOIN 	roles d ON c.roles_id = d.id
            JOIN 	sub_divisions e ON e.division_group_id = c.id
            WHERE 	d.name LIKE '%operasional%'
            GROUP BY e.id, e.name
            "
        );

        $output     = [
            "program"       => $query_program,
            "sub_division"  => $query_sub_division,
        ];

        return $output;
    }

    public static function getCurrentSubDivision()
    {
        $user_id    = Auth::user()->id;

        return DB::select(
            "
            SELECT 	c.id as sub_division_id,
                    LOWER(c.name) as sub_division_name
            FROM 	employees a 
            JOIN 	job_employees b ON a.id = b.employee_id
            JOIN 	sub_divisions c ON b.sub_division_id = c.id
            WHERE 	a.user_id = '$user_id'
            "
        );
    }

    public static function doHapusJenisPekerjaan($data)
    {
        DB::beginTransaction();

        $pkb_id     = $data['pkb_id'];
        $ip         = $data['ip'];
        $user_id    = $data['user_id'];

        DB::table('proker_bulanan')->where(['uuid' => $pkb_id])->update(['pkb_is_active'=>'f', 'updated_by' => $user_id, 'updated_at' => date('Y-m-d H:i:s')]);

        try {
            DB::commit();
            LogHelper::create('delete', 'Berhasil Menghapus Program Kerja Operasional : '.$pkb_id, $ip);
            $output     = array(
                "status"    => "berhasil",
                "errMsg"    => '',
            );
        } catch(\Exception $e) {
            DB::rollBack();
            LogHelper::create('error_system', 'Gagal Hapus Proker Bulanan', $ip);

            $output     = array(
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            );
        }

        return $output;
    }

    public static function getListAktivitasProgram($data)
    {
        $sub_division   = $data['sub_division_id'];
        $query  = DB::select(
            "
            SELECT 	a.id as rule_id,
                    a.rul_title,
                    a.rul_pic_sdid as rul_sdid,
                    b.name as rul_sdid_name,
                    CONCAT('H', a.rul_sla) as rul_sla
            FROM 	programs_jadwal_rules a
            JOIN 	sub_divisions b ON a.rul_pic_sdid = b.id
            WHERE   a.rul_pic_sdid LIKE '%$sub_division%'
            ORDER BY a.id ASC
            "
        );

        return $query;
    }

    public static function doGenerateWithAPI($data_api, $ip)
    {
        DB::beginTransaction();
        // LogHelper::prettier($data_api->data->jadwal);

        $api_data   = $data_api->data->jadwal;
        $data_ke    = 0;
        $db_data    = DB::select(
            "
            SELECT  *
            FROM    programs_jadwal
            "
        );

        for($i = 0; $i < count($api_data); $i++) {
            $tour_code  = $api_data[$i]->KODE;
            $program_id = $api_data[$i]->ERP_PROGRAM_ID;
            $dpt_date   = date('Y-m-d', strtotime($api_data[$i]->BERANGKAT));
            $arv_date   = date('Y-m-d', strtotime($api_data[$i]->PULANG));

            for($j = 0; $j < count($db_data); $j++) {
                $db_data_program_id     = $db_data[$j]->jdw_programs_id;
                $db_data_dpt_date       = $db_data[$j]->jdw_depature_date;
                $db_data_arv_date       = $db_data[$j]->jdw_arrival_date;
                $db_data_jdw_uuid       = $db_data[$j]->jdw_uuid;

                if(($db_data_program_id == $program_id) && ($db_data_dpt_date == $dpt_date)) {
                    // UPDATE
                    $update_where   = [
                        'jdw_uuid'      => $db_data_jdw_uuid,
                    ];

                    $update_data    = [
                        'jdw_tour_code' => $tour_code,
                    ];

                    DB::table('programs_jadwal')->where($update_where)->update($update_data);
                    $data_ke    = $data_ke + 1;
                    break;
                }
            }
        }

        try {
            DB::commit();
            LogHelper::create('add', 'Berhasil Generate Data Tour Code sebanyak '.$data_ke, $ip);

            return 'Berhasil Generate Data Tour Code Sebanyak : '.$data_ke;
        } catch(\Exception $e) {
            DB::rollBack();
            LogHelper::create('error_system', $e->getMessage(), $ip);

            return $e->getMessage();
        }
    }

    public static function doSimpanJadwalUmrahV2($data)
    {
        // LogHelper::prettier($data);
        DB::beginTransaction();

        $user_id    = $data['user_id'];
        $user_role  = $data['user_role'];
        $jenis      = $data['data']['program_umrah_jenis'];
        $today      = date('Y-m-d H:i:s');
        $ip         = $data['ip'];
        $tour_code  = $data['data']['program_umrah_tour_code'];
        // SIMPAN DATA KE TABLE
        if($jenis == 'add') {
            // CHECK DULU
            $check          = DB::select(
                "
                SELECT  id
                FROM    programs_jadwal
                WHERE   jdw_tour_code = '$tour_code'
                AND     is_active = 't'
                "
            );
            if(!empty($check)) {
                DB::rollBack();
                LogHelper::create('error_system', 'Program Jadwal Umrah telah tersedia', $ip);
                $output     = array(
                    "status"    => "duplicate",
                    "errMsg"    => "Program Jadwal Umrah Telah Dibuat",
                );
                return $output;
            } else {
                $data_simpan    = array(
                    "jdw_uuid"          => str::uuid(),
                    "jdw_programs_id"   => $data['data']['program_umrah_program_id'],
                    "jdw_depature_date" => $data['data']['program_umrah_dpt_date'],
                    "jdw_arrival_date"  => $data['data']['program_umrah_arv_date'],
                    "jdw_mentor_name"   => $data['data']['program_umrah_mentor_name'], 
                    "jdw_tour_code"     => $data['data']['program_umrah_tour_code'],
                    "is_generated"      => "f",
                    "is_active"         => "t",
                    "created_by"        => $user_id,
                    "created_at"        => $today,
                    "updated_by"        => $user_id,
                    "updated_at"        => $today,
                );
                DB::table('programs_jadwal')->insert($data_simpan);
                $jdw_uuid   = $data_simpan['jdw_uuid'];
            }
        } else if($jenis == 'edit') {
            $jdw_uuid   = $data['data']['program_umrah_id'];
            
            $data_where = [
                "jdw_uuid"      => $jdw_uuid,
            ];
            
            $data_update= [
                "jdw_programs_id"   => $data['data']['program_umrah_program_id'],
                "jdw_depature_date" => $data['data']['program_umrah_dpt_date'], 
                "jdw_arrival_date"  => $data['data']['program_umrah_arv_date'],
                "jdw_mentor_name"   => $data['data']['program_umrah_mentor_name'],
                "jdw_tour_code"     => $data['data']['program_umrah_tour_code'],
                "updated_by"        => $user_id,
                "updated_at"        => $today
            ];

            DB::table('programs_jadwal')->where($data_where)->update($data_update);
        }

        try {
            DB::commit();
            $jenis == 'add' ? LogHelper::create('add', 'Berhasil Menambahkan Program Umrah Baru : '.$jdw_uuid, $ip) : LogHelper::create('edit', 'Berhasi Merubah Program Umrah : '.$jdw_uuid, $ip);
            $output     = array(
                "status"    => "berhasil",
                "errMsg"    => "",
            );
        } catch (\Exception $e) {
            DB::rollBack();
            $jenis == 'add' ? LogHelper::create('error_system', 'Gagal Menambahkan Program Umrah Baru', $ip) : LogHelper::create('error_system', 'Gagal Merubah Program Umrah', $ip);
            $output     = array(
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            );
        }

        return $output;
    }

    public static function getEventsFinance($data)
    {
        $start_date     = $data['sendData']['start_date'];
        $end_date       = $data['sendData']['end_date'];
        $user_id        = $data['user_id'];
        $user_role      = $data['user_role'];

        $getData        = DB::select(
            "
            SELECT 	a.uuid as pkb_uid,
                    a.pkb_title,
                    a.pkb_start_date,
                    a.pkb_end_date
            FROM 	proker_bulanan a
            JOIN 	employees b ON a.created_by	= b.user_id
            JOIN 	job_employees c ON b.id = c.employee_id
            JOIN 	group_divisions d ON c.group_division_id = d.id
            WHERE 	d.name LIKE '%$user_role%'
            AND 	a.pkb_is_active = 't'
            AND 	a.pkb_start_date BETWEEN '$start_date' AND '$end_date'
            ORDER BY a.pkb_start_date ASC
            "
        );

        return $getData;
    }
    
    // 26 JULY 2024
    // NOTE : GET DATA TOUR CODE
    public static function getTourCode($data)
    {
        $tour_code  = $data['tour_code'];

        $header  = DB::select(
            "
            SELECT  a.jdw_tour_code as tour_code,
                    a.jdw_depature_date,
                    a.jdw_arrival_date
            FROM    programs_jadwal a
            WHERE   a.is_active = 't'
            AND     a.is_generated = 't'
            AND     a.jdw_uuid LIKE '$tour_code'
            "
        );

        $detail     = DB::select(
            "
            SELECT  b.uuid as pkb_id,
                    b.pkb_description as pkb_title,
                    b.pkb_start_date,
                    b.pkb_end_date,
                    c.jdw_tour_code as tour_code
            FROM    tr_prog_jdw a
            JOIN    proker_bulanan b ON a.prog_pkb_id = b.uuid
            JOIN    programs_jadwal c ON a.prog_jdw_id = c.jdw_uuid
            WHERE   a.prog_pkb_is_created = 't'
            AND     a.prog_jdw_id LIKE '$tour_code'
            AND     b.pkb_is_active = 't'
            AND     b.pkb_is_pay IS NULL
            "
        );

        $output     = array(
            "header"    => $header,
            "detail"    => $detail, 
        );

        return $output;
    }

    public static function doSimpanAktivitas($data)
    {
        DB::beginTransaction();

        $jenis      = $data['jenis'];
        $user_id    = $data['user_id'];
        $ip         = $data['ip'];

        // GET EMPLOYEE
        $employee_id    = DB::table('employees')->select('id')->where(['user_id' => $user_id])->get()[0]->id;

        if($jenis == 'add') {
            // SIMPAN DATA AKTIVIGTAS FINANCE
            $data_simpan    = [
                "uuid"              => Str::random(30),
                "pkb_title"         => $data['sendData']['fin_title'],
                "pkb_start_date"    => $data['sendData']['fin_date'],
                "pkb_end_date"      => $data['sendData']['fin_date'],
                "pkb_description"   => $data['sendData']['fin_description'],
                "pkb_pkt_id"        => !empty($data['sendData']['opr_pkb_id']) ? $data['sendData']['opr_pkb_id'] : "",
                "pkb_employee_id"   => $employee_id,
                "pkb_is_active"     => "t",
                "created_by"        => $user_id,
                "created_at"        => date('Y-m-d H:i:s'),
                "updated_by"        => $user_id,
                "updated_at"        => date('Y-m-d H:i:s'),
            ];

            DB::table('proker_bulanan')->insert($data_simpan);

            if($data['sendData']['fin_category'] == 'jpk_operasional') {
                // UPDATE PROKER BULANAN
                $pkb_id     = $data['sendData']['opr_pkb_id'];

                DB::table('proker_bulanan')->where(['uuid' => $pkb_id])->update(['pkb_is_pay' => 't']);
            }

            try {
                DB::commit();
                LogHelper::create('add', 'Berhasil Menambahkan Data Aktivitas Baru : '. $data_simpan['uuid'], $ip);
                $output     = [
                    "status"    => "berhasil",
                    "errMsg"    => [],
                ];
            } catch(\Exception $e) {
                DB::rollBack();
                LogHelper::create('add', 'Gagal Menambahkan Data Aktivitas Baru', $ip);
                $output     = [
                    "status"    => "gagal",
                    "errMsg"    => $e->getMessage(),
                ];
            }
        } else if($jenis == 'edit') {
            $data_where     = array(
                "uuid"      => $data['sendData']['fin_ID'],
            );

            $data_update    = array(
                "pkb_title"         => $data['sendData']['fin_title'],
                "pkb_start_date"    => $data['sendData']['fin_date'], 
                "pkb_end_date"      => $data['sendData']['fin_date'],
                "pkb_description"   => $data['sendData']['fin_description'],
                "updated_by"        => $user_id,
                "updated_at"        => date('Y-m-d H:i:s'),
            );

            DB::table('proker_bulanan')->where($data_where)->update($data_update);

            try {
                DB::commit();
                LogHelper::create('edit', 'Berhasil Mengubah Data Aktivitas :'.$data['sendData']['fin_ID'], $ip);
                $output     = [
                    "status"    => "berhasil",
                    "errMsg"    => [],
                ];
            } catch(\Exception $e) {
                DB::rollBack();
                LogHelper::create('add', 'Gagal Mengubah Data Aktivitas', $ip);
                $output     = [
                    "status"    => "gagal",
                    "errMsg"    => $e->getMessage(),
                ];
            }
        } else if($jenis == 'hapus') {
            $data_where     = array(
                "uuid"      => $data['sendData']['fin_ID'],
            );

            $data_update    = array(
                "pkb_is_active" => "f",
            );

            DB::table('proker_bulanan')->where($data_where)->update($data_update);

            // CHECK APAKAH AKTIVITAS INI NYAMBUNG DENGAN OPERASIONAL
            $check   = DB::table('proker_bulanan')->where(['uuid'   => $data['sendData']['fin_ID'], 'pkb_pkt_id' => $data['sendData']['pkb_ID']])->get();

            if(count($check) > 0) {
                $data_where_update_ref  = [
                    "uuid"      => $data['sendData']['pkb_ID'],
                ];

                $data_update_ref        = [
                    "pkb_is_pay"=> null,
                ];

                DB::table('proker_bulanan')->where($data_where_update_ref)->update($data_update_ref);
            }

            try {
                DB::commit();
                LogHelper::create('edit', 'Berhasil Menghapus Data Aktivitas :'.$data['sendData']['fin_ID'], $ip);
                $output     = [
                    "status"    => "berhasil",
                    "errMsg"    => [],
                ];
            } catch(\Exception $e) {
                DB::rollBack();
                LogHelper::create('add', 'Gagal Menghapus Data Aktivitas', $ip);
                $output     = [
                    "status"    => "gagal",
                    "errMsg"    => $e->getMessage(),
                ];
            }
        }

        return $output;
    }

    public static function doGetEventsFinanceDetail($id)
    {
        return DB::select(
            "
            SELECT  *
            FROM    (
                    SELECT  a.uuid as pkb_id,
                            a.pkb_title,
                            a.pkb_start_date,
                            a.pkb_description,
                            c.jdw_tour_code,
                            'jpk_operasional' as jpk_status,
                            a.pkb_pkt_id as ref_id,
                            (SELECT pkb_description FROM proker_bulanan WHERE uuid = a.pkb_pkt_id) as ref_title,
                            (SELECT pkb_start_date FROM proker_bulanan WHERE uuid = a.pkb_pkt_id) as ref_date_start,
                            (SELECT pkb_end_date FROM proker_bulanan WHERE uuid = a.pkb_pkt_id) as ref_date_end
                    FROM    proker_bulanan a
                    JOIN    tr_prog_jdw b ON b.prog_pkb_id = a.pkb_pkt_id
                    JOIN    programs_jadwal c ON b.prog_jdw_id = c.jdw_uuid
                    WHERE   a.pkb_is_active = 't'

                    UNION

                    SELECT  a.uuid as pkb_id,
                            a.pkb_title,
                            a.pkb_start_date,
                            a.pkb_description,
                            null as jdw_tour_code,
                            'jpk_finance' as jpk_status,
                            REPLACE(a.pkb_pkt_id, '', 'test') as ref_id,
                            null as ref_title,
                            null as ref_date_start,
                            null as ref_date_end
                    FROM    proker_bulanan a
                    JOIN    employees b ON a.created_by = b.user_id
                    JOIN    job_employees c ON b.id = c.employee_id
                    JOIN    group_divisions d ON c.group_division_id = d.id
                    WHERE   d.name LIKE '%finance%'
                    AND     (LENGTH(a.pkb_pkt_id) < 1 OR a.pkb_pkt_id LIKE '%|%')
                    AND     a.pkb_is_active = 't'
            ) as act_det
            WHERE   act_det.pkb_id LIKE '$id'
            "
        );
    }

    public static function getListEventsDigital($data)
    {
        $user_id    = $data['user_id'];
        $start_date = $data['start_date'];
        $end_date   = $data['end_date'];
        return DB::select(
            "
            SELECT 	uuid as pkh_id,
                    pkh_title,
                    pkh_date
            FROM 	proker_harian
            WHERE 	created_by = '$user_id'
            AND 	pkh_date BETWEEN '$start_date' AND '$end_date'
            AND     pkh_is_active = 't'
            "
        );
    }

    public static function getListEventDigitalDetail($data)
    {
        $pkh_id     = $data['id'];
        $user_id    = $data['user_id'];

        return DB::select(
            "
            SELECT  uuid as pkh_id,
                    pkh_title,
                    pkh_pkb_id,
                    pkh_start_time,
                    pkh_end_time
            FROM    proker_harian
            WHERE   uuid = '$pkh_id'
            AND     created_by = '$user_id'
            "
        );
    }

    public static function getListProgramDigital($data)
    {
        $current_month  = date('m', strtotime($data['today']));
        $current_year   = date('Y', strtotime($data['today']));
        $user_id        = $data['user_id'];

        $header  = DB::select(
            "
            SELECT 	a.id,
                    b.uuid as pkb_id,
                    a.name,
                    d.pktd_title,
                    b.pkb_is_active
            FROM 	master_program a
            JOIN 	proker_bulanan b ON a.id = b.master_program_id
            JOIN 	proker_tahunan c ON SUBSTRING_INDEX(b.pkb_pkt_id, ' | ', 1) = c.uid
            JOIN 	proker_tahunan_detail d ON (d.pkt_id = c.id AND SUBSTRING_INDEX(b.pkb_pkt_id, ' | ', -1) = d.pktd_seq)
            JOIN 	proker_bulanan_detail f ON b.id = f.pkb_id
            WHERE 	EXTRACT(YEAR FROM b.pkb_start_date) = '$current_year'
            AND 	EXTRACT(MONTH FROM b.pkb_start_date) = '$current_month'
            AND 	f.pkbd_pic LIKE '$user_id'
            AND 	b.pkb_is_active = 't'
            GROUP BY a.id, b.uuid, a.name, d.pktd_title, b.pkb_is_active
            "
        );

        $detail     = DB::select(
            "
            SELECT 	a.uuid as pkb_id,
                    b.id as pkbd_id,
                    b.pkbd_type,
                    b.pkbd_pic
            FROM 	proker_bulanan a
            JOIN 	proker_bulanan_detail b ON a.id = b.pkb_id
            WHERE 	a.pkb_is_active = 't'
            AND 	b.pkbd_pic LIKE '$user_id'
            AND 	EXTRACT(YEAR FROM a.pkb_start_date) = '$current_year'
            AND 	EXTRACT(MONTH FROM a.pkb_start_date) = '$current_month'
            ORDER BY a.pkb_start_date, a.id, b.id ASC
            "
        );

        $output     = [
            "header"    => $header,
            "detail"    => $detail
        ];

        return $output;
    }

    public static function doSimpanAktivitasHarianDigital($data)
    {
        DB::beginTransaction();

        if($data['jenis'] == 'add') {
            // SIMPAN KE PROKER HARIAN
            $data_insert_proker_harian  = [
                "uuid"              => Str::random(30),
                "pkh_title"         => $data['data']['daily_title'],
                "pkh_date"          => $data['data']['daily_date'],
                "pkh_start_time"    => $data['data']['daily_date']." ".$data['data']['daily_startTime'],
                "pkh_end_time"      => $data['data']['daily_date']." ".$data['data']['daily_endTime'],
                "pkh_pkb_id"        => $data['data']['daily_programID']." | ".$data['data']['daily_programDetailID'],
                "pkh_is_active"     => "t",
                "pkh_total_activity"=> 1,
                "created_by"        => $data['user_id'],
                "created_at"        => date('Y-m-d H:i:s'),
                "updated_by"        => $data['user_id'],
                "updated_at"        => date('Y-m-d H:i:s'),
            ];

            DB::table('proker_harian')->insert($data_insert_proker_harian);

            // UPDATE PPROKER BULANAN HEADER
            $query_bulanan_header   = DB::table('proker_bulanan')->where([ 'uuid' => $data['data']['daily_programID'] ]);
            if(!empty($query_bulanan_header)) {
                $pkb_where  = [ 'uuid' => $data['data']['daily_programID'] ];
                $pkb_update = [ 'updated_by' => $data['user_id'], 'updated_at' => date('Y-m-d H:i:s') ];
                DB::table('proker_bulanan')->where($pkb_where)->update($pkb_update);
            }
            // UPDATE PROKER BULANAN DETAIL
            $query_bulanan_detail   = DB::table('proker_bulanan_detail')->where(['id' => $data['data']['daily_programDetailID']])->get();
            if(!empty($query_bulanan_detail)) {
                $pkbd_num_result    = $query_bulanan_detail[0]->pkbd_num_result + 1;
                $pkbd_where_update  = [ 'id' => $data['data']['daily_programDetailID'] ];
                DB::table('proker_bulanan_detail')->where($pkbd_where_update)->update(['pkbd_num_result' => $pkbd_num_result]);
            }
        } else if($data['jenis'] == 'edit') {
            // PROKER BALIKIN DATA PROKER BULANAN
            $pkh_id     = $data['data']['daily_ID'];

            $query_get_proker_bulanan   = DB::select(
                "
                SELECT  *
                FROM    proker_harian
                WHERE   uuid = '$pkh_id'
                "
            );

            if(!empty($query_get_proker_bulanan)) {
                $data_where     = [
                    "uuid"      => $data['data']['daily_ID'],
                ];
                $data_update    = [
                    "pkh_title"         => $data['data']['daily_title'],
                    "pkh_start_time"    => $data['data']['daily_date']." ".$data['data']['daily_startTime'],
                    "pkh_end_time"      => $data['data']['daily_date']." ".$data['data']['daily_endTime'],
                    "pkh_pkb_id"        => $data['data']['daily_programID']." | ".$data['data']['daily_programDetailID'],
                    "updated_by"        => $data['user_id'],
                    "updated_at"        => date('Y-m-d H:i:s'),
                ];
                DB::table('proker_harian')->where($data_where)->update($data_update);
                
                // UPDATE TABLE BULANAN JIKA ADA PERUBAHAN
                if($data['data']['daily_programID']." | ".$data['data']['daily_programDetailID'] != $query_get_proker_bulanan[0]->pkh_pkb_id) {
                    // BALIKIN DATA LAMA
                    $proker_bulanan_seq     = explode(" | ", $query_get_proker_bulanan[0]->pkh_pkb_id)[1];
                    $query_proker_bulanan_detail    = DB::table('proker_bulanan_detail')->where([ 'id' => $proker_bulanan_seq ])->get()[0];
                    $pkbd_num_result_old            = $query_proker_bulanan_detail->pkbd_num_result - 1;
                    // UPDATE PROKER BULANAN DETAIL OLD
                    DB::table('proker_bulanan_detail')->where(['id'=>$proker_bulanan_seq])->update(['pkbd_num_result' => $pkbd_num_result_old]);

                    // UBAH DATA BARU
                    $proker_bulanan_seq_baru            = $data['data']['daily_programDetailID'];
                    $query_proker_bulanan_detail_baru   = DB::table('proker_bulanan_detail')->where([ 'id'  => $proker_bulanan_seq_baru ])->get()[0];
                    $pkbd_num_result_new                = $query_proker_bulanan_detail_baru->pkbd_num_result + 1;
                    DB::table('proker_bulanan_detail')->where(['id'=>$proker_bulanan_seq_baru])->update(['pkbd_num_result' => $pkbd_num_result_new]);
                }
            }
        } else if($data['jenis'] == 'hapus') {
            $pkh_id     = $data['data']['daily_ID'];
            // $pkh_id      = 9999;

            $query_get_proker_harian    = DB::table('proker_harian')->where(['uuid' => $pkh_id])->get();
            if(count($query_get_proker_harian) > 0) {
                $proker_bulanan_uuid    = explode(' | ', $query_get_proker_harian[0]->pkh_pkb_id)[0];
                $proker_bulanan_seq     = explode(' | ', $query_get_proker_harian[0]->pkh_pkb_id)[1];

                // UPDATE DATA PROKER HARIAN
                DB::table('proker_harian')
                    ->where(['uuid' => $pkh_id])
                    ->update(['pkh_is_active' => 'f', 'updated_by' => $data['user_id'], 'updated_at' => date('Y-m-d H:i:s')]);

                // UPDATE DATA PROKER BULANAN
                DB::table('proker_bulanan')
                    ->where(['uuid' => $proker_bulanan_uuid])
                    ->update(['updated_by' => $data['user_id'], 'updated_at' => date('Y-m-d H:i:s')]);
                
                // BALIKIN DARA DETAIL
                $query_get_proker_bulanan_detail    = DB::table('proker_bulanan_detail')
                                                        ->where(['id' => $proker_bulanan_seq])
                                                        ->get()[0];
                $pkbd_num_result_table              = $query_get_proker_bulanan_detail->pkbd_num_result - 1;
                DB::table('proker_bulanan_detail')
                    ->where(['id' => $proker_bulanan_seq])
                    ->update(['pkbd_num_result' => $pkbd_num_result_table]);
            }
        }

        try {
            DB::commit();
            switch($data['jenis']) {
                case 'add' :
                        LogHelper::create('add', 'Berhasil Menambahkan Data Jenis Pekerjaan Baru : '.$data_insert_proker_harian['uuid'], $data['ip']);
                    break;
                case 'edit' :
                        LogHelper::create('edit', 'Berhasil Merubah Data Jenis Pekerjaan : '.$data['data']['dailyID'], $data['ip']);
                    break;
                case 'hapus' :
                        LogHelper::create('delete', 'Berhasil Menghapus Data Jenis Pekerjaan : '.$data['data']['daily_ID'], $data['ip']);
                    break;
            }
            $output     = [
                "status"    => "berhasil",
                "errMsg"    => ""
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            LogHelper::create('error_system', 'Gagal Melakukan Transaksi Jenis Pekerjaan Divisi Digital', $data['ip']);
            Log::channel('daily')->error($e->getMessage());

            $output     = [
                "status"    => "gagal",
                "errMsg"    => $e->getMessage()
            ];
        }
        
        return $output;
    }

    public static function getListActUser($data)
    {
        $user_id    = $data['user_id'];
        $today      = $data['today'];

        return DB::select(
            "
            SELECT 	a.pkb_title,
                    a.pkb_start_date,
                    f.pkbd_type as pkbd_title,
                    f.pkbd_num_target,
                    f.pkbd_num_result,
                    f.pkbd_pic
            FROM 	proker_bulanan a
            JOIN 	proker_tahunan b ON SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) = b.uid
            JOIN 	group_divisions c  ON b.division_group_id = c.id
            JOIN 	proker_tahunan_detail e ON (e.pkt_id = b.id AND SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', -1) = e.pktd_seq)
            JOIN 	proker_bulanan_detail f ON f.pkb_id = a.id
            WHERE 	a.pkb_is_active = 't'
            AND 	c.name LIKE '%marketing%'
            AND 	a.pkb_start_date = '$today'
            AND 	(f.pkbd_pic LIKE '$user_id' OR f.pkbd_pic IN ('0', '$user_id'))
            ORDER BY a.id, f.id, a.pkb_start_date ASC
            "
        );
    }

    public static function doGetRKAPOperasional($data)
    {
        $current_role   = $data['current_role'];
        $current_year   = $data['current_year'];
        $pkt_id         = $data['pkt_id'];

        $header     = DB::select(
            "
            SELECT 	a.uid as pkt_id,
                    a.pkt_title as pkt_title,
                    a.pkt_year as pkt_year,
                    a.pkt_description
            FROM 	proker_tahunan a
            JOIN 	group_divisions b ON a.division_group_id = b.id
            AND 	b.name LIKE '%$current_role%'
            AND 	a.pkt_year = '$current_year'
            AND     a.uid LIKE '%$pkt_id%'
            ORDER BY a.pkt_year DESC
            "
        );

        if($pkt_id != '%') {
            $detail     = DB::select(
                "
                SELECT 	a.uid as pkt_id,
                        a.pkt_title as pkt_title,
                        a.pkt_year as pkt_year,
                        c.pktd_seq as pkt_detail_seq,
                        c.pktd_title as pkt_detail_title
                FROM 	proker_tahunan a
                JOIN 	group_divisions b ON a.division_group_id = b.id
                JOIN 	proker_tahunan_detail c ON a.id = c.pkt_id
                AND 	b.name LIKE '%$current_role%'
                AND 	a.pkt_year = '$current_year'
                AND     a.uid LIKE '%$pkt_id%'
                ORDER BY a.pkt_year DESC, CAST(c.pktd_seq AS UNSIGNED) ASC  
                "
            );
        } else {
            $detail     = "";
        }

        $output     = [
            "header"    => !empty($header) ? $header : [],
            "detail"    => !empty($detail) ? $detail : [],
        ];

        return $output;
    }

    public static function getListAktivitasHarian($data)
    {
        $user_id    = $data['user_id'];
        $curr_date  = $data['selected_month'];

        $query      = DB::select(
            "
            SELECT 	a.uuid as pkb_id,
                    a.pkb_title,
                    b.pkbd_type,
                    b.pkbd_num_result,
                    b.pkbd_num_target,
                    b.pkbd_pic
            FROM 	proker_bulanan a
            JOIN 	proker_bulanan_detail b ON a.id = b.pkb_id
            WHERE 	a.pkb_start_date = '$curr_date'
            AND 	(b.pkbd_pic LIKE '$user_id' AND b.pkbd_pic IN ('0', '$user_id'))
            AND 	a.pkb_is_active = 't'
            "
        );

        return $query;
    }

    public static function doGetDataActUserChart($data)
    {
        $user_name  = $data['user_name'];

        $user_id    = DB::table('users')->where(['name' => $user_name])->get()[0]->id;

        $query      = DB::select(
            "
            SELECT 	*
            FROM 	(
                    SELECT 	a.id,
                            a.uuid as pkb_id,
                            a.pkb_start_date, 
                            a.pkb_end_date,
                            a.pkb_description,
                            c.jdw_tour_code,
                            a.created_at
                    FROM 	proker_bulanan a
                    JOIN 	tr_prog_jdw b ON a.uuid = b.prog_pkb_id
                    JOIN 	programs_jadwal c ON b.prog_jdw_id = c.jdw_uuid
                    WHERE 	a.pkb_is_active = 't'
                    AND 	a.created_by = '$user_id'

                    UNION ALL

                    SELECT 	a.id,
                            a.uuid as pkb_id,
                            a.pkb_start_date,
                            a.pkb_end_date,
                            a.pkb_title,
                            null as jdw_tour_code,
                            a.created_at
                    FROM 	proker_bulanan a
                    WHERE 	a.created_by = '$user_id'
                    AND 	a.pkb_is_active = 't'
                    AND 	a.pkb_title NOT LIKE '%[%]%'
            ) AS pkb_chart
            ORDER BY pkb_chart.created_at DESC
            "
        );

        return $query;
    }

    public static function getFinanceRKAPList($data)
    {
        $role       = $data['user_role'];
        $pkt_id     = $data['rkap_id'];

        $query      = DB::select(
            "
            SELECT 	a.uid as pkt_id,
                    a.pkt_title,
                    a.pkt_year,
                    b.name as pkt_group_division,
                    COUNT(c.pkt_id) as pkt_total_detail
            FROM 	proker_tahunan a
            JOIN 	group_divisions b ON a.division_group_id = b.id
            JOIN 	proker_tahunan_detail c ON a.id = c.pkt_id
            WHERE 	b.name LIKE '%$role%'
            AND     a.uid LIKE '$pkt_id'
            GROUP BY a.uid, a.pkt_title, a.pkt_year, b.name
            ORDER BY a.pkt_year DESC
            "
        );

        return $query;
    }

    public static function doSimpanRKAPFinance($data)
    {
        DB::beginTransaction();
        $jenis      = $data['jenis'];
        $user_id    = $data['user_id'];
        $user_role  = $data['user_role'];
        $user_ip    = $data['ip'];
        
        // HEADER
        $rkap_id    = $data['data']['rkap_id'];
        $rkap_title = $data['data']['rkap_title'];
        $rkap_desc  = $data['data']['rkap_desc'];
        $rkap_year  = $data['data']['rkap_year'];
        $rkap_detail= $data['data']['rkap_detail'];

        // HILANGKAN DETAIL JIKA ADA YANG KOSONG
        $temp_detail    = [];
        for($i = 0; $i < count($rkap_detail); $i++) {
            if($rkap_detail[$i]['rkapd_title'] != "") {
                array_push($temp_detail, $rkap_detail[$i]);
            } 
        }

        // GET GROUP DIVISION ID
        $group_division  = DB::select(
            "
                SELECT  id
                FROM    group_divisions
                WHERE   name LIKE '%finance%'
            "
        );
        if($jenis == 'add')
        {
            // SIMPAN HEADER
            $data_simpan_header     = [
                "uid"               => Str::random(30),
                "pkt_title"         => $rkap_title,
                "pkt_description"   => $rkap_desc,
                "pkt_year"          => $rkap_year,
                "pkt_pic_job_employee_id"   => "",
                "division_group_id" => $group_division[0]->id,
                "created_by"        => $user_id,
                "created_at"        => date('Y-m-d H:i:s'),
                "updated_by"        => $user_id,
                "updated_at"        => date('Y-m-d H:i:s'),
            ];

            DB::table('proker_tahunan')->insert($data_simpan_header);
            
            $rkap_id   = DB::getPdo()->lastInsertId();

            // SIMPAN DETAIL
            for($j = 0; $j < count($temp_detail); $j++)
            {
                $data_detail    = $temp_detail[$j];
                
                $data_simpan_detail     = [
                    "pkt_id"        => $rkap_id,
                    "pktd_seq"      => $data_detail['rkapd_seq'],
                    "pktd_title"    => $data_detail['rkapd_title'],
                    "pktd_target"   => 0,
                ];

                DB::table('proker_tahunan_detail')->insert($data_simpan_detail);
            }
        } else if($jenis == 'edit') {
            // UPDATE HEADER
            $data_where_header  = [
                "uid"       => $rkap_id,
            ];
            
            $data_update_header  = [
                "pkt_title"         => $rkap_title,
                "pkt_description"   => $rkap_desc,
                "pkt_year"          => $rkap_year,
                "updated_by"        => $user_id,
                "updated_at"        => date('Y-m-d H:i:s'),
            ];
            DB::table('proker_tahunan')->where($data_where_header)->update($data_update_header);

            // UPDATE DETAIL
            // GET PKT ID
            $rkap_id_header     = DB::table('proker_tahunan')->select('id')->where($data_where_header)->get()[0]->id;
            // DELETE DATA BEFORE
            DB::table('proker_tahunan_detail')->where(['pkt_id' => $rkap_id_header])->delete();
            // INSERT NEW DATA
            for($i = 0; $i < count($temp_detail); $i++) {
                $data_detail    = $temp_detail[$i];

                $data_update_detail     = [
                    "pkt_id"        => $rkap_id_header,
                    "pktd_seq"      => $data_detail['rkapd_seq'],
                    "pktd_title"    => $data_detail['rkapd_title'],
                    "pktd_target"   => 0,
                ];
                DB::table('proker_tahunan_detail')->insert($data_update_detail);
            }   
        }

        try {
            DB::commit();

            if($jenis == 'add') {
                LogHelper::create('add', 'Berhasil Menambahkan Data RKAP Baru : '.$data_simpan_header['uid'], $user_ip);
            } else if($jenis == 'edit') {
                LogHelper::create('edit', 'Berhasil Mengubah Data RKAP : '.$rkap_id, $user_ip);
            }

            $output     = [
                "status"    => "berhasil",
                "errMsg"    => "",
            ];
        } catch(\Exception $e) {
            DB::rollBack();

            LogHelper::create('error_system', $jenis == 'add' ? "Gagal Menambahkan Data RKAP Baru" : "Gagal Merubah Data RKAP", $user_ip);
            Log::channel('daily')->error($e->getMessage());

            $output     = [
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            ];
        }

        return $output;
    }

    public static function doGetDataRKAP($data)
    {
        $pkt_uid    = $data['rkap_id'];

        // GET HEADER
        $header     = DB::select(
            "
            SELECT 	a.uid as pkt_id,
                    a.pkt_title,
                    a.pkt_description,
                    a.pkt_year
            FROM 	proker_tahunan a
            JOIN 	group_divisions b ON a.division_group_id = b.id
            WHERE 	a.uid = '$pkt_uid'
            AND     b.name LIKE '%finance%'
            "
        );

        $detail     = DB::select(
            "
            SELECT 	a.uid as pkt_id,
                    c.pktd_seq,
                    c.pktd_title
            FROM 	proker_tahunan a
            JOIN 	group_divisions b ON a.division_group_id = b.id
            JOIN 	proker_tahunan_detail c ON a.id = c.pkt_id
            WHERE 	a.uid = '$pkt_uid'
            AND 	b.name LIKE '%finance%'
            ORDER BY CAST(c.pktd_seq AS UNSIGNED) ASC
            "
        );

        $output     = [
            "header"    => !empty($header[0]) ? $header[0] : "",
            "detail"    => !empty($detail) ? $detail : []
        ];

        return $output;
    }

    public static function getListPengajuan($data)
    {
        $user_id        = $data['user_id'];
        $current_year   = $data['current_year'];

        return DB::select(
            "
            SELECT  a.emp_act_uuid as emp_act_id,
                    a.emp_act_user_id,
                    b.name as emp_act_user_name,
                    a.emp_act_title,
                    a.emp_act_start_date,
                    a.emp_act_end_date,
                    a.emp_act_type,
                    a.emp_act_status
            FROM    employees_activity a
            JOIN    employees b ON a.emp_act_user_id = b.user_id
            WHERE   a.emp_act_user_id LIKE '$user_id'
            AND     EXTRACT(YEAR FROM a.created_at) = '$current_year'
            AND     a.emp_act_type <> 'Lembur'
            ORDER BY a.emp_act_start_date DESC
            "
        );
    }

    public static function doSimpanPengajuanCuti($data)
    {
        DB::beginTransaction();

        // DATA DATA
        $user_id        = $data['user_id'];
        $pgj_id         = $data['data']['pgj_id'];
        $pgj_title      = $data['data']['pgj_title'];
        $pgj_start_date = $data['data']['pgj_date_start'];
        $pgj_end_date   = $data['data']['pgj_date_end'];
        $pgj_type       = $data['data']['pgj_type'];
        $pgj_status     = $data['data']['pgj_status'];
        $ip             = $data['ip'];
        
        if($pgj_status == "3")
        {
            $pgj_insert_data    = [
                "emp_act_uuid"      => Str::uuid(),
                "emp_act_user_id"   => $user_id,
                "emp_act_title"     => $pgj_title,
                "emp_act_start_date"=> $pgj_start_date,
                "emp_act_end_date"  => $pgj_end_date,
                "emp_act_type"      => $pgj_type,
                "emp_act_status"    => $pgj_status,
                "created_by"        => $user_id,
                "created_at"        => date('Y-m-d H:i:s'),
                "updated_by"        => $user_id,
                "updated_at"        => date('Y-m-d H:i:s'),
            ];
    
            DB::table('employees_activity')->insert($pgj_insert_data);
    
            try {
                DB::commit();
                LogHelper::create('add', 'Berhasil Membuat Pengajuan '.$pgj_type.' : '.$pgj_insert_data['emp_act_uuid'], $ip);
    
                $output     = [
                    "status"    => "berhasil",
                    "errMsg"    => "",
                ];
    
            } catch(\Exception $e) {
                DB::rollBack();
                Log::channel('daily')->error($e->getMessage());
                LogHelper::create('error_system', 'Gagal Membuat Pengajuan '.$pgj_type, $ip);
    
                $output     = [
                    "status"    => "gagal",
                    "errMsg"    => $e->getMessage(),
                ];
            }
        } else {
            $pgj_where  = [
                "emp_act_uuid"  => $pgj_id,
            ];
            $pgj_update = [
                "emp_act_status"=> $pgj_status,
                "updated_by"    => $user_id,
                "updated_at"    => date('Y-m-d H:i:s'),
            ];

            DB::table('employees_activity')->where($pgj_where)->update($pgj_update);

            try {
                DB::commit();
                LogHelper::create('edit', 'Berhasil Konfirmasi Pengajuan '.$pgj_type.' dengan ID : '.$pgj_id, $ip);

                $output     = [
                    "status"    => "berhasil",
                    "errMsg"    => "",
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                Log::channel('daily')->error($e->getMessage());
                LogHelper::create('error_system', 'Gagal Konfirmasi Pengajuan'.$pgj_type, $ip);

                $output     = [
                    "status"    => "gagal",
                    "errMsg"    => $e->getMessage(),
                ];
            }
        }

        return $output;
    }

    // 26 AGUSTUS 2024
    // NOTE : AMBIL LIST ABSENSI
    public static function getListAbsensi($data)
    {
        $tanggal_awal   = $data['data']['tanggal_awal'];
        $tanggal_akhir  = $data['data']['tanggal_akhir'];
        $user_cari      = $data['data']['user_id'];
        $jml_hari       = $data['data']['jml_hari'];
        

        $abs_data       = [];

        // GET DATA USER
        $query_get_data_user    = DB::select(
            "
            SELECT  a.user_id,
                    a.name as user_name
            FROM    employees a 
            WHERE   a.user_id LIKE '$user_cari'
            AND     a.user_id NOT IN ('1')
            ORDER BY a.user_id ASC
            "
        );

        for($i = 0; $i < count($query_get_data_user); $i++)
        {
            $curr_user  = $query_get_data_user[$i]->user_id;
            $curr_name  = $query_get_data_user[$i]->user_name;

            $tgl_awal   = $tanggal_awal;
            $tgl_akhir  = $tanggal_akhir;
            for($j = 1; $j <= $jml_hari; $j++) {
                if($tgl_awal != date('Y-m-d', strtotime($tgl_akhir . '+1 day'))) {
                    $query_get_data_absen   = DB::select(
                        "
                        SELECT  *
                        FROM    tm_presence
                        WHERE   prs_user_id = '$curr_user'
                        AND     prs_date = '$tgl_awal'
                        "
                    );
                    if(count($query_get_data_absen) > 0) {
                        $abs_data[]     = [
                            "nama"              => $curr_name,
                            "tanggal_absen"     => $query_get_data_absen[0]->prs_date,
                            "jam_masuk"         => $query_get_data_absen[0]->prs_in_time == '' ? '00:00:00' : date('H:i:s', strtotime($query_get_data_absen[0]->prs_in_time)),
                            "jam_keluar"        => $query_get_data_absen[0]->prs_out_time == '' ? '00:00:00' : date('H:i:s', strtotime($query_get_data_absen[0]->prs_out_time)),
                        ];
                    } else {
                        $abs_data[]     = [
                            "nama"              => $curr_name,
                            "tanggal_absen"     => $tgl_awal,
                            "jam_masuk"         => "00:00:00",
                            "jam_keluar"        => "00:00:00"
                        ];
                    }
                    $tgl_awal = date('Y-m-d', strtotime($tgl_awal . '+1 day'));
                } else {
                    $tgl_awal   = $tanggal_awal;
                    $tgl_akhir  = $tanggal_akhir;
                }
            }
        }

        return $abs_data;
    }

    public static function getDataEmployee()
    {
        return DB::select(
            "
            SELECT  a.user_id as emp_id,
                    b.name as emp_name,
                    d.name as emp_divisi
            FROM    employees a
            JOIN 	users b ON b.id = a.user_id
            JOIN 	job_employees c ON a.id = c.employee_id
            JOIN 	group_divisions d ON c.group_division_id = d.id
            WHERE   user_id NOT IN ('1','38','41','25')
            AND 	b.is_active = '1'
            ORDER BY user_id ASC
            "
        );
    }

    public static function get_master_employees_fee()
    {
        $query  = DB::table('employees_fee as a')
                    ->select('a.employee_id as emp_id', 'e.name as emp_name', 'd.name as emp_division', 'a.employee_fee as emp_fee')
                    ->join('employees as b', 'a.employee_id', '=', 'b.id')
                    ->join('job_employees as c', 'b.id', '=', 'c.employee_id')
                    ->join('group_divisions as d', 'c.group_division_id', '=', 'd.id')
                    ->join('users as e', 'b.user_id', '=', 'e.id')
                    ->where('e.is_active', '=', '1')
                    ->orderBy('e.name', 'ASC')
                    ->get();
        return $query;
    }

    public static function do_update_employees_fee($data)
    {
        $emp_id     = $data['emp_id'];
        $emp_fee    = $data['emp_fee'];
        $ip_address = $data['ip'];
        $user_id    = $data['user_id'];

        DB::beginTransaction();

        // UPDATE EMP_FEE

        // CHECK DULU
        $emp_fee_check_data     = DB::table('employees_fee')->where(['employee_id' => $emp_id])->get();

        $emp_fee_where  = [
            "employee_id"   => $emp_id,
        ];
        $emp_fee_update = [
            "employee_fee"  => $emp_fee,
            "updated_by"    => $user_id,
            "updated_at"    => date('Y-m-d H:i:s'),
        ];
        
        /*
        KONDISI : 
        - KETIKA EMP_FEE_CHECK_DATA -> EMPLOYEE_FEE LEBIH BESAR DARI 0 MAKA INSERT KE HISTORY DAN UPDATE EMPLOYEES FEE
        - KETIKA EMP_FEE_CHECK_DATA -> EMPLOYEE_FEE KURANG DARI SAMA DENGAN 0 MAKA UPDATE EMPLOYEES FEE SAJA
        */
        if($emp_fee_check_data[0]->employee_fee > 0) {
            $emp_fee_insert_data    = [
                "employee_id"   => $emp_id,
                "employee_fee"  => $emp_fee_check_data[0]->employee_fee, 
                "created_by"    => $user_id,
                "created_at"    => date('Y-m-d', strtotime($emp_fee_check_data[0]->created_at)),
                "expired_at"    => date('Y-m-d'),
            ];
            DB::table('employees_fee_history')->insert($emp_fee_insert_data);
            DB::table('employees_fee')->where($emp_fee_where)->update($emp_fee_update);
        } else {
            DB::table('employees_fee')->where($emp_fee_where)->update($emp_fee_update);
        }

        try {
            DB::commit();
            LogHelper::create('edit', 'Berhasil Merubah Data Gaji Pokok Karyawan '.$emp_id, $ip_address);

            $output     = [
                "status"    => "berhasil",
                "errMsg"    => ""
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            LogHelper::create('error_system', 'Gagal Merubah Data Gaji Pokok Karyawan', $ip_address);
            Log::channel('daily')->error($e->getMessage());

            $output     = [
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            ];
        }

        return $output;
    }

    public static function get_list_lembur()
    {
        $user_id    = Auth::user()->getRoleNames()[0] == 'admin' ? '%' : Auth::user()->id;
        $query  = DB::table('employees_activity')
                    ->join('users as b', 'emp_act_user_id', '=', 'b.id')
                    ->select('emp_act_uuid as emp_act_id', 'emp_act_user_id as emp_user_id', 'b.name as emp_user_name', 'emp_act_start_date as emp_act_date', 'emp_act_title as emp_description', 'emp_act_type as emp_trans_type', 'emp_act_status as emp_trans_status')
                    ->where('emp_act_type', '=', 'Lembur')
                    ->where('emp_act_user_id', 'LIKE', '%'.$user_id.'%')
                    ->get();
        return $query;
    }

    public static function do_simpan_pengajuan_lembur($data)
    {
        DB::beginTransaction();
        $jenis      = $data['jenis'];
        $ip         = $data['ip'];

        $user_id    = $data['user_id'];
        $user_name  = $data['user_name'];

        $header     = $data['data']['header'];
        $detail     = $data['data']['detail'];

        if($jenis == 'add')
        {
            // INSERT HEADER
            $data_header    = [
                "emp_act_uuid"      => Str::uuid(),
                "emp_act_user_id"   => $user_id,
                "emp_act_title"     => $header['lmb_description'],
                "emp_act_start_date"=> date('Y-m-d'),
                "emp_act_end_date"  => date('Y-m-d'),
                "emp_act_type"      => "Lembur",
                "emp_act_status"    => 3,
                "created_by"        => $user_id,
                "created_at"        => date('Y-m-d H:i:s'),
                "updated_by"        => $user_id,
                "updated_at"        => date('Y-m-d H:i:s'),
            ];

            DB::table('employees_activity')->insert($data_header);
            $emp_act_id     = DB::getPdo()->lastInsertId();

            // INSERT DETAIL
            for($i = 0; $i < count($detail); $i++) {
                $data_detail    = [
                    "emp_act_id"        => $emp_act_id,
                    "empd_seq"          => $detail[$i]['lmbd_seq'],
                    "empd_description"  => $detail[$i]['lmbd_desc'],
                    "empd_date"         => $detail[$i]['lmbd_date'],
                    "empd_start_time"   => $detail[$i]['lmbd_start_time'],
                    "empd_end_time"     => $detail[$i]['lmbd_end_time'],
                    "empd_status"       => 0, 
                ];

                DB::table('employees_activity_detail')->insert($data_detail);
            }
        } else if($jenis == 'edit') {
            // GET ID
            $emp_act_id     = DB::table('employees_activity')->select('id')->where('emp_act_uuid', '=', $data['data']['header']['lmb_id'])->get()[0]->id;
            // UPDATE HEADER
            $data_where_header  = [
                "emp_act_uuid"      => $data['data']['header']['lmb_id']
            ];
            $data_update_header = [
                "updated_by"        => Auth::user()->id,
                "updated_at"        => date('Y-m-d H:i:s'),
            ];
            DB::table('employees_activity')->where($data_where_header)->update($data_update_header);

            // UPDATE DETAIL
            $emp_act_detail = $data['data']['detail'];
            
            // DELETE EMPLOYEES ACTIVITY USERS
            DB::table('employees_activity_detail')->where('emp_act_id', '=', $emp_act_id)->delete();

            // INSERT NEW DATA
            for($i = 0; $i < count($emp_act_detail); $i++) {
                if($emp_act_detail[$i]['lmbd_desc'] != '') {
                    $data_detail    = [
                        "emp_act_id"        => $emp_act_id,
                        "empd_seq"          => $emp_act_detail[$i]['lmbd_seq'],
                        "empd_description"  => $emp_act_detail[$i]['lmbd_desc'],
                        "empd_date"         => $emp_act_detail[$i]['lmbd_date'],
                        "empd_start_time"   => $emp_act_detail[$i]['lmbd_start_time'],
                        "empd_end_time"     => $emp_act_detail[$i]['lmbd_end_time'],
                        "empd_status"       => 0, 
                    ];
    
                    DB::table('employees_activity_detail')->insert($data_detail);
                }
            }
        }

        try {
            DB::commit();
            if($jenis == 'add') {
                LogHelper::create('add', 'Berhasil Membuat Pengajuan Lembur ID : '.$data_header['emp_act_uuid'], $ip);
            } else {
                LogHelper::create('edit', 'Berhasil Merubah Pengajuan Lembur ID : ', $ip);
            }

            $output     = [
                "status"    => "berhasil",
                "errMsg"    => "",
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            Log::channel('daily')->error($e->getMessage());
            LogHelper::create('error_system', 'Gagal Melakukan Pengajuan Lembur', $ip);

            $output     = [
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            ];
        }

        return $output;
    }

    public static function do_get_data_lembur($id)
    {
        $emp_act_id     = $id;

        $get_data_header    = DB::table('employees_activity as a')
                                    ->join('employees as b', 'a.emp_act_user_id', '=', 'b.user_id')
                                    ->join('job_employees as c', 'c.employee_id', '=', 'b.id')
                                    ->join('group_divisions as d', 'c.group_division_id', '=', 'd.id')
                                    ->join('users as e', 'b.user_id', '=', 'e.id')
                                    ->select('a.emp_act_uuid as emp_act_id', 'a.emp_act_user_id as emp_user_id','e.name as emp_user_name', 'a.emp_act_title as emp_act_description', 'd.name as emp_group_division', 'a.emp_act_status')
                                    ->where('a.emp_act_type', '=', 'Lembur')
                                    ->where('a.emp_act_uuid', '=', $emp_act_id)
                                    ->get();
        
        $get_data_detail    = DB::table('employees_activity as a')
                                ->join('employees_activity_detail as b', 'a.id', '=', 'b.emp_act_id')
                                ->select('a.emp_act_uuid as emp_act_id', 'b.empd_seq', 'b.empd_description', 'b.empd_date', 'b.empd_start_time', 'b.empd_end_time')
                                ->where('a.emp_act_type', '=', 'Lembur')
                                ->where('a.emp_act_uuid', '=', $emp_act_id)
                                ->get();

        $output         = [
            "header"    => $get_data_header,
            "detail"    => $get_data_detail,
        ];

        return $output;
    }

    public static function do_simpan_konfirmasi_data_lembur($data)
    {
        DB::beginTransaction();

        $emp_user_id    = $data['emp_user_id'];
        $emp_act_id     = $data['emp_act_id'];
        $emp_act_status = $data['emp_act_status'];

        $ip             = $data['ip'];

        $data_where     = [
            "emp_act_uuid"  => $emp_act_id,
        ];
        
        $data_update    = [
            "emp_act_status"=> $emp_act_status,
            "updated_by"    => $emp_user_id,
            "updated_at"    => date('Y-m-d H:i:s'),
        ];

        DB::table('employees_activity')->where($data_where)->update($data_update);

        try {
            DB::commit();
            LogHelper::create('edit', 'Berhasil Konfirmasi Pengajuan Lembur ID : '.$emp_act_id, $ip);

            $output     = [
                "status"    => "berhasil",
                "err_msg"   => "",
            ];
        } catch(\Exception $e) {
            DB::rollBack();

            Log::channel('daily')->error($e->getMessage());
            LogHelper::create('error_system', 'Gagal Konfirmasi Transaksi Pengajuan Lembur', $ip);
            
            $output     = [
                "status"    => "gagal",
                "err_msg"   => $e->getMessage(),
            ];
        }

        return $output;
    }

    public static function get_data_finance_sim_employees_fee($data)
    {
        $emp_id     = $data['emp_id'];
        $date_start = $data['date_start'];
        $date_end   = $data['date_end'];

        // GET HEADER
        $emp_header     = DB::table('employees_fee as a')
                                ->join('job_employees as b', 'b.employee_id', '=', 'a.employee_id')
                                ->join('group_divisions as c', 'b.group_division_id', '=', 'c.id')
                                ->select('a.employee_id as emp_id', 'a.employee_name as emp_name', 'a.employee_fee as emp_fee', 'c.name as emp_division')
                                ->where('a.employee_id', '=', $emp_id)
                                ->get();

        $emp_detail     = DB::table('tm_presence as a')
                            ->join('employees as b', 'a.prs_user_id', '=', 'b.user_id')
                            ->join('users as c', 'b.user_id', '=', 'c.id')
                            ->select('b.id as emp_id', 'c.name as emp_name', 'a.prs_date as emp_prs_date', 'a.prs_in_time as emp_prs_in_time', 'a.prs_out_time as emp_prs_out_time')
                            ->where('b.id', '=', $emp_id)
                            ->whereBetween('a.prs_date', [$date_start, $date_end])
                            ->orderBy('a.prs_date', 'ASC')
                            ->get();
        
        $output         = [
            "header"        => $emp_header,
            "detail"        => $emp_detail,
        ];

        return $output;
    }
}