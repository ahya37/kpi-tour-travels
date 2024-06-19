<?php 

namespace App\Services;
use App\Helpers\ResponseFormatter;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Hash;

date_default_timezone_set('Asia/Jakarta');

class ProgramKerjaService
{
    public static function getDataProkerTahunan($data)
    {
        $uid                = $data['uid'];
        $roleName           = $data['roleName'];
        $query  = DB::select(
            "
            SELECT 	pt.uid as uid,
                    pt.pkt_title as title,
                    gd.name as division_group_name,
                    pt.pkt_description as description,
                    pt.pkt_year as periode,
                    pt.created_at,
                    count(*) as total_program
            FROM 	proker_tahunan pt
            JOIN 	proker_tahunan_detail ptd ON pt.id = ptd.pkt_id
            JOIN 	group_divisions gd ON pt.division_group_id = gd.id
            JOIN 	roles r ON gd.roles_id = r.id
            WHERE 	pt.uid LIKE '$uid'
            AND 	r.name LIKE '$roleName'
            GROUP BY uid, pkt_title, pkt_description, pkt_year, gd.name, created_at
            ORDER BY pt.created_at DESC
            "
        );

        return $query;
    }

    public static function doSimpanProkerTahunan($data, $jenis, $ip)
    {
        DB::beginTransaction();

        if($jenis == 'add') {
            // SIMPAN DULU HEADER
            $data_header    = array(
                "uid"               => Str::uuid(),
                "pkt_title"         => $data['prtTitle'],
                "pkt_description"   => $data['prtDescription'],
                "pkt_year"          => $data['prtPeriode'],
                "pkt_pic_job_employee_id"   => $data['prtPICEmployeeID'],
                "division_group_id"         => $data['prtGroupDivisionID'],
                "created_by"        => Auth::user()->id,
                "updated_by"        => Auth::user()->id,
                "created_at"        => date('Y-m-d H:i:s'),
                "updated_at"        => date('Y-m-d H:i:s'),
            );
            $ProkerHeader   = DB::table('proker_tahunan')->insert($data_header);

            $idHeader   = DB::getPDO()->lastInsertId();
            for($i = 0; $i < count($data['prtSub']); $i++)
            {
                $data_sub   = array(
                    "pkt_id"        => $idHeader,
                    "pktd_seq"      => $i + 1,
                    "pktd_title"    => $data['prtSub'][$i]['subProkTitle'],
                );

                DB::table('proker_tahunan_detail')->insert($data_sub);
            }
        } else if($jenis == 'edit') {
            // UPDATE DULU HEADER
            $header_where   = array(
                "uid"       => $data['prtID'],
            );

            $header_update  = array(
                "pkt_title"                 => $data['prtTitle'],
                "pkt_description"           => $data['prtDescription'],
                "pkt_pic_job_employee_id"   => $data['prtPICEmployeeID'],
                "division_group_id"         => $data['prtGroupDivisionID'],
                "updated_by"                => Auth::user()->id,
                "updated_at"                => date('Y-m-d H:i:s'),
            );

            DB::table('proker_tahunan')->where($header_where)->update($header_update);

            // GET ID
            $query_get_data_id   = DB::table('proker_tahunan')->where('uid', $data['prtID'])->get()[0]->id;
            
            // DELETE DETAIL
            DB::table('proker_tahunan_detail')
                ->where('pkt_id', $query_get_data_id)
                ->delete();

            // REINSERT DETAIL
            // DIGUNAKAN KETIKA DATA PADA ARRAY TERDAPAT VALUE YANG NULL
            $dataDetail     = [];
            for($i = 0; $i < count($data['prtSub']);$i++) {
                if(!empty($data['prtSub'][$i]['subProkTitle'])) {
                    $dataDetail[]   = array(
                        "subProkSeq"    => $i + 1,
                        "subProkTitle"  => $data['prtSub'][$i]['subProkTitle'],
                    );
                }
            }

            for($j = 0; $j < count($dataDetail); $j++) {
                // SIMPAN KE TABLE DETAIL
                $data_detail    = array(
                    "pkt_id"        => $query_get_data_id,
                    "pktd_seq"      => $dataDetail[$j]['subProkSeq'],
                    "pktd_title"    => $dataDetail[$j]['subProkTitle'],
                );

                DB::table('proker_tahunan_detail')->insert($data_detail);
            }
        }

        try {
            DB::commit();
            $output     = array(
                'transStatus'   => 'berhasil',
                'errMsg'        => '',
            );
            if($jenis == 'add') {
                $message    = "Berhasil Menambahkan Program Kerja Tahunan";
            } else {
                $message    = "Berhasil Mengubah Program Kerja Tahunan";
            }
            LogHelper::create($jenis, $message, $ip);
        } catch(\Exception $e) {
            DB::rollback();
            Log::channel('daily')->error($e->getMessage());
            $output     = array(
                'transStatus'   => 'gagal',
                'errMsg'        => $e->getMessage(),
            );
            LogHelper::create("error_system", $e->getMessage(), $ip);
        }

        return $output;
    }

