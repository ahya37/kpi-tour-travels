<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Hash;
use Illuminate\Support\Facades\Log;

date_default_timezone_set('Asia/Jakarta');

class ProgramKerjaService
{
    public static function getDataProkerTahunan($data)
    {
        $uid                = $data['uid'];
        $groupDivisionID    = $data['groupDivisionID'];
        $query  = DB::select(
            "
            SELECT  pt.uid as uid,
                    pt.pkt_title as title,
                    gd.name as division_group_name,
                    pt.pkt_description as description,
                    pt.pkt_year as periode,
                    pt.created_at,
                    count(*) as total_program
            FROM 	proker_tahunan pt
            INNER JOIN proker_tahunan_detail ptd ON pt.id = ptd.pkt_id
            INNER JOIN group_divisions gd ON pt.division_group_id = gd.id
            WHERE 	pt.uid LIKE '%".$uid."%'
            AND 	gd.id LIKE '".$groupDivisionID."'
            GROUP BY uid, pkt_title, pkt_description, pkt_year, name, created_at
            ORDER BY pt.created_at DESC
            "
        );

        return $query;
    }

    public static function doSimpanProkerTahunan($data, $jenis)
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
        } catch(\Exception $e) {
            DB::rollback();
            Log::channel('daily')->error($e->getMessage());
            $output     = array(
                'transStatus'   => 'gagal',
                'errMsg'        => $e->getMessage(),
            );
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
        $query_header  = DB::select(
            "
            SELECT 	a.uuid as pkb_uuid,
                    a.pkb_title,
                    a.pkb_description,
                    a.pkb_start_date,
                    SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) as pkb_pkt_id,
                    SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', -1) as pkb_pkt_id_seq,
                    d.id as pkb_gd_id,
                    d.name as pkb_gd_name,
                    e.id as pkb_sd_id,
                    e.name as pkb_sd_name,
                    a.pkb_employee_id
            FROM 	proker_bulanan a
            JOIN 	proker_tahunan b ON SUBSTRING_INDEX(a.pkb_pkt_id,' | ',1) = b.uid
            JOIN 	job_employees c ON b.pkt_pic_job_employee_id = c.employee_id
            JOIN 	group_divisions d ON c.group_division_id = d.id
            JOIN 	sub_divisions e ON c.sub_division_id = e.id
            WHERE 	a.uuid LIKE '%$cari%'
            ORDER BY a.pkb_start_date, a.created_at ASC
            "
        );

        if(($cari != '%') || (!empty($cari))) {
            $query_detail   = DB::select(
                "
                SELECT 	b.pkbd_type as jenis_pekerjaan,
                        b.pkbd_target as target_sasaran,
                        b.pkbd_result as hasil,
                        b.pkbd_evaluation as evaluasi,
                        b.pkbd_description as keterangan
                FROM 	proker_bulanan a
                JOIN 	proker_bulanan_detail b ON a.id = b.pkb_id
                WHERE 	a.uuid = '$cari'
                ORDER BY a.created_at, b.id ASC
                "
            );
        } else {
            $query_detail   = null;
        }
        
        $output     = array(
            "header"    => $query_header,
            "detail"    => $query_detail,
        );
        
        return $output;
    }

    public static function doGetProkerTahunan($data)
    {
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
            WHERE 	a.parent_id IS NULL
            AND 	a.uid LIKE '$prokerID'
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
        DB::beginTransaction();
        
        if($dataProkerBulanan['prokerBulanan_typeTrans'] == 'add') {
            // HEADER
            $data_insert    = array(
                "uuid"                  => Str::uuid(),
                "pkb_title"             => $dataProkerBulanan['prokerBulanan_title'],
                "pkb_start_date"        => date('Y-m-d', strtotime($dataProkerBulanan['prokerBulanan_startDate'])),
                "pkb_description"       => $dataProkerBulanan['prokerBulanan_description'],
                "pkb_pkt_id"            => !empty($dataProkerBulanan['prokerBulanan_subProkerTahunan']) ? $dataProkerBulanan['prokerBulanan_prokerTahunanID']." | ".$dataProkerBulanan['prokerBulanan_subProkerTahunan'] : $dataProkerBulanan['prokerBulanan_prokerTahunanID'],
                "pkb_employee_id"       => $dataProkerBulanan['prokerBulanan_employeeID'],
                "created_by"            => Auth::user()->id,
                "updated_by"            => Auth::user()->id,
                "created_at"            => date('Y-m-d H:i:s'),
                "updated_at"            => date('Y-m-d H:i:s'),
            );
            DB::table('proker_bulanan')
                ->insert($data_insert);

            $idHeader   = DB::getPdo()->lastInsertId();
            if(count($dataProkerBulanan['prokerBulanan_detail']) > 0) {
                for($i = 0; $i < count($dataProkerBulanan['prokerBulanan_detail']); $i++) {
                    $data_insert_detail     = array(
                        "pkb_id"            => $idHeader,
                        "pkbd_type"         => $dataProkerBulanan['prokerBulanan_detail'][$i]['jenisPekerjaan'],
                        "pkbd_target"       => $dataProkerBulanan['prokerBulanan_detail'][$i]['targetSasaran'],
                        "pkbd_result"       => $dataProkerBulanan['prokerBulanan_detail'][$i]['hasil'],
                        "pkbd_evaluation"   => $dataProkerBulanan['prokerBulanan_detail'][$i]['evaluasi'],
                        "pkbd_description"  => $dataProkerBulanan['prokerBulanan_detail'][$i]['keterangan'],
                    );
                    
                    if(!empty($data_insert_detail['pkbd_type'])) {
                        DB::table('proker_bulanan_detail')->insert($data_insert_detail);
                    }
                }
            } else {
                // DO NOTHING
            }
        } else if($dataProkerBulanan['prokerBulanan_typeTrans'] == 'edit') {
            // print("<pre>" .print_r($dataProkerBulanan, true). "</pre>");die();
            $data_header_where  = array(
                "uuid"      => $dataProkerBulanan['prokerBulanan_ID'],
            );

            $data_header_update = array(
                "pkb_pkt_id"            => $dataProkerBulanan['prokerBulanan_prokerTahunanID']." | ".$dataProkerBulanan['prokerBulanan_subProkerTahunan'],
                "pkb_title"             => $dataProkerBulanan['prokerBulanan_title'],
                "pkb_description"       => $dataProkerBulanan['prokerBulanan_description'],
                "pkb_employee_id"       => $dataProkerBulanan['prokerBulanan_employeeID'],
                "updated_by"            => Auth::user()->id,
                "updated_at"            => date('Y-m-d H:i:s'),
            );

            // UPDATE HEADER
            DB::table('proker_bulanan')
                ->where($data_header_where)
                ->update($data_header_update);
            
            // UPDATE DETAIL
            if(count($dataProkerBulanan['prokerBulanan_detail']) > 0) {
                if(!empty($dataProkerBulanan['prokerBulanan_detail'][0]['jenisPekerjaan'])) {
                    // DELETE DATA
                    $getIDProkerBulanan     = DB::table('proker_bulanan')
                                                    ->select('id')
                                                    ->where('uuid','=', $dataProkerBulanan['prokerBulanan_ID'])
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

                    for($i = 0; $i < count($dataProkerBulanan['prokerBulanan_detail']); $i++) {
                        $data_insert_detail     = array(
                            "pkb_id"            => $getIDProkerBulanan[0]->id,
                            "pkbd_type"         => $dataProkerBulanan['prokerBulanan_detail'][$i]['jenisPekerjaan'],
                            "pkbd_target"       => $dataProkerBulanan['prokerBulanan_detail'][$i]['targetSasaran'],
                            "pkbd_result"       => $dataProkerBulanan['prokerBulanan_detail'][$i]['hasil'],
                            "pkbd_evaluation"   => $dataProkerBulanan['prokerBulanan_detail'][$i]['evaluasi'],
                            "pkbd_description"  => $dataProkerBulanan['prokerBulanan_detail'][$i]['keterangan'],
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
        } catch(\Exception $e) {
            DB::rollback();
            Log::channel('daily')->error($e->getMessage());
            $output     = array(
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            );
        }

        return $output;
    }
}