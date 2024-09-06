<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use Log;

date_default_timezone_set('Asia/Jakarta');

class TarikDataService {
    
    public static function get_tarik_data_presensi($today)
    {
        DB::beginTransaction();
        // GET DATA FROM PRESENSI
        $query = DB::connection('presensi_percik')
            ->select("
                SELECT  b.nik,
                        b.name,
                        b.erp_id,
                        a.*
                FROM    time_attendance a
                JOIN 	user b ON a.nik = b.nik
                WHERE   a.attendance_date = '$today'
            ");

        $temp_data_presensi     = [];
        $jml_sukses             = 0;
        $jml_gagal              = 0;

        if(count($query))
        {
            for($i = 0; $i < count($query); $i++)
            {
                $temp_data_presensi[]    = [
                    "prs_date"          => $query[$i]->attendance_date,
                    "prs_user_id"       => $query[$i]->erp_id,
                    "prs_in_time"       => $query[$i]->clock_in,
                    "prs_in_file"       => null,
                    "prs_in_location"   => $query[$i]->clock_in_latitude.", ".$query[$i]->clock_in_longitude,
                    "prs_out_time"      => $query[$i]->clock_out == "0000-00-00 00:00:00" ? null : $query[$i]->clock_out,
                    "prs_out_file"      => null,
                    "prs_out_location"  => $query[$i]->clock_out == "0000-00-00 00:00:00" ? null : $query[$i]->clock_out_latitude.", ".$query[$i]->clock_out_longitude,
                    "created_by"        => $query[$i]->erp_id,
                    "updated_by"        => $query[$i]->erp_id,
                    "created_at"        => $query[$i]->clock_in,
                    "updated_at"        => $query[$i]->clock_out == "0000-00-00 00:00:00" ? $query[$i]->clock_in : $query[$i]->clock_out,
                ];
            }

            // INSERT KE TM PRESENCE
            for($j = 0; $j < count($temp_data_presensi); $j++)
            {
                $p_data     = $temp_data_presensi[$j];
                // CHECK DULU
                $prs_user_id    = $p_data['prs_user_id'];
                
                $q_c_tm_presence    = DB::connection('mysql')->select(
                    "
                    SELECT  *
                    FROM    tm_presence
                    WHERE   prs_date = '$today'
                    AND     prs_user_id = '$prs_user_id'
                    "
                );

                // var_dump(count($q_c_tm_presence) == 0, $prs_user_id);die();

                if(count($q_c_tm_presence) == 0) {
                    // INSERT TO TM PRESENCE
                    $data_insert    = [
                        "prs_date"          => $p_data['prs_date'],
                        "prs_user_id"       => $p_data['prs_user_id'],
                        "prs_in_time"       => $p_data['prs_in_time'],
                        "prs_in_file"       => $p_data['prs_in_file'],
                        "prs_in_location"   => $p_data['prs_in_location'],
                        "prs_out_time"      => $p_data['prs_out_time'],
                        "prs_out_file"      => $p_data['prs_out_file'],
                        "prs_out_location"  => $p_data['prs_out_location'],
                        "created_by"        => $p_data['created_by'],
                        "updated_by"        => $p_data['updated_by'],
                        "created_at"        => $p_data['created_at'],
                        "updated_at"        => $p_data['updated_at'],
                    ];
                    
                    DB::connection('mysql')->table('tm_presence')->insert($data_insert);
                    $jml_sukses++;
                } else {
                    $data_where         = [
                        "prs_date"          => $p_data['prs_date'],
                        "prs_user_id"       => $p_data['prs_user_id'],
                    ];

                    $data_update        = [
                        "prs_in_time"       => $p_data['prs_in_time'],
                        "prs_in_location"   => $p_data['prs_in_location'],
                        "prs_out_time"      => $p_data['prs_out_time'],
                        "prs_out_location"  => $p_data['prs_out_location'],
                    ];

                    DB::connection('mysql')->table('tm_presence')->where($data_where)->update($data_update);
                    $jml_sukses++;
                }
            }

            try {
                DB::commit();
                $output     = [
                    "message"   => "Berhasil Migrasi Data Absensi sebanyak ".$jml_sukses,
                ];
            } catch(\Exception $e) {
                DB::rollBack();
                $output     = [
                    "message"   => $e->getMessage(),
                ];
            }
        } else {
            DB::rollBack();
            $output     = [
                "message"   => "Tidak Ada yang bisa di migrasi"
            ];
        }

        return $output;
    }
    
}

?>