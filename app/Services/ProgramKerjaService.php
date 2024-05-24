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
    public static function getDataProkerTahunan($id)
    {
        $query  = DB::select(
            "
            SELECT 	pt.uid as uid,
                    pt.pkt_title as title,
                    pt.pkt_description as description,
                    pt.pkt_year as periode,
                    count(*) as total_program
            FROM 	proker_tahunan pt
            INNER JOIN proker_tahunan_detail ptd ON pt.id = ptd.pkt_id
            WHERE 	pt.uid LIKE '%".$id."%'
            GROUP BY uid, title, description, periode
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
                "pkt_title"         => $data['prtTitle'],
                "pkt_description"   => $data['prtDescription'],
                "pkt_pic_job_employee_id"   => $data['prtPICEmployeeID'],
                "division_group_id"         => $data['prtGroupDivisionID'],
                "updated_by"        => Auth::user()->id,
                "updated_at"        => date('Y-m-d H:i:s'),
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
        $get_detail     = DB::select(
            "
            SELECT 	b.pktd_seq as detail_seq,
                    b.pktd_title as detail_title
            FROM 	proker_tahunan a
            JOIN proker_tahunan_detail b ON a.id = b.pkt_id
            WHERE 	a.uid = '".$id."'
            ORDER BY b.pktd_seq ASC
            "
        );

        $output     = array(
            "header"    => !empty($get_header) ? $get_header : null,
            "detail"    => !empty($get_detail) ? $get_detail : [],
        );
        return $output;
    }
}