    public static function getDataProkerTahunanDetail($id)
    {
        $get_header     = DB::select(
            "
            SELECT 	pt.*
            FROM 	proker_tahunan pt
            WHERE 	pt.uid = '".$id."'
            "
        );

        $proker_tahunan_id  = !empty($get_header) ? $get_header[0]->id : "";
        // QUERY PENGAMBILAN DATA BARU
        $get_detail     = DB::select(
            "
            SELECT 	b.pktd_seq as detail_seq,
                    b.pktd_title as detail_title
            FROM 	proker_tahunan a
            JOIN proker_tahunan_detail b ON a.id = b.pkt_id
            WHERE 	a.uid = '$id'
            ORDER BY CAST(b.pktd_seq AS SIGNED) ASC
            "
        );

        $output     = array(
            "header"    => !empty($get_header) ? $get_header : null,
            "detail"    => !empty($get_detail) ? $get_detail : [],
        );
        return $output;
    }

    // BULANAN
    public static function getProkerBulananAll($cari)
    {
        $uuid       = $cari['uuid'];
        $roleName   = $cari['role_name'] == 'admin' ? '%' : $cari['role_name'];
        $tgl_awal   = $cari['tgl_awal'];
        $tgl_akhir  = $cari['tgl_akhir'];
        $jadwal     = $cari['jadwal'];

        $query_list  = DB::select(
            "
            SELECT 	a.uuid as pkb_uuid,
                    a.pkb_title,
                    a.pkb_description,
                    a.pkb_start_date,
                    a.pkb_end_date,
                    SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) as pkb_pkt_id,
                    SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', -1) as pkb_pkt_id_seq,
                    d.id as pkb_gd_id,
                    d.name as pkb_gd_name,
                    d.roles_id as role_id,
                    f.name as role_name,
                    e.id as pkb_sd_id,
                    e.name as pkb_sd_name,
                    a.pkb_employee_id
            FROM 	proker_bulanan a
            JOIN 	proker_tahunan b ON SUBSTRING_INDEX(a.pkb_pkt_id,' | ',1) = b.uid
            JOIN 	job_employees c ON b.pkt_pic_job_employee_id = c.employee_id
            JOIN 	group_divisions d ON c.group_division_id = d.id
            JOIN 	sub_divisions e ON c.sub_division_id = e.id
            JOIN 	roles f ON d.roles_id = f.id
            WHERE 	a.uuid LIKE '$uuid'
            AND 	f.name LIKE '$roleName'
            AND     a.pkb_start_date BETWEEN '$tgl_awal' AND '$tgl_akhir'
            AND     a.pkb_title LIKE '$jadwal%'
            ORDER BY a.pkb_start_date, a.created_at ASC
            "
        );

        if($cari['uuid'] != '%') {
            $query_header   = DB::select(
                "
                SELECT 	a.uuid as pkb_uuid,
                        a.pkb_title,
                        a.pkb_description,
                        a.pkb_start_date,
                        SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) as pkb_pkt_id,
                        SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', -1) as pkb_pkt_id_seq,
                        d.id as pkb_gd_id,
                        d.name as pkb_gd_name,
                        d.roles_id as role_id,
                        f.name as role_name,
                        e.id as pkb_sd_id,
                        e.name as pkb_sd_name,
                        a.pkb_employee_id,        
                        a.pkb_start_date,
                        a.pkb_end_date,
                        a.pkb_start_time,
                        a.pkb_end_time
                FROM 	proker_bulanan a
                JOIN 	proker_tahunan b ON SUBSTRING_INDEX(a.pkb_pkt_id,' | ',1) = b.uid
                JOIN 	job_employees c ON b.pkt_pic_job_employee_id = c.employee_id
                JOIN 	group_divisions d ON c.group_division_id = d.id
                JOIN 	sub_divisions e ON c.sub_division_id = e.id
                JOIN 	roles f ON d.roles_id = f.id
                WHERE 	a.uuid LIKE '$uuid'
                AND 	f.name LIKE '$roleName'
                ORDER BY a.pkb_start_date, a.created_at ASC
                "
            );

