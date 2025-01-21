<?php

namespace App\Services;

use App\Helpers\LogHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function Ramsey\Uuid\v1;

date_default_timezone_set('Asia/Jakarta');

class WebsiteService 
{
    public static function percikTours_data_summary()
    {
        $data   = [];
        // GET DATA SUMMARY
        $get_data     = DB::connection('web_percik')
                        ->table('pct_summary_data')
                        ->get();

        if(count($get_data) > 0) {
            $data_summary    = [
                "total_jemaah"      => $get_data[0]->total_jemaah,
                "total_perjalanan"  => $get_data[0]->total_perjalanan,
                "total_pembimbing"  => $get_data[0]->total_pembimbing,
                "total_agen"        => $get_data[0]->total_agen,
                "last_update"       => date('Y-m-d H:i:s'),
            ];
        } else {
            $data_summary    = [
                "total_jemaah"      => $get_data[0]->total_jemaah,
                "total_perjalanan"  => $get_data[0]->total_perjalanan,
                "total_pembimbing"  => $get_data[0]->total_pembimbing,
                "total_agen"        => $get_data[0]->total_agen,
                "last_update"       => date('Y-m-d H:i:s'),
            ];
        }
        // GET DATA PRODUK
        $product        = DB::connection('web_percik')
                            ->table('pct_master_category')
                            ->get();
        if(count($product) > 0) {
            foreach($product as $item) {
                $temp_product[]   = [
                    "product_id"    => $item->category_id,
                    "product_name"  => $item->category_name,
                ];
            }
            $data_product   = [
                "total_product" => count($product),
                "data"          => $temp_product,
            ];
        } else {
            $data_product   = [
                "total_product" => 0,
                "data"          => 0,
            ];
        }
        // GET DATA PROGRAM
        $program        = DB::connection('web_percik')
                            ->table('pct_master_program')
                            ->get();

        if(count($program) > 0) {
            foreach($program as $item) {
                $temp_program[] = [
                    "product_id"    => $item->category_id,
                    "program_id"    => $item->program_id,
                    "program_name"  => $item->program_name,
                ];
            }

            $data_program   = [
                "total_program"     => count($program),
                "data"              => $temp_program,
            ];
        } else {
            $data_program   = [
                "total_program"     => 0,
                "data"              => [],
            ];
        }
        
        $data   = [
            "data_summary"  => $data_summary,
            "data_product"  => $data_product,
            "data_program"  => $data_program,
        ];

        return $data;
    }

    // 17 JANUARI 2025
    // NOTE : MASTER DATA JADWAL
    public static function get_master_jadwal($year)
    {
        $query  = DB::table('programs_jadwal as a')
                    ->join('programs as b', 'a.jdw_programs_id', '=', 'b.id')
                    ->select('a.*', 'b.name as programs_name')
                    ->where(DB::raw('EXTRACT(YEAR FROM a.jdw_depature_date)'), '=', $year)
                    ->get();
        return $query;
    }

    // NOTE : SYNC MASTER DATA JADWAL
    public static function sync_master_jadwal($data)
    {
        DB::beginTransaction();
        $year       = $data['tahun'];
        $data_api   = $data['data'];
        $user_id    = $data['user_id'];
        $ip_address = $data['ip'];

        $query_data_local   = DB::table('programs_jadwal')
                                ->where(DB::raw('EXTRACT(YEAR FROM jdw_depature_date)'), '=', $year)
                                ->get();
        $data_local         = $query_data_local;

        foreach($data_local as $local) {
            $local_tour_code    = $local->jdw_tour_code;
            $local_tour_id      = $local->jdw_uuid;
            
            for($i = 0; $i < count($data_api); $i++)
            {
                $api_tour_code  = $data_api[$i]['UMRAH_TOUR_CODE'];
                $api_jml_seat   = $data_api[$i]['UMRAH_TOTAL_SEAT'];
                $api_take_seat  = $data_api[$i]['UMRAH_TAKEN_SEAT'];
                $api_avail_seat = $data_api[$i]['UMRAH_AVAILABLE_SEAT'];
                
                if($api_tour_code == $local_tour_code) {
                    // UPDATE YANG DI LOCAL

                    $data_where     = [
                        "jdw_uuid"      => $local_tour_id,
                        "jdw_tour_code" => $local_tour_code,
                    ];

                    $data_update    = [
                        "jdw_seat"              => $api_jml_seat,
                        "jdw_take_seat"         => $api_take_seat,
                        "jdw_available_seat"    => $api_avail_seat,
                        "updated_by"            => $user_id,
                        "updated_at"            => date('Y-m-d H:i:s'),
                    ];

                    DB::table('programs_jadwal')->where($data_where)->update($data_update);
                    break;
                }
            }
        }

        try {
            DB::commit();
            LogHelper::create('edit', 'Berhasil Memperbarui Data Jadwal Umrah', $ip_address);

            $output     = [
                "status"    => "berhasil",
                "message"   => "Berhasil Memperbarui Data Jadwal Umrah",
                "errMsg"    => "",
            ];
        } catch (\Exception $err) {
            DB::rollBack();
            LogHelper::create('error_system', 'Gagal Memperbarui Data Jadwal Umrah', $ip_address);
            Log::channel('daily')->error($err->getMessage());

            $output     = [
                "status"    => "gagal",
                "message"   => "Gagal Memperbarui Data Jadwal Umrah",
                "errMsg"    => $err->getMessage(),
            ];
        }

        return $output;
    }

    public static function get_jadwal_detail($tour_code)
    {
        $query = DB::table('programs_jadwal')
                    ->where('jdw_tour_code', '=', $tour_code)
                    ->get();
        return $query;
    }

    // 21 JANUARI 2025
    // NOTE : UPLOAD FILE
    public static function do_upload_flyer($file)
    {
        DB::beginTransaction();

        $ip     = $file['ip'];

        $data_where     = [
            'jdw_tour_code'     => $file['tour_code'],
        ];

        $data_update    = [
            "jdw_flyer"         => $file['storage_path'] . "/" . $file['custom_name'],
            "updated_by"        => $file['user_id'],
            "updated_at"        => date('Y-m-d H:i:s'),
        ];

        DB::table('programs_jadwal')->where($data_where)->update($data_update);

        try {
            DB::commit();

            LogHelper::create('edit', 'Berhasil Upload Flyer ' . $file['tour_code'], $ip);

            $output     = [
                "status"    => "berhasil",
                "message"   => "Berhasil Upload Flyer",
                "errMsg"    => "",
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::channel('daily')->error($e->getMessage());
            LogHelper::create('error_system', 'Gagal Upload Flyer', $ip);

            $output     = [
                'status'    => 'gagal',
                'message'   => 'Gagal Upload Flyer',
                'errMsg'    => $e->getMessage(),
            ];
        }

        return $output;
    }
}

?>