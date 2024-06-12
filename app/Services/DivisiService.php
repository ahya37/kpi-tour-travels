<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\Log;
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
                    a.is_generated
            FROM 	programs_jadwal a
            JOIN 	programs B on a.jdw_programs_id = b.id
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
                    b.name as jdw_program_name
            FROM 	programs_jadwal a
            JOIN 	programs B on a.jdw_programs_id = b.id
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
                    UPPER(b.name) as program_name
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
                "jdw_program_name"  => $query_getDataProgram[0]->program_name
            ];
        } else {
            $dataProgram    = [];
        }

        $query_getDataRules     = DB::select(
            "
            SELECT 	a.rul_title,
                    a.rul_duration_day,
                    a.rul_sla,
                    LEFT(a.rul_sla, 1) as custom_sla_condition,
                    SUBSTRING_INDEX(SUBSTRING_INDEX(a.rul_sla,'-',-1),'+',-1) AS custom_sla,
                    a.rul_pkt_id
            FROM 	programs_jadwal_rules a
            ORDER BY a.id ASC
            "
        );

        if(!empty($query_getDataRules)) {
            for($i = 0; $i < count($query_getDataRules); $i++) {
                $dataRules  = $query_getDataRules[$i];
                if($dataRules->custom_sla_condition == '-') {
                    $dataSimpan         = [
                        "pkb_title"         => "[".$dataProgram['jdw_program_name']."] (".date('d/M/Y', strtotime($dataProgram['jdw_depature_date']))." s/d ".date('d/M/Y', strtotime($dataProgram['jdw_arrival_date'])).") ".$dataRules->rul_title,
                        "pkb_start_date"    => count($dataProgram) > 0 ? date('Y-m-d', strtotime(' -'.$dataRules->custom_sla.' days', strtotime($dataProgram['jdw_depature_date']))) : '1970-01-01',
                        "pkb_end_date"      => count($dataProgram) > 0 ? $dataProgram['jdw_depature_date'] : '1970-01-01',
                        "pkb_description"   => count($dataProgram) > 0 ? $dataProgram['jdw_description'] : '',
                        "pkb_pkt_id"        => $dataRules->rul_pkt_id,
                        "pkb_employee_id"   => '',
                        "created_by"        => Auth::user()->id,
                        "updated_by"        => Auth::user()->id,
                        "created_at"        => date('Y-m-d H:i:s'),
                        "updated_at"        => date('Y-m-d H:i:s'),
                    ];
                } else {
                    $dataSimpan         = [
                        "pkb_title"         => "[".$dataProgram['jdw_program_name']."] (".date('d/M/Y', strtotime($dataProgram['jdw_depature_date']))." s/d ".date('d/M/Y', strtotime($dataProgram['jdw_arrival_date'])).") ".$dataRules->rul_title,
                        "pkb_start_date"    => count($dataProgram) > 0 ? $dataProgram['jdw_arrival_date'] : '1970-01-01',
                        "pkb_end_date"      => count($dataProgram) > 0 ? date('Y-m-d', strtotime(' +'.$dataRules->custom_sla.' days', strtotime($dataProgram['jdw_arrival_date']))) : '1970-01-01',
                        "pkb_description"   => count($dataProgram) > 0 ? $dataProgram['jdw_description'] : '',
                        "pkb_pkt_id"        => $dataRules->rul_pkt_id,
                        "pkb_employee_id"   => '',
                        "created_by"        => Auth::user()->id,
                        "updated_by"        => Auth::user()->id,
                        "created_at"        => date('Y-m-d H:i:s'),
                        "updated_at"        => date('Y-m-d H:i:s'),
                    ];
                }
                DB::table('proker_bulanan')->insert($dataSimpan);
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
                    SUBSTRING_INDEX(SUBSTRING_INDEX(a.rul_sla,'+',-1), '-', -1) as rul_length_day
            FROM    programs_jadwal_rules a
            WHERE 	a.id = '$rulesID'
            "
        );

        return $query;
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
}