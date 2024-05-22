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
            SELECT 	a.*,
                    (SELECT COUNT(*) FROM proker_tahunan WHERE parent_id = a.id) as total_program
            FROM 	proker_tahunan a
            WHERE 	a.parent_id IS NULL
            AND 	a.uid LIKE '%".$id."%'
            ORDER BY a.id ASC
            "
        );

        return $query;
    }

    public static function doSimpanProkerTahunan($data)
    {
        DB::beginTransaction();

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
                "uid"           => Str::uuid(),
                "parent_id"     => $idHeader,
                "pkt_title"         => $data['prtSub'][$i]['subProkTitle'],
                "pkt_description"   => null,
                "pkt_year"          => $data['prtPeriode'],
                "pkt_pic_job_employee_id"   => $data['prtPICEmployeeID'],
                "division_group_id"         => $data['prtGroupDivisionID'],
                "created_by"        => Auth::user()->id,
                "updated_by"        => Auth::user()->id,
                "created_at"        => date('Y-m-d H:i:s'),
                "updated_at"        => date('Y-m-d H:i:s'),
            );

            DB::table('proker_tahunan')->insert($data_sub);
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
}