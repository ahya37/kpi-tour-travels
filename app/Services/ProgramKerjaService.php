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
                "uid"               => Str::random(30),
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
            // Log::channel('daily')->error($e->getMessage());
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
            WHERE 	pt.uid = '$id'
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
        $query_get_sub_division     = DB::table('employees AS a')
                                        ->select('c.name as sub_division_name')
                                        ->where('a.user_id', Auth::user()->id)
                                        ->join('job_employees AS b', 'b.employee_id', '=', 'a.id')
                                        ->join('sub_divisions AS c', 'c.id', '=', 'b.sub_division_id')
                                        ->get()->toArray();
        $current_sub_division   = !empty($query_get_sub_division) ? strtolower($query_get_sub_division[0]->sub_division_name) : '%';

        if($cari['group_divisi'] == '') {
            $current_user   = Auth::user()->id;
            $query_get_group_division   = DB::select(
                "
                    SELECT 	c.name as group_division_name
                    FROM 	employees a
                    JOIN 	job_employees b ON a.id = b.employee_id
                    JOIN 	group_divisions c ON b.group_division_id = c.id
                    WHERE 	a.user_id = '$current_user'
                "
            );
            
            $group_division     = !empty($query_get_group_division) ? $query_get_group_division[0]->group_division_name : null;
        } else {
            $group_division     = '%';
        }

        if($current_sub_division == 'pic' || $current_sub_division == 'manager')
        {
            $uuid           = $cari['uuid'];
            $roleName       = $cari['current_role'] == 'admin' ? '%' : $cari['current_role'];
            $tgl_awal       = $cari['tgl_awal'];
            $tgl_akhir      = $cari['tgl_akhir'];
            $jadwal         = $cari['jadwal'];
            $group_divisi   = !empty($cari['group_divisi']) ? $cari['group_divisi'] : $group_division;
            $sub_divisi     = '%';
            $user_id        = '%';
        } else {
            $uuid           = $cari['uuid'];
            $roleName       = $cari['current_role'] == 'admin' || $cari['current_role'] == 'umum' ? '%' : $cari['current_role'];
            $tgl_awal       = $cari['tgl_awal'];
            $tgl_akhir      = $cari['tgl_akhir'];
            $jadwal         = $cari['jadwal'];
            $group_divisi   = !empty($cari['group_divisi']) ? $cari['group_divisi'] : '%';
            $sub_divisi     = $current_sub_division;
            $user_id        = $cari['current_role'] == 'admin' || $cari['current_role'] == 'operasional' || $cari['current_role'] == 'umum' ? '%' : Auth::user()->id;
        }
        
        // FOR DEBUGGING
        // print("<pre>" . print_r($cari, true) . "</pre>");die();

        // var_dump([
        //     "uuid"          => $uuid, 
        //     "roleName"      => $roleName,
        //     "tgl_awal"      => $tgl_awal,
        //     "tgl_akhir"     => $tgl_akhir,
        //     "jadwal"        => $jadwal,
        //     "group_divisi"  => $group_divisi,
        //     "sub_divisi"    => $sub_divisi,
        //     "user_id"       => $user_id,
        //     "current_sub_division"  => $current_sub_division
        // ]);die();


        if($jadwal == '%') {
            $query_list     = DB::select(
                "
                SELECT 	DISTINCT pkb.pkb_uuid,
                        pkb.pkb_title,
                        pkb.pkb_description,
                        pkb.pkb_start_date,
                        pkb.pkb_end_date,
                        pkb.pkb_pkt_uuid,
                        pkb.pkb_pkt_seq,
                        pkb.pkb_created_date,
                        pkb.pkb_created_by,
                        pkb.group_division_name,
				        pkb.status_created,
                        pkb.status_active
                FROM 	(
                        SELECT 	a.uuid as pkb_uuid,
                                a.pkb_title,
                                a.pkb_description,
                                a.pkb_start_date,
                                a.pkb_end_date,
                                SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) as pkb_pkt_uuid,
                                SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', -1) as pkb_pkt_seq,
                                g.id as role_id,
                                g.name as role_name,
                                c.name as group_division_name,
                                f.name as sub_division_name,
                                a.created_at as pkb_created_date,
                                a.created_by as pkb_created_by,
								null as status_created,
                                a.pkb_is_active as status_active
                        FROM 	proker_bulanan a
                        JOIN 	proker_tahunan b ON SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) = b.uid
                        JOIN 	group_divisions c ON b.division_group_id = c.id
                        JOIN 	job_employees d ON d.group_division_id = c.id
                        JOIN 	employees e ON d.employee_id = e.id
                        JOIN 	sub_divisions f ON d.sub_division_id = f.id
                        JOIN 	roles g ON g.id = c.roles_id
                        WHERE 	a.pkb_title NOT LIKE '[%]%'
    
                        UNION ALL
    
                        SELECT 	e.uuid as pkb_uuid,
                                CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(e.pkb_title, ')', 1), '(', 1),'', UPPER(e.pkb_description)) as pkb_title,
                                e.pkb_description,
                                e.pkb_start_date,
                                e.pkb_end_date,
                                SUBSTRING_INDEX(e.pkb_pkt_id, ' | ', 1) as pkb_pkt_uuid,
                                SUBSTRING_INDEX(e.pkb_pkt_id, ' | ', -1) as pkb_pkt_seq,
                                g.id as role_id,
                                g.name as role_name,
                                f.name as group_division_name,
                                d.name as pkb_sd_name,
                                e.created_at,
                                e.created_by,
                                a.prog_pkb_is_created as status_created,
                                e.pkb_is_active
                        FROM 	tr_prog_jdw a
                        JOIN 	programs_jadwal b ON a.prog_jdw_id = b.jdw_uuid
                        JOIN 	programs_jadwal_rules c ON a.prog_rul_id = c.id
                        JOIN 	sub_divisions d ON d.id = c.rul_pic_sdid
                        JOIN 	proker_bulanan e ON a.prog_pkb_id = e.uuid
                        JOIN 	group_divisions f ON f.id = d.division_group_id
                        JOIN 	roles g ON f.roles_id = g.id
                        WHERE 	e.pkb_title LIKE '[%]%'
                        AND     b.jdw_uuid LIKE '$jadwal'
                        
                        UNION ALL
                        
                        SELECT 		a.uuid as pkb_uuid,
                                    a.pkb_title,
                                    a.pkb_description,
                                    a.pkb_start_date,
                                    a.pkb_end_date,
                                    SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) as pkb_pkt_uuid,
                                    SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', -1) as pkb_pkt_seq,
                                    d.id as role_id,
                                    d.name as role_name,
                                    c.name as group_division_name,
                                    e.name as sub_division_name,
                                    a.created_at,
                                    a.created_by,
								    null as status_created,
                                    a.pkb_is_active
                        FROM 		proker_bulanan a
                        JOIN 		proker_tahunan b ON SUBSTRING_INDEX(a.pkb_pkt_id,' | ', 1) = b.uid
                        JOIN 		group_divisions c ON c.id = b.division_group_id
                        JOIN 		roles d ON d.id = c.roles_id
                        JOIN 		sub_divisions e ON e.division_group_id = c.id
                        WHERE 		a.pkb_title NOT LIKE '[%]%'
                    ) AS pkb
                JOIN 	model_has_roles mhr ON mhr.model_id = pkb.pkb_created_by
                JOIN 	roles r ON mhr.role_id = r.id
                WHERE 	pkb.pkb_uuid LIKE '$uuid'
                AND 	pkb.pkb_start_date BETWEEN '$tgl_awal' AND '$tgl_akhir'
                AND 	r.name LIKE '$roleName'
                AND 	LOWER(pkb.sub_division_name) LIKE '$sub_divisi'
                AND     pkb.group_division_name LIKE '$group_divisi'
                AND     pkb.status_active = 't'
                ORDER BY pkb.pkb_created_date ASC
                "
            );
        } else {
            $query_list     = DB::select(
                "
                SELECT 	DISTINCT pkb.pkb_uuid,
                        pkb.pkb_title,
                        pkb.pkb_description,
                        pkb.pkb_start_date,
                        pkb.pkb_end_date,
                        pkb.pkb_pkt_uuid,
                        pkb.pkb_pkt_seq,
                        pkb.pkb_created_date,
                        pkb.pkb_created_by,
                        pkb.group_division_name,
                        pkb.status_created
                FROM 	(
                        SELECT 	e.uuid as pkb_uuid,
                                CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(e.pkb_title, ')', 1), '(', 1),'', UPPER(e.pkb_description)) as pkb_title,
                                e.pkb_description,
                                e.pkb_start_date,
                                e.pkb_end_date,
                                SUBSTRING_INDEX(e.pkb_pkt_id, ' | ', 1) as pkb_pkt_uuid,
                                SUBSTRING_INDEX(e.pkb_pkt_id, ' | ', -1) as pkb_pkt_seq,
                                g.id as role_id,
                                g.name as role_name,
                                f.name as group_division_name,
                                d.name as sub_division_name,
                                e.created_at as pkb_created_date,
                                e.created_by as pkb_created_by,
                                a.prog_pkb_is_created as status_created
                        FROM 	tr_prog_jdw a
                        JOIN 	programs_jadwal b ON a.prog_jdw_id = b.jdw_uuid
                        JOIN 	programs_jadwal_rules c ON a.prog_rul_id = c.id
                        JOIN 	sub_divisions d ON d.id = c.rul_pic_sdid
                        JOIN 	proker_bulanan e ON a.prog_pkb_id = e.uuid
                        JOIN 	group_divisions f ON f.id = d.division_group_id
                        JOIN 	roles g ON f.roles_id = g.id
                        WHERE 	e.pkb_title LIKE '[%]%'
                        AND     b.jdw_uuid = '$jadwal'
                    ) AS pkb
                JOIN 	model_has_roles mhr ON mhr.model_id = pkb.pkb_created_by
                JOIN 	roles r ON mhr.role_id = r.id
                WHERE 	pkb.pkb_uuid LIKE '$uuid'
                AND 	pkb.pkb_start_date BETWEEN '$tgl_awal' AND '$tgl_akhir'
                AND 	r.name LIKE '$roleName'
                AND 	LOWER(pkb.sub_division_name) LIKE '$sub_divisi'
                AND     pkb.group_division_name LIKE '$group_divisi'
                ORDER BY pkb.pkb_created_date ASC
                "
            );
        }

        if($cari['uuid'] != '%') {
            // var_dump($cari['uuid']);die();
            $query_header   = DB::select(
                "
                SELECT 	a.uuid as pkb_uuid,
                        a.pkb_title,
                        a.pkb_description,
                        a.pkb_start_date,
                        a.pkb_end_date,
                        a.pkb_start_time,
                        a.pkb_end_time,
                        SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) as pkb_pkt_id,
                        SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', -1) as pkb_pkt_id_seq,
                        a.pkb_employee_id,
                        b.division_group_id as pkb_gd_id,
                        (SELECT name FROM group_divisions WHERE id = b.division_group_id) as pkb_gd_name
                FROM 	proker_bulanan a
                JOIN 	proker_tahunan b ON SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) = b.uid
                JOIN	group_divisions c ON c.id = b.division_group_id
                JOIN 	roles d ON d.id = c.roles_id
                WHERE 	a.uuid LIKE '$uuid'
                AND 	d.name LIKE '$roleName'
                ORDER BY a.created_at DESC
                LIMIT 1
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
        $roleName   = (Auth::user()->hasRole('admin') || Auth::user()->hasRole('umum')) ? '%' : Auth::user()->getRoleNames()[0];
        $prokerID   = $data['prokerID'];
        $query      = DB::select(
            "
            SELECT 	a.uid as pkt_uid,
                    a.pkt_title,
                    b.id as group_division_id,
                    b.name as group_division_name,
                    d.id as role_id,
                    d.name as role_name,
                    a.created_at
            FROM 	proker_tahunan a
            JOIN 	group_divisions b ON a.division_group_id = b.id
            JOIN 	roles d ON b.roles_id = d.id
            WHERE 	a.parent_id IS NULL
            AND 	(d.id LIKE '$roleName' OR d.name LIKE '$roleName')
            AND 	a.uid LIKE '$prokerID'
            AND 	a.pkt_year = EXTRACT(YEAR FROM CURRENT_DATE)
            ORDER BY a.created_at ASC
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
                "uuid"                  => Str::random(30),
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
                "pkb_is_active"         => "t",
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

            // CHECK APAKAH SUDAH MASUK TR_PROG_JDW 
            if(!empty($dataProkerBulananInput['prokerBulanan_programJadwalID'])) {
                $check  = DB::table('tr_prog_jdw')
                            ->select('prog_pkb_id')
                            ->where(['prog_jdw_id' =>$dataProkerBulananInput['prokerBulanan_programJadwalID'], "prog_rul_id"=> $dataProkerBulananInput['prokerBulanan_programJadwalRulSeq'] ])
                            ->get();
                if( $check[0]->prog_pkb_id == "" ) {
                    // UPDATE TABLE TSB
                    DB::table('tr_prog_jdw')
                        ->where(['prog_jdw_id' =>$dataProkerBulananInput['prokerBulanan_programJadwalID'], "prog_rul_id"=> $dataProkerBulananInput['prokerBulanan_programJadwalRulSeq'] ])
                        ->update(['prog_pkb_id' => $data_insert['uuid']]);
                } else {
                    $data_insert     = array(
                        "prog_jdw_id"   => $dataProkerBulananInput['prokerBulanan_programJadwalID'],
                        "prog_rul_id"   => $dataProkerBulananInput['prokerBulanan_programJadwalRulSeq'],
                        "prog_pkb_id"   => $data_insert['uuid'],
                    );

                    DB::table('tr_prog_jdw')->insert($data_insert);
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
                "created_by"            => Auth::user()->id,
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

            // UPDATE TR-PROG-JDW STATUS
            $prokerBulananID    = $dataProkerBulananInput['prokerBulanan_ID'];
            $queryGetDataProgramJadwal  = DB::select(
                "
                SELECT  *
                FROM    tr_prog_jdw
                WHERE   prog_pkb_id = '$prokerBulananID'
                "
            );

            // var_dump($queryGetDataProgramJadwal);die();
            if(!empty($queryGetDataProgramJadwal)) {
                // UPDATE TR PROG JDW STATUS
                $prog_jdw_where     = array(
                    "prog_jdw_id"   => $queryGetDataProgramJadwal[0]->prog_jdw_id,
                    "prog_rul_id"   => $queryGetDataProgramJadwal[0]->prog_rul_id,
                    "prog_pkb_id"   => $queryGetDataProgramJadwal[0]->prog_pkb_id
                );

                $prog_update        = array(
                    "prog_pkb_is_created"    => "t",
                );
                
                DB::table('tr_prog_jdw')->where($prog_jdw_where)->update($prog_update);
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
            SELECT 	d.jdw_uuid,
                    c.name,
                    d.jdw_depature_date,
                    d.jdw_arrival_date
            FROM 	(
                    SELECT 	CASE
                                WHEN LOWER(REPLACE(REPLACE(SUBSTRING_INDEX(pkb_title, ' ', 1),'[', ''),']','')) = 'Haji' THEN 'Haji Khusus'
                                ELSE LOWER(REPLACE(REPLACE(SUBSTRING_INDEX(pkb_title, ' ', 1),'[', ''),']',''))
                            END AS program
                    FROM 	proker_bulanan
                    WHERE 	pkb_end_date IS NOT NULL
                    GROUP BY program
            ) AS b
            JOIN 	programs c ON b.program = LOWER(c.name)
            JOIN 	programs_jadwal d ON c.id = d.jdw_programs_id
            WHERE   d.is_generated = 't'
            ORDER BY d.jdw_arrival_date ASC
            "
        );

        return $query;
    }

    public static function doGetDataTableDashboad($data)
    {
        $groupDivision  = $data['sendData']['groupDivisi'];
        $createdBy      = $data['sendData']['createdBy'];
        $createdMonth   = $data['sendData']['createdMonth'];
        $query  = DB::select(
            "
            SELECT 	a.uuid,
                    a.pkb_title,
                    a.pkb_start_date,
                    a.pkb_end_date,
                    EXTRACT(MONTH FROM pkb_start_date) as pkb_bulan,
                    b.pkt_year as pkb_tahun,
                    c.id as group_division_id,
                    c.name as group_division_name,
                    a.created_by as created_by_id,
                    d.name as created_by_name,
                    a.pkb_is_active as status_active
            FROM 	proker_bulanan a
            JOIN 	proker_tahunan b ON SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) = b.uid
            JOIN 	group_divisions c ON b.division_group_id = c.id
            JOIN 	employees d ON d.user_id = a.created_by
            WHERE 	b.pkt_year = EXTRACT(YEAR FROM CURRENT_DATE)
            AND     EXTRACT(MONTH FROM a.pkb_start_date) = '$createdMonth'
            AND     c.id LIKE '$groupDivision'
            AND 	a.created_by LIKE '$createdBy'
            ORDER BY a.pkb_start_date DESC
            "
        );

        return !empty($query) ? $query : null;
    }

    // HARIAN
    public static function listProkerHarian($data)
    {
        $rolesName      = !empty($data['current_role']) ? $data['current_role'] : (Auth::user()->getRoleNames()[0] == 'admin' ? '%' : Auth::user()->getRoleNames()[0]);
        $currentDate    = date('Y-m-d');
        $getMonth       = !empty($data['current_month']) ? $data['current_month'] : date('m', strtotime($currentDate));
		$userId    		= Auth::user()->hasRole('admin') ? '%' : Auth::user()->id;
        $query  = DB::select(
            "
            SELECT 	*
            FROM 	(
                    SELECT 	a.uuid as pkh_id,
                            a.pkh_title,
                            a.pkh_date,
                            d.id as group_division_id,
                            d.name as group_division,
                            e.name as role_name,
                            a.created_by as user_id,
                            a.created_at as created_date,
                            a.pkh_is_active as status_active
                    FROM 	proker_harian a
                    JOIN 	proker_bulanan b ON SUBSTRING_INDEX(a.pkh_pkb_id, ' | ', 1) = b.uuid
                    JOIN 	proker_tahunan c ON SUBSTRING_INDEX(b.pkb_pkt_id, ' | ', 1) = c.uid
                    JOIN 	group_divisions d ON c.division_group_id = d.id
                    JOIN 	roles e ON d.roles_id = e.id

                    UNION ALL

                    SELECT 	DISTINCT a.uuid as pkh_id,
                            a.pkh_title,
                            a.pkh_date,
                            f.id as group_division_id,
                            f.name as group_divsion_name,
                            e.name as role_name,
                            a.created_by as user_id,
                            a.created_at as created_date,
                            a.pkh_is_active
                    FROM 	proker_harian a
                    JOIN 	proker_bulanan b ON SUBSTRING_INDEX(a.pkh_pkb_id, ' | ', 1) = b.uuid
                    JOIN 	proker_tahunan c ON SUBSTRING_INDEX(b.pkb_pkt_id, ' | ', 1) = c.uid
                    JOIN 	model_has_roles d ON d.model_id = a.created_by
                    JOIN 	roles e ON d.role_id = e.id
                    JOIN 	group_divisions f ON f.roles_id = e.id
                    WHERE 	e.name = 'umum'
				
				    UNION ALL

                    SELECT 	a.uuid as pkh_id,
                            a.pkh_title, 
                            a.pkh_date,
                            d.id as group_division_id,
                            d.name as group_division_name,
                            c.name as role_name,
                            a.created_by as user_id,
                            a.created_at as created_date,
                            a.pkh_is_active
                    FROM 	proker_harian a
                    JOIN 	model_has_roles b ON a.created_by = b.model_id
                    JOIN 	roles c ON b.role_id = c.id
                    JOIN 	group_divisions d ON b.role_id = d.roles_id
                    WHERE 	SUBSTRING_INDEX(a.pkh_pkb_id,' | ', -1) = 'Lainnya'
                    ) AS harian
            WHERE 	harian.role_name LIKE '$rolesName'
            AND 	harian.user_id LIKE '$userId'
            AND 	EXTRACT(MONTH FROM harian.pkh_date) = '$getMonth'
            AND     harian.status_active = 't'
            ORDER BY harian.created_date DESC
            "
        );
        
        return $query;
    }

    public static function getProkerBulanan($data)
    {
        $currentDate    = $data['currentDate'];
        $roles          = $data['rolesName'];
        $pkt_uuid       = $data['pkt_uuid'];
        $pkb_uuid       = $data['pkb_uuid'];
        $current_user   = $roles == '%' ? '%' : Auth::user()->id;


        // $query          = DB::select(
        //     "
        //     SELECT 	a.uuid as pkb_uuid,
        //             b.id as pkbd_id,
        //             a.pkb_title,
        //             a.pkb_start_date as pkb_date,
        //             b.pkbd_type as pkb_type_detail,
        //             e.model_id as role_id,
        //             f.name as role_name
        //     FROM 	proker_bulanan a
        //     JOIN  	proker_bulanan_detail b ON a.id = b.pkb_id
        //     JOIN  	proker_tahunan c ON SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) = c.uid
        //     JOIN 	employees d ON d.id = c.pkt_pic_job_employee_id
        //     JOIN 	model_has_roles e ON d.user_id = e.model_id
        //     JOIN 	roles f ON e.role_id = f.id
        //     WHERE 	EXTRACT(MONTH FROM a.pkb_start_date) = EXTRACT(MONTH FROM '$currentDate')
        //     AND 	EXTRACT(YEAR FROM a.pkb_start_date) = EXTRACT(YEAR FROM '$currentDate')
        //     AND 	(e.model_id LIKE '$roles' OR f.name LIKE '$roles')
        //     AND     c.id LIKE '$pkt_uuid'
        //     ORDER BY a.pkb_start_date, a.created_at ASC
        //     "
        // );

        $query  = DB::select(
            "
            SELECT 	a.uuid as pkb_uuid,
                    b.id as pkbd_id,
                    a.pkb_title, 
                    a.pkb_start_date as pkb_date, 
                    b.pkbd_type as pkb_type_detail,
                    e.id as role_id,
                    e.name as role_name
            FROM 	proker_bulanan a
            JOIN 	proker_bulanan_detail b ON a.id = b.pkb_id
            JOIN 	proker_tahunan c ON c.uid = SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1)
            JOIN 	group_divisions d ON d.id = c.division_group_id
            JOIN 	roles e ON e.id = d.roles_id
            WHERE 	EXTRACT(YEAR FROM a.pkb_start_date) = EXTRACT(YEAR FROM '$currentDate')
            AND 	c.uid LIKE '$pkt_uuid'
            AND     a.uuid LIKE '$pkb_uuid'
            AND 	(e.name LIKE '$roles' OR e.id LIKE '$roles')
            ORDER BY a.pkb_start_date, a.created_at ASC
            "
        );

        return $query;
    }

    public static function getProkerHarianDetail($uuid)
    {
        $query_header   = DB::select(
            "
            SELECT 	*
            FROM 	(
                    SELECT 	a.uuid as pkh_id,
                            a.pkh_title,
                            b.pkb_description,
                            a.pkh_date,
                            SUBSTRING_INDEX(a.pkh_start_time,' ', -1) as pkh_start_time,
                            SUBSTRING_INDEX(a.pkh_end_time,' ', -1) as pkh_end_time,
                            SUBSTRING_INDEX(a.pkh_pkb_id,' ', 1) as pkb_id,
                            SUBSTRING_INDEX(a.pkh_pkb_id,' ', -1) as pkbd_id,
                            c.uid as pkh_pkt_id,
                            b.pkb_employee_id as pkh_employee_id,
                            c.division_group_id as pkh_gd_id
                    FROM 	proker_harian a
                    JOIN 	proker_bulanan b ON b.uuid = SUBSTRING_INDEX(a.pkh_pkb_id, ' ', 1)
                    JOIN 	proker_tahunan c ON c.uid = SUBSTRING_INDEX(b.pkb_pkt_id, ' | ', 1)
                    WHERE 	a.pkh_pkb_id NOT LIKE '%Lainnya'

                    UNION

                    SELECT 	a.uuid as pkh_id,
                            a.pkh_title,
                            b.pkb_description,
                            a.pkh_date,
                            SUBSTRING_INDEX(a.pkh_start_time,' ', -1) as pkh_start_time,
                            SUBSTRING_INDEX(a.pkh_end_time,' ', -1) as pkh_end_time,
                            b.uuid as pkb_id,
                            SUBSTRING_INDEX(a.pkh_pkb_id,' ', -1) as pkbd_id,
                            SUBSTRING_INDEX(a.pkh_pkb_id,' ', 1) as pkh_pkt_id,
                            b.pkb_employee_id as pkh_employee_id,
                            c.division_group_id as pkh_gd_id
                    FROM 	proker_harian a
                    JOIN 	proker_bulanan b ON b.pkb_pkt_id = CONCAT(SUBSTRING_INDEX(a.pkh_pkb_id, ' | ', 1),' | ', a.uuid)
                    JOIN 	proker_tahunan c ON c.uid = SUBSTRING_INDEX(b.pkb_pkt_id,' | ', 1)
                    ) AS pkh
            WHERE 	pkh.pkh_id = '$uuid'
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
        $currentRole    = Auth::user()->getRoleNames()[0];
        if($data['programKerjaHarian_jenisTrans'] == 'add') {
            $dataSimpan_header  = array(
                "uuid"              => Str::random(30), 
                "pkh_title"         => $currentRole != 'umum' ? $data['programKerjaHarian_description'] : ($data['programKerjaHarian_pkbID'] == 'Lainnya' ? $data['programKerjaHarian_act_text'] : $data['programKerjaHarian_description']),
                "pkh_date"          => $data['programKerjaHarian_startDate'],
                "pkh_start_time"    => $data['programKerjaHarian_startDate']." ".$data['programKerjaHarian_startTime'],
                "pkh_end_time"      => $data['programKerjaHarian_startDate']." ".$data['programKerjaHarian_endTime'],
                "pkh_pkb_id"        => $currentRole != 'umum' ? $data['programKerjaHarian_pkbID']." | ".$data['programKerjaHarian_pkbSeq'] : ($data['programKerjaHarian_pkbID'] == 'Lainnya' ? $data['programKerjaHarian_pktID']." | ".$data['programKerjaHarian_pkbID'] : $data['programKerjaHarian_pkbID']." | ".$data['programKerjaHarian_pkbSeq']),
                "pkh_is_active"     => "t",
                "created_by"        => Auth::user()->id,
                "updated_by"        => Auth::user()->id,
                "created_at"        => date('Y-m-d H:i:s'),
                "updated_at"        => date('Y-m-d H:i:s'),
            );
            DB::table('proker_harian')->insert($dataSimpan_header);

            $getIDHeader = DB::getPdo()->lastInsertId();
            
            // CEK DAN INPUT KETIKA FILE ADA
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

            // CEK KETIKA CURRENT ROLE = UMUM MAKA INSERT KE PROKER BULANAN
            if($currentRole == 'umum' && $data['programKerjaHarian_pkbID'] == 'Lainnya' ) {
                // INSERT TO PROKER BULANAN
                $data_simpan_proker_bulanan  = [
                    "uuid"              => Str::uuid(),
                    "pkb_title"         => $data['programKerjaHarian_act_text'],
                    "pkb_start_date"    => $data['programKerjaHarian_startDate'],
                    "pkb_description"   => $data['programKerjaHarian_description'],
                    "pkb_pkt_id"        => $data['programKerjaHarian_pktID']. " | ".$dataSimpan_header['uuid'],
                    "pkb_employee_id"   => $data['programKerjaHarian_gd_picID'],
                    "pkb_start_time"    => $data['programKerjaHarian_startDate']." ".$data['programKerjaHarian_startTime'],
                    "pkb_end_time"      => $data['programKerjaHarian_startDate']." ".$data['programKerjaHarian_endTime'],
                    "created_by"        => Auth::user()->id,
                    "updated_by"        => Auth::user()->id,
                    "created_at"        => date('Y-m-d H:i:s'),
                    "updated_at"        => date('Y-m-d H:i:s'),
                ];

                DB::table('proker_bulanan')->insert($data_simpan_proker_bulanan);
            } else {
                // DO NOTHING
            }

        } else if($data['programKerjaHarian_jenisTrans'] == 'edit') {
            $data_where     = array(
                "uuid"      => $data['programKerjaHarian_ID'],
            );

            $data_update    = array(
                "pkh_title"         => $currentRole != 'umum' ? $data['programKerjaHarian_description'] : ($data['programKerjaHarian_pkbID'] == 'Lainnya' ? $data['programKerjaHarian_act_text'] : $data['programKerjaHarian_description']),
                "pkh_date"          => $data['programKerjaHarian_startDate'],
                "pkh_start_time"    => $data['programKerjaHarian_startDate']." ".$data['programKerjaHarian_startTime'],
                "pkh_end_time"      => $data['programKerjaHarian_startDate']." ".$data['programKerjaHarian_endTime'],
                "pkh_pkb_id"        => $currentRole != 'umum' ? $data['programKerjaHarian_pkbID']." | ".$data['programKerjaHarian_pkbSeq'] : ($data['programKerjaHarian_pkbID'] == 'Lainnya' ? $data['programKerjaHarian_pktID']." | ".$data['programKerjaHarian_pkbID'] : $data['programKerjaHarian_pkbID']." | ".$data['programKerjaHarian_pkbSeq']),
                "updated_by"        => Auth::user()->id,
                "updated_at"        => date('Y-m-d H:i:s'),
            );

            DB::table('proker_harian')->where($data_where)->update($data_update);

            if($currentRole == 'umum' && $data['programKerjaHarian_pkbID'] == 'Lainnya') {
                $data_where_bulanan     = array(
                    "uuid"      => $data['programKerjaHarian_pkbID_Lainnya'],
                );

                $data_update_bulanan    = array(
                    "pkb_title"         => $data['programKerjaHarian_act_text'],
                    "pkb_start_date"    => $data['programKerjaHarian_startDate'],
                    "pkb_description"   => $data['programKerjaHarian_description'],
                    "pkb_pkt_id"        => $data['programKerjaHarian_pktID']. " | ".$data_where['uuid'],
                    "pkb_employee_id"   => $data['programKerjaHarian_gd_picID'],
                    "pkb_start_time"    => $data['programKerjaHarian_startDate']." ".$data['programKerjaHarian_startTime'],
                    "pkb_end_time"      => $data['programKerjaHarian_startDate']." ".$data['programKerjaHarian_endTime'],
                    "updated_by"        => Auth::user()->id,
                    "updated_at"        => date('Y-m-d H:i:s')
                );

                DB::table('proker_bulanan')->where($data_where_bulanan)->update($data_update_bulanan);
            }
        }
        
        try {
            DB::commit();
            $output     = array(
                "status"    => "berhasil",
                "errMsg"    => null
            );
            if($data['programKerjaHarian_jenisTrans'] == 'add') {
                if($currentRole != 'umum') {
                    LogHelper::create("add", "Berhasil Menambahkan Program Kerja Harian ID : ".$getIDHeader, $ip);
                } else {
                    LogHelper::create("add", "Berhasil Menambahkan Proggam Kerja Harian Umum ID : ".$getIDHeader, $ip);
                }
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
        $userID         = Auth::user()->id;
        $roleName       = (Auth::user()->getRoleNames()[0]  == 'admin') ? '%' : Auth::user()->getRoleNames()[0];
        $currentYear    = date('Y');

        // var_dump(Auth::user()->getRoleNames()[0] != 'admin');die();

        if(Auth::user()->getRoleNames()[0] != 'admin') {
            $querySubDivision   = DB::select(
                "
                SELECT 	a.user_id,
                        a.name as user_name,
                        LOWER(c.name) as sub_division_name
                FROM 	employees a
                JOIN 	job_employees b ON b.employee_id = a.id
                JOIN 	sub_divisions c ON b.sub_division_id = c.id
                WHERE 	a.user_id = '$userID'
                "
            );
    
            if(!empty($querySubDivision)) {
                // CHECK IF SUB DIVISION IS PIC / MANAGER
                if($querySubDivision[0]->sub_division_name == 'pic' || $querySubDivision[0]->sub_division_name == 'manager') {
                    $userID     = '%';
                } else {
                    $userID     = Auth::user()->id;
                }
            }
        } else {
            $userID         = '%';
        }

        $query  = DB::select(
            "
            SELECT 	SUM(total_proker_tahunan) as grand_total_proker_tahunan,
                    SUM(total_proker_bulanan) as grand_total_proker_bulanan,
                    SUM(total_proker_harian) as grand_total_proker_harian,
                    tahun,
                    GROUP_CONCAT(distinct d.name SEPARATOR ', ') as role_name
            FROM 	(
                    SELECT 	COUNT(a.id) as total_proker_tahunan,
                            0 as total_proker_bulanan,
                            0 as total_proker_harian,
                            pkt_year as tahun,
                            a.created_by,
                            b.roles_id
                    FROM 	proker_tahunan a
                    JOIN	group_divisions b ON a.division_group_id = b.id
                    GROUP BY pkt_year, a.created_by, b.roles_id

                    UNION

                    SELECT 	0 as total_proker_tahunan,
                            COUNT(a.id)as total_proker_bulanan,
                            0 as total_proker_harian,
                            EXTRACT(YEAR FROM a.pkb_start_date) as tahun,
                            a.created_by,
                            c.id as role_id
                    FROM 	proker_bulanan a
                    JOIN 	model_has_roles b ON b.model_id = a.created_by
                    JOIN 	roles c ON c.id = b.role_id
                    GROUP BY EXTRACT(YEAR FROM a.pkb_start_date), a.created_by, c.id

                    UNION

                    SELECT 	0 as total_proker_tahunan,
                            0 as total_proker_bulanan,
                            COUNT(a.id) as total_proker_harian,
                            EXTRACT(YEAR FROM a.pkh_date) as tahun,
                            a.created_by,
                            c.id as role_id
                    FROM 	proker_harian a
                    JOIN 	model_has_roles b ON b.model_id = a.created_by
                    JOIN 	roles c ON c.id = b.role_id
                    GROUP BY EXTRACT(YEAR FROM a.pkh_date), a.created_by, c.id
            ) AS b
            JOIN 	roles d ON (d.id = b.roles_id)
            WHERE 	d.name LIKE '$roleName'
            AND 	b.tahun = '$currentYear'
            AND     b.created_by LIKE '$userID'
            GROUP BY b.tahun
            "
        );

        return $query;
    }

    // 21 JUNI 2024
    // NOTE : PEMBUATAN FUNGIS AMBIL DATA PROGRAM KERJA TAHUNAN BY GROUP DIVISION ID
    public static function doGetProgramKerjaTahunan($groupDivisionID)
    {
        $query  = DB::select(
            "
            SELECT 	a.uid as pkt_id,
                    a.pkt_title as pkt_title
            FROM 	proker_tahunan a
            WHERE 	a.division_group_id = '$groupDivisionID'
            AND 	a.pkt_year = EXTRACT(YEAR FROM CURRENT_DATE)
            ORDER BY created_at ASC
            "
        );
        
        return $query;
    }

    public static function doGetProgramKerjaBulanan($programKerjaTahunanID)
    {
        $query  = DB::select(
            "
            SELECT 	uuid as pkb_id,
                    pkb_title,
                    pkb_start_date as pkb_date
            FROM 	proker_bulanan
            WHERE 	EXTRACT(MONTH FROM pkb_start_date) = EXTRACT(MONTH FROM CURRENT_DATE)
            AND 	SUBSTRING_INDEX(pkb_pkt_id,' | ',1) = '$programKerjaTahunanID'
            ORDER BY created_by ASC
            "
        );
        
        return $query;
    }

    // 26 JUNI 2024
    // NOTE : PENGAMBILAN DATA UNTUK SELECT JADWAL FORM
    public static function getListSelectJadwalUmrahForm()
    {
        return DB::select(
            "
            SELECT 	b.jdw_uuid as prog_jdw_id,
                    c.name as prog_jdw_name,
                    b.jdw_depature_date as prog_jdw_depature_date,
                    b.jdw_arrival_date as prog_jdw_arrival_date,
                    b.jdw_mentor_name as prog_jdw_mentor_name
            FROM 	tr_prog_jdw a
            JOIN 	programs_jadwal b ON a.prog_jdw_id = b.jdw_uuid
            JOIN 	programs c ON b.jdw_programs_id = c.id
            WHERE 	EXTRACT(YEAR FROM b.jdw_depature_date) = EXTRACT(YEAR FROM CURRENT_DATE) 
            GROUP BY b.jdw_uuid, c.name, b.jdw_depature_date, b.jdw_arrival_date, b.jdw_mentor_name
            ORDER BY LEFT(c.name, 1), b.jdw_depature_date ASC
            "
        );
    }

    public static function getListSelectedJadwalUmrahForm($id)
    {
        // GET CURRENT SUB DIVISION
        $current_id     = Auth::user()->id;
        $current_role   = Auth::user()->getRoleNames()->first();

        $query_get_sub_division     = DB::select(
            "
            SELECT 	e.name as role_name,
                    c.id as group_division_id,
                    c.name as group_division_name,
                    d.id as sub_division_id,
                    d.name as sub_division_name
            FROM 	employees a
            JOIN 	job_employees b ON a.id = b.employee_id
            JOIN 	group_divisions c ON c.id = b.group_division_id
            JOIN 	sub_divisions d ON d.id = b.sub_division_id
            JOIN 	roles e ON c.roles_id = e.id
            WHERE 	a.user_id = '$current_id'
            AND 	e.name = '$current_role'
            "
        );

        if(!empty($query_get_sub_division)) {
            $sub_division   = strtolower($query_get_sub_division[0]->sub_division_name) == 'pic' ? '%' : strtolower($query_get_sub_division[0]->sub_division_name);
        } else {
            $sub_division   = $current_role == 'admin' ? '%' : '';
        }

        $query_get_selected_jadwal  = DB::select(
            "
            SELECT 	DISTINCT a.prog_jdw_id,
                    b.id as prog_jdw_seq,
                    b.rul_title as prog_jdw_title,
                    SUBSTRING_INDEX(b.rul_pkt_id, ' | ', 1) as prog_pkt_id,
                    SUBSTRING_INDEX(b.rul_pkt_id, ' | ', -1) as prog_pkt_seq,
                    c.id as prog_sd_id,
                    c.name as prog_sd_name
            FROM 	tr_prog_jdw a
            JOIN 	programs_jadwal_rules b ON a.prog_rul_id = b.id
            JOIN 	sub_divisions c ON b.rul_pic_sdid = c.id
            WHERE 	a.prog_jdw_id = '$id'
            AND 	LOWER(c.name) LIKE '$sub_division'
            ORDER BY b.id ASC
            "
        );

        return $query_get_selected_jadwal;
    }

    // 07 JULI 2024
    // NOTE : FUNGSI HAPUS DATA PRORAM KERJA HARIAN
    public static function doHapusDataHarian($id, $ip)
    {
        DB::beginTransaction();
        $prokerHarianID     = $id;
        $ip                 = $ip;

        $data_where         = array(
            "uuid"          => $prokerHarianID,
        );

        $data_update        = array(
            "pkh_is_active" => "f",
            "updated_by"    => Auth::user()->id,
            "updated_at"    => date('Y-m-d H:i:s'),
        );

        DB::table('proker_harian')->where($data_where)->update($data_update);

        try {
            DB::commit();
            $output     = array(
                "status"    => "success",
                "errMsg"    => [],
            );
            LogHelper::create("delete", "Berhasil Menghapus Data Harian dengan ID : ".$prokerHarianID, $ip);
        } catch(\Exception $e) {
            DB::rollBack();
            $output     = array(
                "status"    => "error",
                "errMsg"    => $e->getMessage(),
            );
            LogHelper::create("error_system", "Gagal Mengapus Data Harian", $ip);
        }

        return $output;

        // try {
        //     DB::commit();
        //     $output     = array(
        //         'transStatus'   => 'berhasil',
        //         'errMsg'        => '',
        //     );
        //     if($jenis == 'add') {
        //         $message    = "Berhasil Menambahkan Program Kerja Tahunan";
        //     } else {
        //         $message    = "Berhasil Mengubah Program Kerja Tahunan";
        //     }
        //     LogHelper::create($jenis, $message, $ip);
        // } catch(\Exception $e) {
        //     DB::rollback();
        //     // Log::channel('daily')->error($e->getMessage());
        //     $output     = array(
        //         'transStatus'   => 'gagal',
        //         'errMsg'        => $e->getMessage(),
        //     );
        //     LogHelper::create("error_system", $e->getMessage(), $ip);
        // }
    }

    // 08 JULI 2024
    // NOTE : GET DATA LIST USER BY GROUP DIVISION
    public static function doGetDataTableListUser($groupDivisionID)
    {
        return DB::select(
            "
            SELECT 	b.user_id,
                    b.name
            FROM 	job_employees a
            JOIN 	employees b ON a.employee_id = b.id
            WHERE 	a.group_division_id LIKE '$groupDivisionID'
            ORDER BY b.user_id ASC
            "
        );
    }
}