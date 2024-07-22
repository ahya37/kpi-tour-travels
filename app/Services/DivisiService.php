<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
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
                    a.is_active as status_active
            FROM 	programs_jadwal a
            JOIN 	programs b on a.jdw_programs_id = b.id
            WHERE   a.jdw_uuid LIKE '$uuid'
            AND     EXTRACT(YEAR FROM a.jdw_depature_date) LIKE '$tahun_cari'
            AND     (EXTRACT(MONTH FROM a.jdw_depature_date) = '$bulan_cari' OR EXTRACT(MONTH FROM a.jdw_depature_date) LIKE '$bulan_cari') 
            AND     a.jdw_programs_id LIKE '$programs_id'
            ORDER BY a.jdw_depature_date, b.name ASC
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
                    a.is_generated as status_generated
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
                        "pkbd_type"     => $dataRules->rul_title
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
                    a.rul_condition
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
        
        // $query_get_rule_all     = DB::select(
        //     "
        //     SELECT 	a.prog_jdw_id,
        //             a.prog_rul_id,
        //             b.rul_title
        //     FROM 	tr_prog_jdw a
        //     JOIN 	programs_jadwal_rules b ON a.prog_rul_id = b.id
        //     WHERE 	prog_jdw_id = '$id'
        //     GROUP BY a.prog_jdw_id, a.prog_rul_id, b.rul_title
        //     ORDER BY CAST(a.prog_rul_id AS SIGNED) ASC
        //     "
        // );

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
                        WHEN LEFT(d.rul_sla, 1) = '-' THEN DATE_ADD(b.jdw_depature_date, INTERVAL d.rul_sla DAY)
                        WHEN LEFT(d.rul_sla, 1) = '+' THEN b.jdw_arrival_date
                        ELSE b.jdw_depature_date
                    END as start_date_job,
                    CASE
                        WHEN LEFT(d.rul_sla, 1) = '-' THEN b.jdw_depature_date
                        WHEN LEFT(d.rul_sla, 1) = '+' THEN DATE_ADD(b.jdw_arrival_date, INTERVAL d.rul_sla DAY)
                    END as end_date_job,
				    e.name as pic_role_name
            FROM 	tr_prog_jdw a
            JOIN 	programs_jadwal b ON a.prog_jdw_id = b.jdw_uuid
            JOIN 	programs_jadwal_rules d ON a.prog_rul_id = d.id
            JOIN  sub_divisions e ON d.rul_pic_sdid = e.id
            WHERE 	a.prog_jdw_id = '$id'
            ORDER BY d.id ASC
            "
        );

        $query_get_jdw_in_pkb  = DB::select(
            "
            SELECT 	b.prog_rul_id,
                    a.pkb_start_date,
                    a.pkb_end_date
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
            LogHelper::create('delete', 'Berhasil Membatalkan Program ID : '.$id, $ip);
        } catch(\Exception $e) {
            DB::rollBack();
            $output     = array(
                "status"    => "error",
                "errMsg"    => $e->getMessage(),
            );
            LogHelper::create('error_system', 'Gagal Membatalkan Program', $ip);
        }

        return $output;
    }

    public static function getListDailyOperasional($data)
    {
        $start_date     = $data['start_date'];
        $end_date       = $data['end_date'];
        $program        = $data['program'];
        $sub_division   = $data['sub_divisi'];

        $query  = DB::select(
            "
            SELECT 	pkb.*
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
            WHERE   pkb.pkb_start_date BETWEEN '$start_date' AND '$end_date'
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
}