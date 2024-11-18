<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use Log;
use Str;

date_default_timezone_set('Asia/Jakarta');

class TarikDataService {
    
    public static function get_data_absensi($data)
    {
        $tgl_cari   = $data['tgl_cari'];

        $query      = DB::connection('presensi_percik')
                        ->table('time_attendance as a')
                        ->join('user as b', 'a.nik', '=', 'b.nik')
                        ->select('a.*', 'b.nik', 'b.name', 'b.erp_id')
                        ->where('a.attendance_date', '=', $tgl_cari)
                        ->get();

        return $query;
    }

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

        if(count($query) > 0)
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

    // 18 NOVEMBER 2024
    // NOTE : SYNC DATA UMHAJ KE LOCAL
    public static function do_sync_jadwal_umrah_local($data)
    {
        DB::beginTransaction();
        // EXTRACT
        $umhaj_data     = $data['umhaj_data'];
        $tahun          = $data['tahun'];
        
        // CONTAINER FILTERED UMHAJ DATA
        $temp_data_umhaj= [];

        // LOOP DATA UMHAJ
        for($i = 0; $i < count($umhaj_data); $i++)
        {
            // EXTRACT
            $umhaj_tour_code    = $umhaj_data[$i]['UMRAH_TOUR_CODE'];
            $umhaj_depature_date= date('Y-m-d', strtotime($umhaj_data[$i]['UMRAH_DEPATURE']));
            $umhaj_arrival_date = date('Y-m-d', strtotime($umhaj_data[$i]['UMRAH_ARRIVAL']));
            $umhaj_tour_leader  = $umhaj_data[$i]['UMRAH_MENTOR_NAME'];

            // CHECK DI LOCAL ADA ATAU TIDAK
            $check              = DB::table('programs_jadwal')->where('jdw_tour_code', '=', $umhaj_tour_code)->get();
            
            if(count($check) == 0) {
                $temp_data_umhaj[]  = [
                    "tour_code"         => $umhaj_tour_code,
                    "depature_date"     => $umhaj_depature_date,
                    "arrival_date"      => $umhaj_arrival_date,
                    "tour_leader"       => $umhaj_tour_leader,
                ];
            }
        }
        
        // SIMPAN KE LOCAL
        for($i = 0; $i < count($temp_data_umhaj); $i++)
        {
            // GET DATA PPROGRAM
            $program_id     = DB::table('programs')->where('alias', '=', substr($temp_data_umhaj[$i]['tour_code'], 0, 2))->get();
            
            $data_simpan    = [
                "jdw_uuid"          => Str::uuid(),
                "jdw_programs_id"   => count($program_id) > 0 ? $program_id[0]->id : "-",
                "jdw_depature_date" => $temp_data_umhaj[$i]['depature_date'],
                "jdw_arrival_date"  => $temp_data_umhaj[$i]['arrival_date'],
                "jdw_mentor_name"   => $temp_data_umhaj[$i]['tour_leader'],
                "jdw_tour_code"     => $temp_data_umhaj[$i]['tour_code'],
                "is_generated"      => "f",
                "is_active"         => "t",
                "created_by"        => $data['user_id'],
                "created_at"        => date('Y-m-d'),
                "updated_by"        => $data['user_id'],
                "updated_at"        => date('Y-m-d'),
            ];

            DB::table('programs_jadwal')->insert($data_simpan);
        }
        
        try {
            DB::commit();
            LogHelper::create('add', 'Berhasil Menarik Data Jadwal Umrah Sebanyak : ' . count($temp_data_umhaj), $data['ip_address']);
            $output     = [
                "status"    => "berhasil",
                "errMsg"    => "",
                "count"     => count($temp_data_umhaj),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            LogHelper::create('error_system', 'Gagal Menarik Data Jadwal Umrah', $data['ip_address']);
            Log::channel('daily')->error($e->getMessage());
            
            $output     = [
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
                "count"     => 0,
            ];
        }

        return $output;
    }
}

?>