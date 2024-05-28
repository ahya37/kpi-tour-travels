<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Hash;

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
            GROUP BY uid, title, description, periode, created_at
            ORDER BY pt.created_at DESC
            "
        );

        // $query  = DB::select(
        //     "
        //     SELECT 	pt.uid as uid,
        //             pt.pkt_title as title,
        //             pt.pkt_description as description,
        //             pt.pkt_year as periode,
        //             gd.name as division_group_name,
        //             (SELECT count(*) FROM proker_tahunan WHERE parent_id = pt.id) AS total_program
        //     FROM 	proker_tahunan pt
        //     JOIN group_divisions gd ON pt.division_group_id = gd.id
        //     WHERE 	pt.uid LIKE '".$uid."'
        //     AND 	pt.division_group_id LIKE '".$groupDivisionID."'
        //     AND 	pt.parent_id IS NULL
        //     ORDER BY pt.created_at DESC
        //     "
        // );

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

                // $data_sub   = array(
                //     "uid"                       => Str::uuid(),
                //     "parent_id"                 => $idHeader,
                //     "pkt_title"                 => $data['prtSub'][$i]['subProkTitle'],
                //     "pkt_year"                  => $data['prtPeriode'],
                //     "pkt_pic_job_employee_id"   => $data['prtPICEmployeeID'],
                //     "division_group_id"         => $data['prtGroupDivisionID'],
                //     "created_by"                => Auth::user()->id,
                //     "updated_by"                => Auth::user()->id,
                //     "created_at"                => date('Y-m-d H:i:s'),
                //     "updated_at"                => date('Y-m-d H:i:s'),
                // );

                DB::table('proker_tahunan_detail')->insert($data_sub);
                // DB::table('proker_tahunan')->insert($data_sub);
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
            // DB::table('proker_tahunan')
            //     ->where('parent_id', $query_get_data_id)
            //     ->delete();
            
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
                // $data_detail    = array(
                //     "uid"                       => Str::uuid(),
                //     "parent_id"                 => $query_get_data_id,
                //     "pkt_title"                 => $dataDetail[$j]['subProkTitle'],
                //     "pkt_year"                  => $data['prtPeriode'],
                //     "pkt_pic_job_employee_id"   => $data['prtPICEmployeeID'],
                //     "division_group_id"         => $data['prtGroupDivisionID'],
                //     "created_by"                => Auth::user()->id,
                //     "updated_by"                => Auth::user()->id,
                //     "created_at"                => date('Y-m-d H:i:s'),
                //     "updated_at"                => date('Y-m-d H:i:s'),
                // );

                DB::table('proker_tahunan_detail')->insert($data_detail);
                // DB::table('proker_tahunan')->insert($data_detail);
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
            WHERE 	a.uid = '".$id."'
            ORDER BY CAST(b.pktd_seq AS SIGNED) ASC
            "
        );

        // QUERY PENGAMBILAN DATA LAMA
        // $get_detail     = DB::select(
        //     "
        //     SELECT 	pt.pkt_title as detail_title
        //     FROM 	proker_tahunan pt
        //     WHERE 	pt.parent_id = (SELECT id FROM proker_tahunan WHERE uid = 'cee5315b-9c3d-4e53-bf0f-3b7c24b55e08')
        //     ORDER BY pt.id ASC
        //     "
        // );

        $output     = array(
            "header"    => !empty($get_header) ? $get_header : null,
            "detail"    => !empty($get_detail) ? $get_detail : [],
        );
        return $output;
    }

    // BULANAN
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
}