<?php

namespace App\Services;

use App\Helpers\LogHelper;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Response;
use Str;

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

    // 04 FEBRUARI 2025
    // NOTE : AMBIL LIST ARTIKEL
    public static function get_article_list($data)
    {
        $user_name  = $data['username'];
        $ip_address = $data['ip_address'];
        
        DB::beginTransaction();

        // GET DATA
        $query  = DB::table('programs_article as a')
                        ->join('programs_jadwal as b', 'a.jdw_tour_code', '=', 'b.jdw_tour_code')
                        ->join('programs as c', 'b.jdw_programs_id', '=', 'c.id')
                        ->select('a.*', 'c.name as jdw_program_name')
                        ->orderBy('a.created_at', 'asc')
                        ->get();

        try {
            DB::commit();
            LogHelper::create('search_data', 'Berhasil Mengambil Data Artikel Sebanyak '. count($query) . ' Data', $ip_address);
            $output     = [
                "status"    => "berhasil",
                "data"      => $query,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('daily')->error($e->getMessage());
            LogHelper::create('error_system', 'Koneksi Bermasalah', $ip_address);

            $output     = [
                "status"    => "gagal",
                "data"      => [],
            ];
        }

        return $output;
    }

    public static function get_article_tour_detail($data)
    {
        $ip_address = $data['ip_address'];
        $tour_code  = $data['tour_code'];
        $current_date   = new DateTime();

        $query      = DB::table('programs_jadwal as a')
                        ->join('programs as b', 'a.jdw_programs_id', '=', 'b.id')
                        ->join('products as c', 'b.product_id', '=', 'c.id')
                        ->select(   'a.jdw_tour_code as tour_code', 
                                    'c.name as product_name', 
                                    'b.name as program_name', 
                                    'a.jdw_depature_date as depature_date', 
                                    'a.jdw_arrival_date as arrival_date', 
                                    'a.jdw_mentor_name as mentor_name', 
                                    'a.jdw_itinerary as itinerary', 
                                    'a.jdw_flyer as flyer', 
                                    'a.jdw_airline as airlines_name', 
                                    'a.jdw_flight_code as airlines_code', 
                                    'a.jdw_cost_double as cost_double', 
                                    'a.jdw_cost_triple as cost_tiple', 
                                    'a.jdw_cost_quad as cost_quad',
                                    'a.jdw_destination as destination',
                                    'a.jdw_duration as duration',
                                    'a.jdw_hotel as hotel_name'
                                )
                        ->where('a.jdw_depature_date', '>=', $current_date->modify('+1 month'))
                        ->where('a.jdw_tour_code', '=', $tour_code)
                        ->get();
        
        try {
            $output     = [
                'status'    => 'berhasil',
                'message'   => 'Berhasil Mengambil Data Tour Code : ' . $tour_code,
                'data'      => $query,
            ];
            LogHelper::create('search_data', $output['message'], $ip_address);
        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            LogHelper::create('error_system', 'Gagal Mengambil Data Tour Code : ' . $tour_code, $ip_address);

            $output     = [
                'status'    => 'gagal',
                'message'   => $e->getMessage(),
                'data'      => [],
            ];
        }

        return $output;
    }

    // 06 FEBRUARI 2025
    // NOTE : SIMPAN ARTICLE
    public static function do_save_article_umrah($data)
    {
        $type           = $data['type'];
        $ip_address     = $data['ip_address'];
        $user_id        = $data['user_id'];
        
        $article_data   = $data['data'];

        $today          = date('Y-m-d H:i:s');

        DB::beginTransaction();

        // UPDATE PROGRAMS_JADWAL
        $data_where_programs    = [
            'jdw_tour_code'     => $article_data['jdw_tour_code'],
        ];

        $data_update_programs   = [
            'jdw_airline'       => $article_data['jdw_airline'],
            'jdw_cost_double'   => $article_data['jdw_cost_double'],
            'jdw_cost_triple'   => $article_data['jdw_cost_triple'],
            'jdw_cost_quad'     => $article_data['jdw_cost_quad'],
            'jdw_destination'   => $article_data['jdw_destination'],
            'jdw_duration'      => $article_data['jdw_duration'],
            'jdw_hotel'         => $article_data['jdw_hotel'],
            'jdw_flyer'         => $article_data['jdw_flyer'],
        ];

        DB::table('programs_jadwal')->where($data_where_programs)->update($data_update_programs);

        if($type == 'add') {
            // INSERT TO PROGRAMS ARTICLE
            $data_insert_article    = [
                'jdw_uuid'      => Str::uuid(),
                'jdw_title_name'=> $article_data['jdw_article_title'],
                'jdw_tour_code' => $article_data['jdw_tour_code'],
                'jdw_title_slug'=> str_replace(' ', '-', strtolower($article_data['jdw_article_title'])),
                'created_by'    => $user_id,
                'created_at'    => $today,
                'updated_by'    => $user_id,
                'updated_at'    => $today,
            ];
            
            DB::table('programs_article')->insert($data_insert_article);
            
            try {
                DB::commit();

                $output     = [
                    'status'    => 'berhasil',
                    'message'   => 'Berhasil Menambahkan Artikel',
                    'err_msg'   => [],
                ];

                LogHelper::create('add', 'Berhasil Menambahkan Article Baru ID : ' . $data_insert_article['jdw_uuid'], $ip_address);

            } catch (\Exception $e) {
                DB::rollBack();

                $output     = [
                    'status'    => 'gagal',
                    'message'   => 'Gagal Menambahkan Artikel',
                    'err_msg'   => $e->getMessage(),
                ];

                Log::channel('daily')->error($e->getMessage());
                LogHelper::create('error_system', $output['message'], $ip_address);
                
            }
        } else if($type == 'edit') {
            // UPDATE PROGRAMS ARTICLE
            $data_where_article     = [
                'jdw_uuid'      => $article_data['jdw_article_uuid'],
                'jdw_tour_code' => $article_data['jdw_tour_code'],
            ];

            $data_update_article    = [
                'jdw_title_name'    => $article_data['jdw_article_title'],
                'jdw_title_slug'    => str_replace(' ','-', strtolower($article_data['jdw_article_title'])),
                'jdw_status_upload' => 'pending',
                'updated_by'        => $user_id,
                'updated_at'        => $today,
            ];

            DB::table('programs_article')->where($data_where_article)->update($data_update_article);

            try {
                DB::commit();

                $output     = [
                    'status'    => 'berhasil',
                    'message'   => 'Berhasil Update Artikel',
                    'err_msg'   => ''
                ];

                LogHelper::create('edit', 'Berhasil Update Artikel : ' . $article_data['jdw_article_uuid'], $ip_address);

            } catch (\Exception $e) {
                DB::rollBack();
                
                $output     = [
                    'status'    => 'gagal',
                    'message'   => 'Gagal Update Artikel',
                    'err_msg'   => $e->getMessage(),
                ];
                
                Log::channel('daily')->error($e->getMessage());
                LogHelper::create('error_system', 'Gagal Update Artikel : ' . $article_data['jdw_article_uuid'], $ip_address);
            }
        } else if($type == 'approve') {
            $data_where     = [
                'jdw_uuid'      => $article_data['jdw_article_uuid'],
            ];

            $data_update    = [
                'jdw_status_upload' => 'approve',
                'updated_by'        => $user_id,
                'updated_at'        => $today,
            ];

            DB::table('programs_article')
                ->where($data_where)
                ->update($data_update);

            try {
                DB::commit();
                
                LogHelper::create('edit', 'Berhasil Aprove Artikel ' . $article_data['jdw_article_uuid'], $ip_address);

                $output      = [
                    'status'    => 'berhasil',
                    'message'   => 'Berhasil Approve Artikel',
                    'data'      => '',
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                Log::channel('daily')->error($e->getMessage());
                LogHelper::create('error_system', 'Gagal Approve Artikel ' . $article_data['jdw_article_uuid'], $ip_address);

                $output     = [
                    'status'    => 'gagal',
                    'message'   => 'Gagal Approve Artikel',
                    'data'      => $e->getMessage(),
                ];
            }
        }

        return $output;
    }

    // 07 FEBRUARI 2025
    // NOTE : AMBIL ARTIKEL DETAIL
    public static function get_article_detail($data)
    {
        $ip_address     = $data['ip_address'];
        $article_uuid   = $data['article_uuid'];

        // GET DATA
        $query          = DB::table('programs_article as a')
                            ->join('programs_jadwal as b', 'a.jdw_tour_code', '=', 'b.jdw_tour_code')
                            ->join('programs as c', 'b.jdw_programs_id', '=', 'c.id')
                            ->join('products as d', 'c.product_id','=','d.id')
                            ->select(
                                    'a.jdw_uuid as article_id',
                                    'a.jdw_title_name as article_title',
                                    'a.jdw_status_upload as article_status_upload',
                                    'b.jdw_tour_code as tour_code',
                                    'c.name as program_name',
                                    'd.name as product_name',
                                    'b.jdw_depature_date as depature_date',
                                    'b.jdw_arrival_date as arrival_date',
                                    'b.jdw_destination as destination',
                                    'b.jdw_duration as duration',
                                    'b.jdw_airline as airline_name',
                                    DB::raw("SUBSTRING_INDEX(b.jdw_hotel, ' | ', 1) as hotel_mekkah"),
                                    DB::raw("SUBSTRING_INDEX(b.jdw_hotel, ' | ', -1) as hotel_madinah"),
                                    'b.jdw_cost_quad as cost_quad',
                                    'b.jdw_cost_triple as cost_triple',
                                    'b.jdw_cost_double as cost_double',
                                    'b.jdw_flyer as flyer',
                                    'b.jdw_itinerary as itinerary'
                                    )
                            ->where('a.jdw_uuid', '=', $article_uuid)
                            ->where('a.jdw_status_upload', '=', 'pending')
                            ->get();
        
        try {
            $output     = [
                'status'    => 'berhasil',
                'message'   => 'Berhasil Mencari Data Artikel UUID : ' . $article_uuid,
                'data'      => $query,
            ];

            LogHelper::create('search_data', $output['message'], $ip_address);
        } catch (\Exception $e) {
            $output     = [
                'status'    => 'gagal',
                'message'   => 'Gagal Mencari Data Artikel UUID : ' . $article_uuid,
                'data'      => [],
            ];
            
            Log::channel('daily')->error($e->getMessage());
            LogHelper::create('error_system', 'Gagal Mencari Data Artikel UUID : ' . $article_uuid, $ip_address);
        }

        return $output;
    }

    // 11 MARET 2025
    // NOTE : AMBIL DATA ASSET
    public static function get_data_asset_umrah($tour_code)
    {
        // GET HEADER
        $query_header   = DB::table('programs_jadwal')
                            ->select('jdw_depature_date', 'jdw_arrival_date', 'jdw_mentor_name')
                            ->where('jdw_tour_code', '=', $tour_code)
                            ->get();

        $query_detail   = DB::table('programs_jadwal_file')
                            ->where('jdw_det_tour_code', '=', $tour_code)
                            ->get();

        try {
            $output     = [
                'is_success'    => true,
                'status_code'   => 200,
                'message'       => 'Berhasil Mengambil Data Asset Tour Code : ' . $tour_code,
                'data'          => [
                    'header'    => $query_header,
                    'detail'    => $query_detail,
                ] 
            ];
        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            
            $output     = [
                'is_success'    => false,
                'status_code'   => 500,
                'message'       => 'Internal Server Error',
                'data'          => []
            ];
        }

        return $output;
    }
    
    // SIMPAN DATA ASSET
    public static function do_simpan_data_asset_umrah($data)
    {
        $user_id        = $data['user_id'];
        $today          = date('Y-m-d H:i:s');
        $ip_address     = $data['ip_address'];
        $tour_code      = $data['tour_code'];
        $detail_asset   = $data['detail'];

        DB::beginTransaction();

        // DELETE DATA SEBELUMNYA
        DB::table('programs_jadwal_file')->where('jdw_det_tour_code', '=', $tour_code)->delete();
        
        // INSERT DATA BARU
        for($i = 0; $i < count($detail_asset); $i++) {
            $data_insert    = [
                'jdw_det_tour_code'     => $tour_code,
                'jdw_det_seq'           => $detail_asset[$i]['asd_seq'],
                'jdw_det_description'   => $detail_asset[$i]['asd_jenis'],
                'jdw_det_link'          => $detail_asset[$i]['asd_url'],
                'created_by'            => $user_id,
                'created_date'          => $today,
            ];

            DB::table('programs_jadwal_file')->insert($data_insert);
        }

        try {
            DB::commit();

            $output     = [
                'is_success'    => true,
                'status_code'   => 201,
                'message'       => 'Berhasil Menambahkan Data Asset',
                'data'          => []
            ];

            LogHelper::create('add', $output['message'] . " tour code : ". $tour_code, $ip_address);
        } catch (\Exception $e) {
            DB::rollback();
            $output     = [
                'is_success'    => false,
                'status_code'   => 500,
                'message'       => 'Internal Server Error',
                'data'          => []
            ];

            Log::channel('daily')->error($e->getMessage());
            LogHelper::create('error_system', $output['message'], $ip_address);
        }

        return $output;
    }
}

?>