            $query_detail   = DB::select(
                "
                SELECT 	b.id as detail_id,
                        b.pkbd_type as jenis_pekerjaan,
                        b.pkbd_target as target_sasaran,
                        b.pkbd_result as hasil,
                        b.pkbd_evaluation as evaluasi,
                        b.pkbd_description as keterangan
                FROM 	proker_bulanan a
                JOIN 	proker_bulanan_detail b ON a.id = b.pkb_id
                WHERE 	a.uuid = '$uuid'
                ORDER BY a.created_at, b.id ASC
                "
            );
        } else {
            $query_detail   = null;
            $query_header   = null;
        }
        
        $output     = array(
            "list"      => $query_list,
            "header"    => $query_header,
            "detail"    => $query_detail,
        );
        
        return $output;
    }

    public static function doGetProkerTahunan($data)
    {
        $roleName   = Auth::user()->getRoleNames()[0] == 'admin' ? '%' : Auth::user()->getRoleNames()[0];
        $prokerID   = $data['prokerID'];
        $query      = DB::select(
            "
            SELECT 	a.uid as pkt_uid,
                    a.pkt_title as pkt_title,
                    b.group_division_id,
                    c.name as group_division_name,
                    b.sub_division_id,
                    d.name as sub_division_name,
                    a.created_at as created_date
            FROM 	proker_tahunan a
            JOIN 	job_employees b ON a.pkt_pic_job_employee_id = b.employee_id
            JOIN 	group_divisions c ON b.group_division_id = c.id
            JOIN 	sub_divisions d ON b.sub_division_id = d.id
            JOIN    roles e ON c.roles_id = e.id
            WHERE 	a.parent_id IS NULL
            AND 	a.uid LIKE '$prokerID'
            AND     e.name LIKE '$roleName'
            ORDER BY a.created_at DESC
            "
        );

        return $query;
    }

    public static function getDataPIC($groupDivisionID)
    {
        $query  = DB::select(
            "
            SELECT 	a.employee_id,
                    b.name as employee_name
            FROM 	job_employees a
            INNER JOIN employees b ON a.employee_id = b.id
            WHERE 	group_division_id = '$groupDivisionID'
            ORDER BY b.name ASC
            "
        );

        return $query;
    }
    
    public static function doSimpanProkerBulanan($dataProkerBulanan)
    {
        $dataProkerBulananInput     = $dataProkerBulanan->all()['sendData'];
        // print("<pre>".print_r($dataProkerBulananInput, true)."</pre>");die();
        DB::beginTransaction();
        
        if($dataProkerBulananInput['prokerBulanan_typeTrans'] == 'add') {
            // HEADER
            $data_insert    = array(
                "uuid"                  => Str::uuid(),
                "pkb_title"             => $dataProkerBulananInput['prokerBulanan_title'],
                "pkb_start_date"        => date('Y-m-d', strtotime($dataProkerBulananInput['prokerBulanan_startDate'])),
                "pkb_end_date"          => date('Y-m-d', strtotime($dataProkerBulananInput['prokerBulanan_endDate'])),
                "pkb_description"       => $dataProkerBulananInput['prokerBulanan_description'],
                "pkb_pkt_id"            => !empty($dataProkerBulananInput['prokerBulanan_subProkerTahunan']) ? $dataProkerBulananInput['prokerBulanan_prokerTahunanID']." | ".$dataProkerBulananInput['prokerBulanan_subProkerTahunan'] : $dataProkerBulananInput['prokerBulanan_prokerTahunanID'],
                "pkb_employee_id"       => $dataProkerBulananInput['prokerBulanan_employeeID'],
                "created_by"            => Auth::user()->id,
                "updated_by"            => Auth::user()->id,
                "created_at"            => date('Y-m-d H:i:s'),
                "updated_at"            => date('Y-m-d H:i:s'),
                "pkb_start_time"        => date('Y-m-d', strtotime($dataProkerBulananInput['prokerBulanan_startDate']))." ".$dataProkerBulananInput['prokerBulanan_startActivity'],
                "pkb_end_time"          => date('Y-m-d', strtotime($dataProkerBulananInput['prokerBulanan_startDate']))." ".$dataProkerBulananInput['prokerBulanan_endActivity'],
            );
            DB::table('proker_bulanan')
                ->insert($data_insert);

            $idHeader   = DB::getPdo()->lastInsertId();
            // INSERT DETAIL
            if(count($dataProkerBulananInput['prokerBulanan_detail']) > 0) {
                for($i = 0; $i < count($dataProkerBulananInput['prokerBulanan_detail']); $i++) {
                    $data_insert_detail     = array(
                        "pkb_id"            => $idHeader,
                        "pkbd_type"         => $dataProkerBulananInput['prokerBulanan_detail'][$i]['jenisPekerjaan'],
                        "pkbd_target"       => $dataProkerBulananInput['prokerBulanan_detail'][$i]['targetSasaran'],
                        "pkbd_result"       => $dataProkerBulananInput['prokerBulanan_detail'][$i]['hasil'],
                        "pkbd_evaluation"   => $dataProkerBulananInput['prokerBulanan_detail'][$i]['evaluasi'],
                        "pkbd_description"  => $dataProkerBulananInput['prokerBulanan_detail'][$i]['keterangan'],
                    );
                    
                    if(!empty($data_insert_detail['pkbd_type'])) {
                        DB::table('proker_bulanan_detail')->insert($data_insert_detail);
                    }
                }
            }

            // INSERT FILE
            if(!empty($dataProkerBulananInput['prokerBulanan_file_list'])) {
                for($j = 0; $j < count($dataProkerBulananInput['prokerBulanan_file_list']); $j++) {
                    $data_insert_file   = array(
                        "pkb_id"    => $idHeader,
                        "pkbf_seq"  => $j + 1,
                        "pkbf_name" => $dataProkerBulananInput['prokerBulanan_file_list'][$j]['originalName'],
                        "pkbf_path" => $dataProkerBulananInput['prokerBulanan_file_list'][$j]['path'],

                    );
                    DB::table('proker_bulanan_file')->insert($data_insert_file);
                }
            }
        } else if($dataProkerBulananInput['prokerBulanan_typeTrans'] == 'edit') {
            // print("<pre>" .print_r($dataProkerBulananInput, true). "</pre>");die();
            $data_header_where  = array(
                "uuid"      => $dataProkerBulananInput['prokerBulanan_ID'],
            );

            $data_header_update = array(
                "pkb_title"             => $dataProkerBulananInput['prokerBulanan_title'],
                "pkb_start_date"        => date('Y-m-d', strtotime($dataProkerBulananInput['prokerBulanan_startDate'])),
                "pkb_end_date"          => date('Y-m-d', strtotime($dataProkerBulananInput['prokerBulanan_endDate'])),
                "pkb_description"       => $dataProkerBulananInput['prokerBulanan_description'],
                "pkb_pkt_id"            => $dataProkerBulananInput['prokerBulanan_prokerTahunanID']." | ".$dataProkerBulananInput['prokerBulanan_subProkerTahunan'],
                "pkb_employee_id"       => $dataProkerBulananInput['prokerBulanan_employeeID'],
                "pkb_start_time"        => date('Y-m-d', strtotime($dataProkerBulananInput['prokerBulanan_startDate']))." ".$dataProkerBulananInput['prokerBulanan_startActivity'],
                "pkb_end_time"          => date('Y-m-d', strtotime($dataProkerBulananInput['prokerBulanan_startDate']))." ".$dataProkerBulananInput['prokerBulanan_endActivity'],
                "updated_by"            => Auth::user()->id,
                "updated_at"            => date('Y-m-d H:i:s'),
            );

            // UPDATE HEADER
            DB::table('proker_bulanan')
                ->where($data_header_where)
                ->update($data_header_update);
            
            // UPDATE DETAIL
            if(count($dataProkerBulananInput['prokerBulanan_detail']) > 0) {
                if(!empty($dataProkerBulananInput['prokerBulanan_detail'][0]['jenisPekerjaan'])) {
                    // DELETE DATA
                    $getIDProkerBulanan     = DB::table('proker_bulanan')
                                                    ->select('id')
                                                    ->where('uuid','=', $dataProkerBulananInput['prokerBulanan_ID'])
                                                    ->get();
                    
                    // CHECK JIKA ID GETIDPROKERBULANAN SUDAH ADA DI DETAIL
                    $check_detail   = DB::table('proker_bulanan_detail')
                                        ->where('pkb_id', '=', $getIDProkerBulanan[0]->id)
                                        ->get();
                    if(!empty($check_detail)) {
                        DB::table('proker_bulanan_detail')
                            ->where('pkb_id','=', $getIDProkerBulanan[0]->id)
                            ->delete();
                    }

                    for($i = 0; $i < count($dataProkerBulananInput['prokerBulanan_detail']); $i++) {
                        $data_insert_detail     = array(
                            "pkb_id"            => $getIDProkerBulanan[0]->id,
                            "pkbd_type"         => $dataProkerBulananInput['prokerBulanan_detail'][$i]['jenisPekerjaan'],
                            "pkbd_target"       => $dataProkerBulananInput['prokerBulanan_detail'][$i]['targetSasaran'],
                            "pkbd_result"       => $dataProkerBulananInput['prokerBulanan_detail'][$i]['hasil'],
                            "pkbd_evaluation"   => $dataProkerBulananInput['prokerBulanan_detail'][$i]['evaluasi'],
                            "pkbd_description"  => $dataProkerBulananInput['prokerBulanan_detail'][$i]['keterangan'],
                        );
    
                        if(!empty($data_insert_detail['pkbd_type'])) {
                            DB::table('proker_bulanan_detail')
                            ->insert($data_insert_detail);
                        }
                    }
                }
            }
        }

        try {
            DB::commit();
            $output     = array(
                "status"    => "berhasil",
                "errMsg"    => null
            );
            if($dataProkerBulananInput['prokerBulanan_typeTrans'] == 'add') {
                $message    = "Berhasil Menambahkan Program Kerja Bulanan";
            } else {
                $message    = "Berhasil Merubah Program Kerja Bulanan";
            }
            LogHelper::create($dataProkerBulananInput['prokerBulanan_typeTrans'], $message, $dataProkerBulanan->ip());
        } catch(\Exception $e) {
            DB::rollback();
            $output     = array(
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            );
            Log::channel('daily')->error($e->getMessage());
            LogHelper::create("error_message", $e->getMessage(), $dataProkerBulanan->ip());
        }

        return $output;
    }

    public static function doGetListDataHarian($request)
    {
        $pkb_id     = $request->all()['sendData']['pkb_id'];
        $pkbd_id    = $request->all()['sendData']['pkbd_id'];

        $pkb_header = DB::select(
            "
            SELECT 	a.uuid as pkh_id,
                    a.pkh_title,
                    a.pkh_date,
                    SUBSTRING_INDEX(a.pkh_start_time,' ',-1) as pkh_start_time,
                    SUBSTRING_INDEX(a.pkh_end_time,' ', -1) as pkh_end_time,
                    b.name as pkh_create_by
            FROM 	proker_harian a
            JOIN 	users b ON a.created_by = b.id
            WHERE 	SUBSTRING_INDEX(a.pkh_pkb_id, ' | ', 1) = '$pkb_id'
            AND 	SUBSTRING_INDEX(a.pkh_pkb_id, ' | ', -1) = '$pkbd_id'
            ORDER BY a.created_at DESC
            "
        );

        $pkb_detail     = DB::select(
            "
            SELECT 	a.uuid as file_header_id,
                    b.pkhf_seq as file_seq,
                    b.pkhf_name as file_name,
                    b.pkhf_path as file_path
            FROM 	proker_harian a
            JOIN 	proker_harian_file b ON a.id = b.pkh_id
            WHERE 	SUBSTRING_INDEX(a.pkh_pkb_id, ' | ', 1) = '$pkb_id'
            AND 	SUBSTRING_INDEX(a.pkh_pkb_id, ' | ', -1) = '$pkbd_id'
            GROUP BY a.uuid, b.pkhf_seq, b.pkhf_name, b.pkhf_path
            ORDER BY b.pkhf_seq ASC
            "
        );
        
        $output     = array(
            "header"    => $pkb_header,
            "file"      => $pkb_detail
        );

        return $output;
    }

    public static function listProkerTahunan()
    {
        $query  = DB::select(
            "
            SELECT 	b.pktd_seq,
				    b.pktd_title
            FROM 	proker_tahunan a
            JOIN    proker_tahunan_detail b ON a.id = b.pkt_id
            JOIN 	group_divisions c ON a.division_group_id = c.id
            JOIN 	roles d ON c.roles_id = d.id
            WHERE 	d.name LIKE 'operasional'
            ORDER BY CAST(pktd_seq AS SIGNED) ASC
            "
        );

        return $query;
    }

    public static function getCellProkerBulanan($data)
    {
        $get_bulan  = date('m', strtotime($data['tgl_awal']));
        $get_tahun  = date('Y', strtotime($data['tgl_awal']));
        $query  = DB::select(
            "
            SELECT 	SUBSTRING_INDEX(pkb_pkt_id,' | ',-1) as data_ke,
                    pkb_title as title,
                    pkb_start_date
            FROM 	proker_bulanan
            WHERE 	pkb_start_time is not null
            AND 	EXTRACT(YEAR FROM pkb_start_date) = '$get_tahun'
            AND 	EXTRACT(MONTH FROM pkb_start_date) = '$get_bulan'
            ORDER BY pkb_pkt_id, pkb_start_date ASC
            "
        );

        return $query;
    }

    public static function getListSelectJadwalUmrah()
    {
        $query  = DB::select(
            "
            SELECT 	c.name,
                    d.jdw_arrival_date,
                    d.jdw_depature_date
            FROM 	(
                    SELECT 	LOWER(REPLACE(REPLACE(SUBSTRING_INDEX(pkb_title, ' ', 1),'[', ''),']','')) AS program
                    FROM 	proker_bulanan
                    WHERE 	pkb_end_date IS NOT NULL
                    GROUP BY program
            ) AS b
            JOIN 	programs c ON b.program = LOWER(c.name)
            JOIN 	programs_jadwal d ON c.id = d.jdw_programs_id
            ORDER BY d.jdw_arrival_date ASC
            "
        );

        return $query;
    }

    // HARIAN
    public static function listProkerHarian($data)
    {
        $rolesName  = Auth::user()->getRoleNames()[0] == 'admin' ? '%' : Auth::user()->getRoleNames()[0];
        $currentDate    = date('Y-m-d');
        $getMonth       = date('m', strtotime($currentDate));
        $query  = DB::select(
            "
            SELECT 	a.uuid as pkh_id,
                    a.pkh_title,
                    a.pkh_date,
                    d.id as group_division_id,
                    d.name as group_division
            FROM 	proker_harian a
            JOIN 	proker_bulanan b ON SUBSTRING_INDEX(a.pkh_pkb_id, ' | ', 1) = b.uuid
            JOIN 	proker_tahunan c ON SUBSTRING_INDEX(b.pkb_pkt_id, ' | ', 1) = c.uid
            JOIN 	group_divisions d ON c.division_group_id = d.id
            JOIN 	roles e ON d.roles_id = e.id
            WHERE 	e.name LIKE '$rolesName'
            AND 	EXTRACT(MONTH FROM a.pkh_date) = '$getMonth'
            ORDER BY a.id ASC
            "
        );
        
        return $query;
    }

    public static function getProkerBulanan($data)
    {
        $currentDate    = $data['currentDate'];
        $roles          = $data['rolesName'];
        $pkb_uuid       = $data['pkb_uuid'];

        $query          = DB::select(
            "
            SELECT 	a.uuid as pkb_uuid,
                    b.id as pkbd_id,
                    a.pkb_title,
                    a.pkb_start_date as pkb_date,
                    b.pkbd_type as pkb_type_detail,
                    e.model_id as role_id,
                    f.name as role_name
            FROM 	proker_bulanan a
            JOIN  	proker_bulanan_detail b ON a.id = b.pkb_id
            JOIN  	proker_tahunan c ON SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) = c.uid
            JOIN 	employees d ON d.id = c.pkt_pic_job_employee_id
            JOIN 	model_has_roles e ON d.user_id = e.model_id
            JOIN 	roles f ON e.role_id = f.id
            WHERE 	EXTRACT(MONTH FROM a.pkb_start_date) = EXTRACT(MONTH FROM '$currentDate')
            AND 	EXTRACT(YEAR FROM a.pkb_start_date) = EXTRACT(YEAR FROM '$currentDate')
            AND 	(e.model_id LIKE '$roles' OR f.name LIKE '$roles')
            AND     a.uuid LIKE '$pkb_uuid'
            ORDER BY a.pkb_start_date, a.created_at ASC
            "
        );

        return $query;
    }

    public static function getProkerHarianDetail($uuid)
    {
        $query_header   = DB::select(
            "
            SELECT 	a.uuid as pkh_id,
                    a.pkh_title,
                    a.pkh_date,
                    SUBSTRING_INDEX(a.pkh_start_time,' ', -1) as pkh_start_time,
                    SUBSTRING_INDEX(a.pkh_end_time,' ', -1) as pkh_end_time,
                    SUBSTRING_INDEX(a.pkh_pkb_id,' ', 1) as pkb_id,
                    SUBSTRING_INDEX(a.pkh_pkb_id,' ', -1) as pkbd_id
            FROM 	proker_harian a
            WHERE 	a.uuid = '$uuid'
            "
        );

        $query_detail   = DB::select(
            "            
            SELECT 	a.uuid as pkh_id,
                    b.pkhf_seq,
                    b.pkhf_name,
                    b.pkhf_path
            FROM 	proker_harian a
            JOIN    proker_harian_file b ON a.id = b.pkh_id
            WHERE 	a.uuid = '$uuid'
            ORDER BY CAST(b.pkhf_seq as signed) ASC
            "
        );

        $header     = !empty($query_header) ? $query_header : [];
        $detail     = !empty($query_detail) ? $query_detail : [];

        $output     = array(
            "header"    => $header,
            "detail"    => $detail,
        );

        return $output;
    }
    
    public static function simpanDataHarian($data, $ip)
    {
        DB::beginTransaction();
        if($data['programKerjaHarian_jenisTrans'] == 'add') {
            $dataSimpan_header  = array(
                "uuid"              => Str::random(30),
                "pkh_title"         => $data['programKerjaHarian_description'],
                "pkh_date"          => $data['programKerjaHarian_startDate'],
                "pkh_start_time"    => $data['programKerjaHarian_startDate']." ".$data['programKerjaHarian_startTime'],
                "pkh_end_time"      => $data['programKerjaHarian_startDate']." ".$data['programKerjaHarian_endTime'],
                "pkh_pkb_id"        => $data['programKerjaHarian_pkbID']." | ".$data['programKerjaHarian_pkbSeq'],
                "created_by"        => Auth::user()->id,
                "updated_by"        => Auth::user()->id,
                "created_at"        => date('Y-m-d H:i:s'),
                "updated_at"        => date('Y-m-d H:i:s'),
            );
            DB::table('proker_harian')->insert($dataSimpan_header);

            $getIDHeader = DB::getPdo()->lastInsertId();
            
            if(!empty($data['programKerjaHarian_file'])) {
                for($i = 0; $i < count($data['programKerjaHarian_file']); $i++) {
                    $dataSimpan_file    = array(
                        "pkh_id"        => $getIDHeader,
                        "pkhf_seq"      => $i + 1,
                        "pkhf_name"     => str_replace(" ","_", $data['programKerjaHarian_file'][$i]['originalName']),
                        "pkhf_path"     => str_replace(" ","_", $data['programKerjaHarian_file'][$i]['path']),
                    );

                    DB::table('proker_harian_file')->insert($dataSimpan_file);
                }
            }
        } else if($data['programKerjaHarian_jenisTrans'] == 'edit') {
            $data_where     = array(
                "uuid"      => $data['programKerjaHarian_ID'],
            );

            $data_update    = array(
                "pkh_title"         => $data['programKerjaHarian_description'],
                "pkh_date"          => $data['programKerjaHarian_startDate'],
                "pkh_start_time"    => $data['programKerjaHarian_startDate']." ".$data['programKerjaHarian_startTime'],
                "pkh_end_time"      => $data['programKerjaHarian_startDate']." ".$data['programKerjaHarian_endTime'],
                "pkh_pkb_id"        => $data['programKerjaHarian_pkbID']." | ".$data['programKerjaHarian_pkbSeq'],
                "updated_by"        => Auth::user()->id,
                "updated_at"        => date('Y-m-d H:i:s'),
            );

            DB::table('proker_harian')->where($data_where)->update($data_update);
        }
        
        try {
            DB::commit();
            $output     = array(
                "status"    => "berhasil",
                "errMsg"    => null
            );
            if($data['programKerjaHarian_jenisTrans'] == 'add') {
                LogHelper::create("add", "Berhasil Menambahkan Program Kerja Harian", $ip);
            } else if($data['programKerjaHarian_jenisTrans'] == 'edit') {
                LogHelper::create("edit", "Berhasil Mengubah Program Kerja Harian id : ".$data['programKerjaHarian_ID'], $ip);
            }
        } catch(\Exception $e) {
            DB::rollback();
            Log::channel('daily')->error($e->getMessage());
            $output     = array(
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            );
            LogHelper::create("error_system", $e->getMessage(), $ip);
        }

        return $output;
    }

    // 04 06 2024
    // NOTE : SUMMARY UNTUK DASHBOARD PROGRAM KERJA
    public static function doGetDataTotalProgramKerja()
    {
        $roleId     = Auth::user()->getRoleNames()[0]  == 'admin' ? '%' : Auth::user()->getRoleNames()[0];
        $query  = DB::select(
            "
            SELECT 	SUM(total_proker_tahunan) as grand_total_proker_tahunan,
                    SUM(total_proker_bulanan) as grand_total_proker_bulanan,
                    SUM(total_proker_harian) as grand_total_proker_harian,
                    tahun
            FROM 	(
                    SELECT 	COUNT(a.id) as total_proker_tahunan,
                            0 as total_proker_bulanan,
                            0 as total_proker_harian,
                            pkt_year as tahun,
                            c.name as role_name
                    FROM 	proker_tahunan a
                    JOIN	group_divisions b ON a.division_group_id = b.id
                    JOIN 	roles c ON b.roles_id = c.id
                    GROUP BY pkt_year, c.name

                    UNION

                    SELECT 	0 as total_proker_tahunan,
                            COUNT(a.id) as total_proker_bulanan,
                            0 as total_proker_harian,
                            EXTRACT(YEAR FROM a.pkb_start_date) as tahun,
                            c.name
                    FROM 	proker_bulanan a
                    JOIN 	proker_tahunan b ON SUBSTRING_INDEX(a.pkb_pkt_id,' | ',1) = b.uid
                    JOIN	group_divisions c ON b.division_group_id = c.id
                    JOIN 	roles d ON c.roles_id = d.id
                    GROUP BY EXTRACT(YEAR FROM a.pkb_start_date), c.name

                    UNION

                    SELECT 	0 as total_proker_tahunan,
                            0 as total_proker_bulanan,
                            count(a.uuid) as total_proker_harian,
                            EXTRACT(YEAR FROM a.pkh_date) as tahun,
                            e.name as role_name
                    FROM 	proker_harian a
                    JOIN 	proker_bulanan b ON SUBSTRING_INDEX(a.pkh_pkb_id,' | ',1) = b.uuid
                    JOIN 	proker_tahunan c ON SUBSTRING_INDEX(b.pkb_pkt_id,' | ',1) = c.uid
                    JOIN 	group_divisions d ON c.division_group_id = d.id
                    JOIN 	roles e ON d.roles_id = e.id
                    GROUP BY e.name, EXTRACT(YEAR FROM a.pkh_date)
                    
                    UNION
                    
                    SELECT 	0 as total_proker_tahunan,
                            0 as total_proker_bulanan,
                            count(a.uuid) as total_proker_harian,
                            EXTRACT(YEAR FROM a.pkb_start_date) as tahun,
                            d.name as role_name
                    FROM 	proker_bulanan a
                    JOIN 	proker_tahunan b ON SUBSTRING_INDEX(a.pkb_pkt_id,' | ',1) = b.uid
                    JOIN 	group_divisions c ON b.division_group_id = c.id
                    JOIN 	roles d ON c.roles_id = d.id
                    WHERE 	pkb_start_time IS NOT NULL
                    GROUP BY EXTRACT(YEAR FROM a.pkb_start_date), d.name
            ) AS b
            WHERE   b.role_name LIKE '$roleId'
            GROUP BY tahun
            "
        );

        return $query;
    }
}